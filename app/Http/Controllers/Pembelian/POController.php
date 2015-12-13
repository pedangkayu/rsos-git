<?php

namespace App\Http\Controllers\Pembelian;

use App\Models\data_po;
use App\Models\data_prq;
use App\Models\data_sph;
use App\Models\data_vendor;
use App\Models\data_po_item;
use App\Models\data_prq_item;

use App\Jobs\Pembelian\PO\EditPOJob;
use App\Jobs\Pembelian\PO\CreatePOJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class POController extends Controller {
    
	public
		$mySession,
		$ids;

	public function __construct(){
		$format = '_itemPO' . \Auth::user()->id_user;
		$this->mySession = $format;

		if(!empty(session()->get($format))){
			foreach(session()->get($format) as $val){
				$this->ids[] = $val['id_prq_item'];
			}
		}
	}

    public function getIndex(){
    	$items = data_po::show([], [1,2])->paginate(10);

    	$status = [
    		1 => 'Baru',
    		2 => 'Proses',
    		3 => 'Selesai',
    		5 => 'Delete System',
    	];

    	return view('Pembelian.PO.Master', [
    		'items' => $items,
    		'status' => $status
    	]);
    }

    public function getAllpo(Request $req){
    	if($req->ajax()){
    		$res = [];
    		$out = '';

    		$stat = [
	    		1 => 'Baru',
	    		2 => 'Proses',
	    		3 => 'Selesai',
	    		5 => 'Delete System',
	    	];

    		$status = $req->status == 0 ? [1,2,3,5] : $req->status;
    		$items = data_po::show($req->all(), $status)->paginate($req->limit);

    		$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;

    		if($items->total() > 0):

	    		foreach($items as $item){

	    			if($item->id_sph == 0 && in_array($item->status, [0,1])){
	    				$link = '| <a href="' . url('/po/edit/' . $item->id_po) . '">Edit</a> | <a href="javascript:;" onclick="delpo(' . $item->id_po . ');" class="text-danger">Hapus</a>';
	    			}else if($item->status != 5 && $item->id_sph > 0){
	    				$link = '| <a href="' . url('/sph/editsphsystem/' . $item->id_sph) . '" target="_balnk">Edit SPH</a>';
	    			}else{
	    				$link = '';
	    			}

	    			$danger = $item->status == 5 ? '<i class="fa fa-warning pull-right text-danger"></i>' : '';
	    			$red = $item->status == 5 ? 'style=background:#ffcccc;' : '';

	    			$selisih = strtotime($item->deadline) > time() ? \Format::selisih_hari($item->deadline, date('Y-m-d')) . ' hari dari sekarang' : '';
	    			$css = strtotime($item->deadline) > strtotime(date('Y-m-d')) ? '' : 'text-danger semi-bold';
	    			$stat5 = $item->status == 5 ? 'semi-bold' : '';
	    			$out .= '
	    				<tr title="Dibuat oleh : ' . $item->nm_depan . ' ' . $item->nm_belakang . '" class="itempo-' . $item->id_po . '">
							<td class="' . $stat5 . '" ' . $red . '>' . $no . '</td>
							<td class="' . $stat5 . '" ' . $red . '>
								' . $item->no_po . ' ' . $danger . '
								<div class="link">
									<small>
										[
											<a href="' . url('/po/print/' . $item->id_po) . '" target="_blank">Print</a>
											' . $link . '
										]
									</small>
								</div>	
							</td>
							<td class="' . $stat5 . '" ' . $red . '>
								' . $item->nm_vendor . '
								<div><small class="text-muted">' . $item->telpon . '</small></div>
							</td>
							<td ' . $red . '>
								<span class="' . $css . '">' . \Format::indoDate($item->deadline) . '</span>
								<div><small class="text-muted">' . $selisih . '</small></div>
							</td>
							<td ' . $red . '>' . $stat[$item->status] . '</td>
						</tr>
	    			';
	    			$no++;
	    		}

    		else:

    			$out = '
    				<tr>
    					<td colspan="4">Tidak ditemukan</td>
    				</tr>
    			';

    		endif;

    		$res['pagin'] 	= $items->render();
    		$res['content'] = $out;

    		return json_encode($res);

    	}
    }

    public function getPrint($id){

    	$po 	= data_po::poprint($id)->first();
    	
    	if($po == null)
    		return redirect('/po')->withNotif([
    			'label' => 'warning',
    			'err'	=> 'PO tidak tersedia!'
    		]);

    	$items = data_po_item::join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
					->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
					->where('data_po_item.id_po', $id)
					->select(
						'data_barang.nm_barang', 
						'data_barang.id_barang', 
						'data_barang.kode', 
						'data_barang.in', 
						'data_barang.out', 
						'data_po_item.*', 
						'ref_satuan.nm_satuan'
					)->get();

		
		$vendor = data_vendor::find($po->id_vendor);
		
		/* Matematika */
		$subtotal 	= 0;
		foreach($items as $item){
			$subtotal += ( $item->harga - ($item->harga * $item->diskon / 100) ) * $item->req_qty;
		}

		$diskon 	= ($subtotal * $po->diskon) / 100;
		$aftdiskon	= $subtotal - $diskon;
		$ppn 		= ($aftdiskon * $po->ppn) / 100;
		$grandtotal = $aftdiskon + $ppn + $po->adjustment;

		$matematika = [
			'subtotal' 		=> $subtotal,
			'diskon'		=> $diskon,
			'aftdiskon'		=> $aftdiskon,
			'ppn'			=> $ppn,
			'grandtotal' 	=> $grandtotal
		];

		
    	return view('Print.Pembelian.PO.printPO', [
    		'items' 	=> $items,
			'po' 		=> $po,
			'vendor' 	=> $vendor,
			'mtk'		=> $matematika
    	]);

    }

    public function getEdit($id){

    	$p 	= data_po::whereId_po($id);

    	if($p->count() < 1)
    		return redirect('/po')->withNotif([
    			'label' => 'warning',
    			'err'	=> 'PO tidak tersedia!'
    		]);

    	$po = $p->first();
    	if($po->status > 1)
    		return redirect('/po')->withNotif([
    			'label' => 'warning',
    			'err'	=> 'PO tidak tersedia!'
    		]);

    	// Filter
    	if($po->id_sph > 0){
    		$sph = data_sph::find($po->id_sph);
    		return redirect('/po')->withNotif([
    			'label' => 'warning',
    			'err'	=> 'PO No. ' . $po->no_po . ' tidak dapat di perbaharui karena pembuatannya melalui proses Pengajuan Harga. Dengan No. ' . $sph->no_sph_item
    		]);
    	}
    	
    	$items = data_po_item::join('data_barang', 'data_barang.id_barang', '=', 'data_po_item.id_item')
					->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
					->where('data_po_item.id_po', $id)
					->select(
						'data_barang.nm_barang', 
						'data_barang.id_barang', 
						'data_barang.kode', 
						'data_barang.in', 
						'data_barang.out', 
						'data_po_item.*', 
						'ref_satuan.nm_satuan'
					)->get();


		$ids = [];
		foreach($items as $i){
			$ids[] = $i->id_barang;
		}

		$ids = json_encode($ids);

    	return view('Pembelian.PO.EditPO', [
    		'po' 		=> $po,
			'items' 	=> $items,
			'id'		=> $id,
			'no_sph'	=> 0,
			'ids'		=> $ids
    	]);

    }

    public function postEdit(Request $req){
    	$po = $this->dispatch(new EditPOJob($req->all()));
    	return redirect()->back()->withNotif([
    			'label' => 'success',
    			'err'	=> $po['err']
    		]);
    }

    public function postDelpo(Request $req){
    	if($req->ajax()){
    		$po = data_po::find($req->id);
    		$po->update([
    			'status' => 4
    		]);

    		\Loguser::create('Menghapus PO no. ' . $po->no_po);

    		return json_encode([
    			'id' => $req->id
    		]);
    	}
    }


    public function getSelect(){
    	if(session()->get('titipan') > 0){    		
    		session()->forget('titipan');
    		session()->forget($this->mySession);
    	}
    	return view('Pembelian.PO.Select');
    }

    public function getPrq(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$stat = [
				1 => 'Baru', 
				2 => 'Proses',
				3 => 'Selesai'
			];

			$status = $req->status == 0 ? [1,2] : $req->status;

			$prq = data_prq_item::forsph($req->all(), $status, $this->ids)->paginate($req->limit);
			$total = $prq->total();
			if($total > 0):				
				foreach ($prq as $item) {
					
					$st = $item->status > 1 ? 'Proses' : '';

					$when 	= time() > strtotime($item->target) ? '' : \Format::selisih_hari(date('Y-m-d'), $item->target) . ' hari dari sekarang';
					$danger = strtotime(date('Y-m-d')) > strtotime($item->target) ? 'text-danger semi-bold' : '';
					$add 	= $item->id_acc > 0 && $item->status < 2 ? '<button class="btn btn-white" onclick="add(' . $item->id_prq_item . ');"><i class="fa fa-plus"></i></button>' : '<div title="Belum Ter Verifikasi"><i class="fa fa-times text-danger"></i></div>';

					$out .= '
						<tr title="' . $item->nm_barang . '" class="item-prq-' . $item->id_prq_item . '">
							<td>
								' . \Format::substr($item->nm_barang,20) . '
								<div><small class="text-muted">' . $item->kode . '</small></div>
							</td>
							<td class="text-right">
								' . number_format($item->qty,0,',','.') . ' ' . $item->nm_satuan . '
								<div class="text-muted"><small>' . $st . '</small></div>
							</td>
							<td>
								' . $item->no_prq . '
								<div><small class="text-muted">' . \Format::indoDate($item->created_at) . '</small></div>
							</td>
							<td>
								<div class="' . $danger . '">' . \Format::indoDate($item->target) . '</div>
								<div><small class="text-muted">' . $when . '</small></div>
							</td>
							<td class="text-center">
								' . $add . '
							</td>
						</tr>
					';
					
				}
			else:
				$out = '<tr>
					<td colspan="5">Tidak ditemukan</td>
				</tr>';
			endif;

			$res['total'] 	= $total;
			$res['pagin'] 	= $prq->render();
			$res['content'] = $out;

			return json_encode($res);
		}
	}

	public function postAddprq(Request $req){
		if($req->ajax()){
			$item 	= [];

			try{
				
				if($req->session()->get('titipan') > 0 && $req->session()->get('titipan') != $req->titipan)
					throw new \Exception("Maaf barang titipan tidak bisa digabung dengan Supplier lain atau dengan barang milik Rumah Sakit!", 1);
					
				$req->session()->put('titipan', $req->titipan);
				$new 	= [
					[
						'id_prq_item' => $req->id
					]
				];

				$find = false;
				if(!empty($req->session()->get($this->mySession))){

					foreach($req->session()->get($this->mySession) as $val){
						if($val['id_prq_item'] == $req->id){
							$item[] = [
								'id_prq_item' => $val['id_prq_item']
							];
							$find = true;
						}else{
							$item[] = [
								'id_prq_item' => $val['id_prq_item']
							];
						}
					}

				}


				if($find){
					$value = $item;
				}else{
					$value = array_merge($new, $item);
				}

				$req->session()->put($this->mySession, $value);

				return json_encode([
					'status' => true,
					'id' => $req->id
				]);

			}catch(\Exception $e){

				return json_encode([
					'status' => false,
					'id' => $req->id,
					'err' => $e->getMessage()
				]);

			}

			

		}
	}

	public function getSelected(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '<table class="table table-bordered">';
			if(count($this->ids) > 0){
				$items = data_prq_item::join('data_prq', 'data_prq.id_prq', '=','data_prq_item.id_prq')
					->join('data_barang', 'data_barang.id_barang', '=', 'data_prq_item.id_barang')
					->whereIn('data_prq_item.id_prq_item', $this->ids)
					->select('data_prq.no_prq', 'data_barang.kode', 'data_prq_item.id_prq_item')
					->get();

				foreach($items as $item){
					$out .= '<tr class="prq_item_' . $item->id_prq_item . '">
						<td>
							<button type="button" class="close" onclick="delselected(' . $item->id_prq_item . ');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							' . $item->kode . '
							<div><small class="text-muted">' . $item->no_prq . '</small></div>
						</td>
					</tr>';
				}
			}else{
				$out .= '
					<tr>
						<td>Tidak ditemukan</td>
					</tr>
				';
			}

			$out .= '<table>';

			$res['total'] = count($this->ids);
			$res['content'] = $out;

			return json_encode($res);

		}
	}

	public function postDelselected(Request $req){
		if($req->ajax()){

			$item 	= [];

			if(!empty($req->session()->get($this->mySession))){
				foreach($req->session()->get($this->mySession) as $val){
					if($val['id_prq_item'] != $req->id){
						$item[] = [
							'id_prq_item' => $val['id_prq_item']
						];
					}
				}
			}

			$req->session()->put($this->mySession, $item);
			return json_encode([
				'id' => $req->id
			]);

		}
	}

	public function postDellall(Request $req){
		if($req->ajax()){
			$req->session()->forget($this->mySession);
			$req->session()->forget('titipan');
			return json_encode([
				'result' => true
			]);
		}
	}

	public function getCreate(){

		$items = data_prq_item::join('data_prq', 'data_prq.id_prq', '=','data_prq_item.id_prq')
					->join('data_barang', 'data_barang.id_barang', '=', 'data_prq_item.id_barang')
					->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_prq_item.id_satuan')
					->join('ref_satuan AS b', 'b.id_satuan', '=', 'data_barang.id_satuan')
					->whereIn('data_prq_item.id_prq_item', $this->ids)
					->select(
						'data_prq.no_prq', 
						'data_prq.id_prq', 
						'data_barang.nm_barang', 
						'data_barang.kode', 
						'data_barang.in', 
						'data_barang.out', 
						'data_prq_item.*', 
						'ref_satuan.nm_satuan', 
						'b.nm_satuan AS default_satuan', 
						'b.id_satuan AS default_id_satuan'
					)
					->get();

		if(count($items) < 1)
			return redirect('/po/select')->withNotif([
				'label' => 'danger',
				'err' => 'Anda belum memilih item barang untuk dijadikan SPH'
			]);

		$vendors = data_vendor::whereStatus(1)->get();


		$ids = [];
		foreach($items as $i){
			$ids[] = $i->id_barang;
		}

		$ids = json_encode($ids);

		return view('Pembelian.PO.Create', [
			'items' 	=> $items,
			'vendors' 	=> $vendors,
			'id'		=> 0,
			'no_sph'	=> 0,
			'ids'		=> $ids
		]);
	}

	
	public function postCreate(Request $req){
		$po = $this->dispatch(new CreatePOJob($req->all()));

		if($po['result'] == true){
			$req->session()->forget($this->mySession);
			$req->session()->forget('titipan');
			return redirect('/po')->withNotif([
    			'label' => 'success',
    			'err'	=> $po['err']
    		]);
		}else{
			return redirect()->back()->withNotif([
    			'label' => 'warning',
    			'err'	=> $po['err']
    		]);
		}

		
	}

}
