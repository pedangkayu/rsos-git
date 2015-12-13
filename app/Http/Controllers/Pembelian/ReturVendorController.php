<?php

namespace App\Http\Controllers\Pembelian;

use App\Models\data_retur;
use App\Models\data_po;
use App\Models\data_vendor;
use App\Models\data_po_item;
use App\Models\ref_ket_retur;
use App\Models\data_retur_item;

use App\Jobs\Pembelian\ReturVendor\CreateReturJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReturVendorController extends Controller {
    
	public function getIndex(){

		$items = data_retur::pembelian()->paginate(10);
		return view('Pembelian.ReturVendor.Index', [
			'items' => $items
		]);
	}

	public function getAllretur(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$items = data_retur::pembelian($req->all())->paginate($req->limit);
			if($items->total() > 0 ):
			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
			foreach($items as $item){
				$out .= '
					<tr>
						<td>' . $no . '</td>
						<td>' . $item->no_retur . '</td>
						<td>' . $item->no_po . '</td>
						<td>
							' . $item->nm_vendor . '<br />
							<small class="text-muted">
								' . $item->telpon . '
							</small>
						</td>
						<td>
							' . \Format::indoDate2($item->created_at) . '<br />
							<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
						</td>
					</tr>
				';
				$no++;
			}
			else:
				$out = '
					<tr>
						<td colspan="5">Tidak ditemukan!</td>
					</tr>
				';
			endif;

			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);

		}
	}

	public function getPo(){

		$status = [
			1 => 'Baru',
			2 => 'Proses',
			3 => 'Selesai'
		];
		$items = data_po::forretur()->paginate(10);
		return view('Pembelian.ReturVendor.PO', [
			'items' => $items,
			'status' => $status
		]);
	}

	public function getAllpo(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$status = [
				1 => 'Baru',
				2 => 'Proses',
				3 => 'Selesai'
			];

			$stat = ($req->status == 0) ? [1,2] : [$req->status];

			$items = data_po::forretur($req->all())->paginate($req->limit);
			if($items->total() > 0):
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								' . $item->no_po . '
								<div class="text-muted link">
									<small>
										[<a href="' . url('/returvendor/cretereture/' . $item->id_po) . '">Proses</a>]
									</small>
								</div>
							</td>
							<td>
								' . \Format::indoDate($item->created_at) . '
								<div><small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
							</td>
							<td>
								' . $item->nm_vendor . '
								<div><small class="text-muted">dedline ' . \Format::hari($item->deadline) . ', ' . \Format::indoDate($item->deadline) . '</small></div>
							</td>
							<td>' . $status[$item->status] . '</td>
						</tr>
					';
					$no++;
				}
			else:
				$out = '<tr>
					<td colspan="5">Tidak ditemukan</td>
				</tr>';
			endif;

			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);
		}
	}

	public function getCretereture($id = 0){
		if(empty($id) || !is_numeric($id))
			return redirect('/returvendor/po')->withNotif([
				'label' => 'danger',
				'err' => 'Tidak ditemukan!'
			]);

		$po = data_po::join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->where('data_po.id_po', $id)
			->select('data_po.*', 'data_vendor.nm_vendor', 'data_vendor.alamat', 'data_vendor.telpon')
			->first();

		if($po == null || $po->status < 2)
			return redirect('/returvendor/po')->withNotif([
				'label' => 'danger',
				'err' => 'Tidak ditemukan!'
			]);

		$items = data_po_item::forretur($id)->get();

		$status = [
			1 => 'Baru',
			2 => 'Proses',
			3 => 'Selesai'
		];
		return view('Pembelian.ReturVendor.Create', [
			'po' => $po,
			'items' => $items,
			'status' => $status
		]);
	}

	public function getRefretur(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '<option value="">Pilih</option>';
			$items = ref_ket_retur::where('tipe', 2)->get();
			foreach($items as $item){
				$out .= '<option value="' . $item->id_ket_retur . '">' . $item->keterangan . '</option>';
			}

			$res['content'] = $out;
			return json_encode($res);
		}
	}


	public function postCretereture(Request $req){

		if(array_sum($req->qty) < 1)
			return redirect()->back()->withNotif([
				'label' => 'danger',
				'err' => 'Qty tidak boleh kosong semuanya!'
			]);
		
		$retur = $this->dispatch(new CreateReturJob($req->all()));

		if($retur['result'] == true){
			return redirect('/returvendor')->withNotif([
				'label' => $retur['label'],
				'err' => $retur['err']
			]);
		}else{
			return redirect()->back()->withNotif([
				'label' => $retur['label'],
				'err' => $retur['err']
			]);
		}
	}

	public function postAddketretur(Request $req){
		if($req->ajax()){
			ref_ket_retur::create([
				'keterangan' => $req->keterangan,
				'tipe' => 2
			]);

			return json_encode([
				'return' => true
			]);
		}
	}

	public function getPrint($id){
		if(empty($id) || !is_numeric($id))
			return redirect('/returvendor')->withNotif([
				'label' => 'danger',
				'err' => 'Tidak ditemukan!'
			]);

		$retur = data_retur::join('data_po','data_retur.id_po','=','data_retur.id_po')
			->join('data_vendor','data_retur.id_vendor','=','data_vendor.id_vendor')
			->where('id_retur',$id)
			->first();

		$items = data_retur_item::join('data_barang','data_retur_item.id_barang','=','data_barang.id_barang')
		->join('ref_satuan','ref_satuan.id_satuan','=','data_retur_item.id_satuan')
		->where('id_retur',$id)
		->get();

		return view('Print.Pembelian.Retur.printRetur', [
			'retur' => $retur,
			'items' => $items
		]);
	}

	public function getItempo(){

		return view('Pembelian.ReturVendor.ItemPO');
	}

	public function getAllitemspo(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$status = [
				2 => 'Proses',
				3 => 'Selesai'
			];
			$items = data_po_item::itemforretur($req)->paginate($req->limit);
			$total = $items->total();
			if($total > 0){
				foreach($items as $item){
					$out .= '
						<tr class="item">
							<td width="20%">
								<a href="#" data-toggle="modal" data-target="#review" onclick="">' . $item->kode . '</a>
							</td>
							<td width="55%" colspan="2">
								' . $item->nm_barang . '<br />
								<small class="text-muted">No. ' . $item->no_po . ' | ' . \Format::hari($item->created_at) . ', ' . \Format::indoDate2($item->created_at) . ' | ' . $status[$item->status] . '</small>
							</td>
							<td width="15%">
								<a href="' . url('/returvendor/cretereture/' . $item->id_po) . '" class="btn btn-white btn-block btn-small">Proses</a>
							</td>
						</tr>
					';
				}
			}else{
				$out = '
					<tr>
						<td colspan="3">Tidak ditemukan</td>
					<tr>
				';
			}

			$res['total'] 	= $total;
			$res['pagin'] 	= $items->render();
			$res['content'] = $out;

			return json_encode($res);

		}
	}

}
