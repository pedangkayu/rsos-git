<?php

namespace App\Http\Controllers\Pembelian;

use App\Models\data_sph;
use App\Models\data_prq;
use App\Models\data_vendor;
use App\Models\data_barang;
use App\Models\data_po_item;
use App\Models\data_sph_grup;
use App\Models\data_prq_item;
use App\Models\data_sph_item;

use App\Jobs\Pembelian\SPH\EditSPHJob;
use App\Jobs\Pembelian\SPH\CopySPHJob;
use App\Jobs\Pembelian\SPH\SPHtoPOJob;
use App\Jobs\Pembelian\SPH\CreateSPHJob;
use App\Jobs\Pembelian\SPH\POfromSPHJob;
use App\Jobs\Pembelian\SPH\EditSPHSystemJobs;
use App\Jobs\Pembelian\SPH\PoFromEditSPHSystemJobs;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SPHController extends Controller {
    
	public
		$mySession,
		$ids;

	public function __construct(){
		$format = '_itemSPH' . \Auth::user()->id_user;
		$this->mySession = $format;

		if(!empty(session()->get($format))){
			foreach(session()->get($format) as $val){
				$this->ids[] = $val['id_prq_item'];
			}
		}
	}

	public function getIndex(){
		$items = data_sph_grup::show()->paginate(10);
		$status = [
			1 => 'Baru',
			2 => 'Selesai'
		];
		return view('Pembelian.SPH.Master', [
			'items' 	=> $items,
			'status' 	=> $status
		]);
	}

	public function getAllsph(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$stat = [
				1 => 'Baru',
				2 => 'Selesai'
			];

			$status = $req->status == 0 ? [1,2] : $req->status;

			$items = data_sph_grup::show($req->all(), $status)->paginate($req->limit);

			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;

			if($items->total() > 0):
			foreach($items as $item){

				$hapus = \Auth::user()->permission > 1 && $item->status == 1 ? '| <a href="javascript:;" onclick="hapus(' . $item->id_sph_grup . ');" class="text-danger">Hapus</a>' : '';

				$out .= '
					<tr class="sph-' . $item->id_sph_grup . '">
						<td>' . $no . '</td>
						<td>
							' . $item->no_sph . '
							<div class="link">
								<small>
									[
										<a href="' . url('/sph/review/' . $item->id_sph_grup) . '">Lihat</a>
										' . $hapus . '
									]
								</small>
							</div>
						</td>
						<td>' . $item->nm_depan . ' ' . $item->nm_belakang . '</td>
						<td>
							' .  \Format::indoDate($item->created_at) . '
							<div>
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
							</div>
						</td>
						<td>
							' . $stat[$item->status] . '
						</td>
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

	public function postHapussphgrup(Request $req){
		if($req->ajax()){
			$sph = data_sph_grup::find($req->id);
			$sph->update([
				'status' => 3
			]);

			\Loguser::create('Menghapus Grup SPH No. ' . $sph->no_sph);
			return json_encode([
				'id' => $req->id
			]);
		}
	}

	public function getSelect(){
		//session()->forget($this->mySession);
		return view('Pembelian.SPH.SelectItem');
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
					$add 	= $item->id_acc > 0 && $item->status < 3 ? '<button class="btn btn-white" onclick="add(' . $item->id_prq_item . ');"><i class="fa fa-plus"></i></button>' : '<div title="Belum Ter Verifikasi"><i class="fa fa-times text-danger"></i></div>';

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
				'id' => $req->id
			]);

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
					->select('data_prq.no_prq', 'data_barang.kode', 'data_barang.nm_barang', 'data_prq_item.id_prq_item')
					->get();

				foreach($items as $item){
					$out .= '<tr class="prq_item_' . $item->id_prq_item . '">
						<td>
							<button type="button" class="close" onclick="delselected(' . $item->id_prq_item . ');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							' . $item->kode . '
							    <div class="slctd2">' . $item->no_prq . '</div>
								<div class="slctd">' . $item->nm_barang . '</div>
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
			return json_encode([
				'result' => true
			]);
		}
	}

	public function getCreate($id = 0){
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
			return redirect('/sph/select')->withNotif([
				'label' => 'danger',
				'err' => 'Anda belum memilih item barang untuk dijadikan SPH'
			]);

		//$vendors = data_vendor::whereStatus(1)->get();

		if($id > 0):
			$sph = data_sph_grup::whereId_sph_grup($id);
			if($sph->count() < 1)
				return redirect('/sph')->withNotif([
					'label' => 'danger',
					'err' => 'SPH tidak ditemukan!'
				]);

			$sp = $sph->first();

			$no_sph = $sp->no_sph;
		else:
			$no_sph = 0;
		endif;

		$ids = [];
		foreach($items as $i){
			$ids[] = $i->id_barang;
		}

		$ids = json_encode($ids);

		return view('Pembelian.SPH.CreateSPH',[
			'items' 	=> $items,
			'id'		=> $id,
			'no_sph'	=> $no_sph,
			'ids' 		=> $ids
		]);
	}

	public function postCreate(Request $req){

		//dd($req->next);

		$this->validate($req, [
			'vendor' 	=> 'required',
			'deadline' 	=> 'required'
		]);

		// Langsung Membuat PO
		if($req->po != null){
			$po = $this->dispatch(new POfromSPHJob($req->all()));
			
			if($po['result'] == true){
				$req->session()->forget($this->mySession);
				return redirect('/po')->withNotif([
					'label' => 'success',
					'err' => $po['err']
				]);
			}else{
				return redirect()->back()->withNotif([
					'label' => 'warning',
					'err' => $po['err']
				]);
			}
		}

		$sph = $this->dispatch(new CreateSPHJob($req->all()));

		if($sph['status'] == false)
			return redirect()->back()->withNotif([
				'label' => 'danger',
				'err' => $sph['err']
			]);
		
		if($req->next != null){
			return redirect('/sph/create/' . $sph['err']->id_sph_grup )->withNotif([
				'label' => 'success',
				'err' => 'SPH berhasil dibuat dengan no.' . $sph['err']->no_sph . ', silahkan menambahkan kembali data SPH nya!'
			]);
		}else{
			$req->session()->forget($this->mySession);
			return redirect('/sph')->withNotif([
				'label' => 'success',
				'err' => 'SPH berhasil dibuat dengan no.' . $sph['err']->no_sph
			]);
		}

	}

	public function getLastprice(Request $req){
		if($req->ajax()){
			$res = [];
			$out = [];
			$items = data_po_item::join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
				->where('data_po.id_vendor', $req->id)
				->whereIn('data_po_item.id_item', $req->id_barang)
				->select('data_po_item.harga', 'data_po_item.diskon', 'data_po_item.id_item')
				->orderby('data_po_item.id_po_item', 'desc')
				->limit(count($req->id_barang))
				->get();
			$count = [];
			foreach($items as $item){
				$out[] = [
					'id' => $item->id_item,
					'harga' => (INT) $item->harga,
					'diskon' => $item->diskon == null ? 0 : $item->diskon
				];

				$count[] = 1;
			}

			$res['count'] = count($count);
			$res['content'] = $out;

			return json_encode($res);

		}
	}

	public function getLogharga(Request $req){
		if($req->ajax()){

			$res = [];
			$items = data_po_item::join('data_po', 'data_po.id_po', '=', 'data_po_item.id_po')
				->where('data_po.id_vendor', $req->vendor)
				->whereIn('data_po.status', [1,2,3])
				->where('data_po_item.id_item', $req->id_barang)
				->orderby('data_po_item.id_po_item', 'desc')
				->select('data_po.no_po', 'data_po.id_po', 'data_po.created_at', 'data_po.status', 'data_po_item.harga')
				->paginate(10);

			$status = [
				1 => 'Baru',
				2 => 'Proses',
				3 => 'Selesai'
			];

			$out = '<table class="table table-striped" >
				<thead>
					<tr>
						<th>PO</th>
						<th>Tanggal</th>
						<th>Status</th>
						<th class="text-right">Harga</th>
					</tr>
				</thead>
				<tbody>
			';

			if($items->total() > 0){

				foreach($items as $item){
					$out .= '<tr>
						<td>
							<a href="' . url('/po/print/' . $item->id_po) . '" target="_blank">' . $item->no_po . '</a>
						</td>
						<td>
							' . \Format::indoDate($item->created_at) . '<br />
							<div><small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
						</td>
						<td>' . $status[$item->status] . '</td>
						<td class="text-right">' . number_format($item->harga,0,',','.') . '</td>
					</tr>';
				}

			}else{
				$out .= '<tr>
					<td colspan="4">Tidak ditemukan</td>
				<tr>';
			}

			
			$out .= '</tbody></table>';

			$vendor = data_vendor::find($req->vendor);
			$barang = data_barang::find($req->id_barang);

			$res['vendor'] = $vendor->nm_vendor;
			$res['kode'] = $barang->kode;
			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);

		}
	}

	public function getVendors(Request $req){
		if($req->ajax()){

			$res = [];
			$out = '<option value="0">Pilih Penyedia</option>';
			$vendors = data_vendor::whereStatus(1)->get();
			foreach($vendors as $ven){

				$select = ($req->idselect == $ven->id_vendor) ? 'selected="selected"' : '';

				$out .= '<option ' . $select . ' value="' . $ven->id_vendor . '">' . $ven->nm_vendor . '</option>';
			}
			$res['content'] = $out;
			return json_encode($res);
		}
	}

	public function getReview($id){

		$grup 	= data_sph_grup::find($id);
		$sph 	= data_sph::join('data_vendor', 'data_vendor.id_vendor', '=', 'data_sph.id_vendor')
			->where('data_sph.id_sph_grup',$grup->id_sph_grup)
			->whereIn('data_sph.status',[1,2,3])
			->orderby('data_sph.status', 'desc')
			->orderby('data_sph.id_sph', 'asc')
			->select('data_vendor.nm_vendor', 'data_vendor.telpon', 'data_sph.*')->get();

		return view('Pembelian.SPH.ReviewSPH', [
			'grup' => $grup,
			'items' => $sph
		]);
	}

	public function getEditsph($id){

		$items = data_sph_item::join('data_prq', 'data_prq.id_prq', '=','data_sph_item.id_prq')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_sph_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_sph_item.id_satuan')
			->join('ref_satuan As b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_sph_item.id_sph', $id)
			->select(
				'data_prq.no_prq', 
				'data_barang.nm_barang', 
				'data_barang.id_barang', 
				'data_barang.kode', 
				'data_barang.in', 
				'data_barang.out', 
				'data_sph_item.*', 
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS default_satuan',
				'b.id_satuan AS default_id_satuan'
			)
			->get();
		


		$sph = data_sph::find($id);

		$ids = [];
		foreach($items as $i){
			$ids[] = $i->id_barang;
		}

		$ids = json_encode($ids);

		return view('Pembelian.SPH.EditSPH', [
			'sph' 		=> $sph,
			'items' 	=> $items,
			'id'		=> $id,
			'no_sph'	=> 0,
			'ids'		=> $ids
		]);
	}

	public function getEditsphsystem($id){

		$items = data_sph_item::join('data_prq', 'data_prq.id_prq', '=','data_sph_item.id_prq')
			->join('data_barang', 'data_barang.id_barang', '=', 'data_sph_item.id_item')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_sph_item.id_satuan')
			->join('ref_satuan As b', 'b.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_sph_item.id_sph', $id)
			->select(
				'data_prq.no_prq', 
				'data_barang.nm_barang', 
				'data_barang.id_barang', 
				'data_barang.kode', 
				'data_barang.in', 
				'data_barang.out', 
				'data_sph_item.*', 
				'ref_satuan.nm_satuan',
				'b.nm_satuan AS default_satuan',
				'b.id_satuan AS default_id_satuan'
			)
			->get();
		


		$sph = data_sph::find($id);

		$ids = [];
		foreach($items as $i){
			$ids[] = $i->id_barang;
		}

		$ids = json_encode($ids);

		return view('Pembelian.SPH.EditSPHSystem', [
			'sph' 		=> $sph,
			'items' 	=> $items,
			'id'		=> $id,
			'no_sph'	=> 0,
			'ids'		=> $ids
		]);
	}

	public function postEditsphsystem(Request $req){
		$sph = $this->dispatch(new EditSPHSystemJobs($req->all()));
		
		if($sph['status'] == false)
			return redirect()->back()->withNotif([
				'label' => $sph['label'],
				'err' => $sph['err']
			]);

		$po = $this->dispatch(new PoFromEditSPHSystemJobs($sph['data']));
		// dd($po);
		if($po['status'] == true)
			return redirect('/po')->withNotif([
				'label' => $po['label'],
				'err' => $po['err']
			]);
	}

	public function postEdit(Request $req){

		$this->validate($req, [
			'vendor' 	=> 'required',
			'deadline' 	=> 'required'
		]);

		$sph = $this->dispatch(new EditSPHJob($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'SPH berhasil diperbaharui'
		]);
	}

	public function getCopy($id){

		$sph = $this->dispatch(new CopySPHJob($id));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $sph['err']
		]);

	}

	public function postDelitemsph(Request $req){
		if($req->ajax()){

			$sph = data_sph::find($req->id);
			$sph->update([
				'status' => 0
			]);

			\Loguser::create('Menghapus SPH No. ' . $sph->no_sph_item);
			return json_encode([
				'id' => $req->id
			]);
		}
	}

	public function postTopo(Request $req){
		$this->validate($req, [
			'id_sph' => 'required'
		], [
			'required' => 'Anda harus menentukan salah satu dari SPH yang ada di bawah ini!'
		]);
		
		$po = $this->dispatch(new SPHtoPOJob($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $po['err']
		]);

	}

	public function getPrint($id){

		$items = data_sph_item::join('data_prq', 'data_prq.id_prq', '=','data_sph_item.id_prq')
					->join('data_barang', 'data_barang.id_barang', '=', 'data_sph_item.id_item')
					->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
					->where('data_sph_item.id_sph', $id)
					->select(
						'data_prq.no_prq',
						'data_barang.nm_barang', 
						'data_barang.id_barang', 
						'data_barang.kode', 
						'data_barang.in', 
						'data_barang.out', 
						'data_sph_item.*', 
						'ref_satuan.nm_satuan'
					)
					->get();

		$sph 	= data_sph::find($id);
		$vendor = data_vendor::find($sph->id_vendor);
		$grup 	= data_sph_grup::whereId_sph_grup($sph->id_sph_grup)->select('status')->first();


		/* Matematika */
		$subtotal 	= 0;
		foreach($items as $item){
			$subtotal += ( $item->harga - ($item->harga * $item->diskon / 100) ) * $item->qty;
		}

		$diskon 	= ($subtotal * $sph->diskon) / 100;
		$aftdiskon	= $subtotal - $diskon;
		$ppn 		= ($aftdiskon * $sph->ppn) / 100;
		$pph 		= ($aftdiskon * $sph->pph) / 100;
		$grandtotal = $aftdiskon + $ppn + $pph + $sph->adjustment;

		$matematika = [
			'subtotal' 		=> $subtotal,
			'diskon'		=> $diskon,
			'aftdiskon'		=> $aftdiskon,
			'ppn'			=> $ppn,
			'pph'			=> $pph,
			'grandtotal' 	=> $grandtotal
		];

		// Status SPH
		$status = $grup->status == 2 && $sph->status == 1 ? true : false;
		return view('Print.Pembelian.SPH.PrintSPH', [
			'items' 	=> $items,
			'sph' 		=> $sph,
			'vendor' 	=> $vendor,
			'mtk'		=> $matematika,
			'status'	=> $status
		]);
	}

}

