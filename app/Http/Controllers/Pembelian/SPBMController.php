<?php

namespace App\Http\Controllers\Pembelian;

use App\Models\data_po;
use App\Models\data_po_item;
use App\Models\data_spbm;
use App\Models\data_spbm_item;

use App\Jobs\Pembelian\SPBM\CreateSPBMJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SPBMController extends Controller {

	public function getIndex(){

		$items = data_spbm::show()->paginate(10);
		return view('Pembelian.SPBM.Index', [
			'items' => $items
		]);
	}

	public function getAllgr(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$items = data_spbm::show($req->all())->paginate($req->limit);
			
			if($items->total() > 0):
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								<a href="' . url('/gr/detail/' . $item->id_spbm) . '">' . $item->no_spbm . '</a>
								<div class="text-muted">
									<small>PO No. ' . $item->no_po . '</small>
								</div>
							</td>
							<td>
								' . \Format::indoDate($item->tgl_terima_barang) . '
								<div class="text-muted">
									<small>Periksa ' . \Format::indoDate($item->tgl_periksa_barang) . '</small>
								</div>
							</td>
							<td>
								' . $item->nm_vendor . '
								<div class="text-muted">
									<small>oleh ' . $item->nm_pengirim . '</small>
								</div>
							</td>
							<td>
								<a target="_blank" href="' . url('/gr/print/' . $item->id_spbm) . '" class="btn btn-white"><i class="fa fa-print"></i></a>
							</td>
						</tr>
					';
					$no++;
				}
			else:
				$out = '
					<tr>
						<td colspan="5">Tidak ditemukan</td>
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
			2 => 'Proses'
		];
		$items = data_po::forspbm()->paginate(10);
		return view('Pembelian.SPBM.PO', [
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
				2 => 'Proses'
			];

			$stat = ($req->status == 0) ? [1,2] : [$req->status];

			$items = data_po::forspbm($req->all(), $stat)->paginate($req->limit);
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
										[<a href="' . url('/gr/creategr/' . $item->id_po) . '">Proses</a>]
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

	public function getCreategr($id = 0){

		if(empty($id) || !is_numeric($id))
			return redirect('/gr/po')->withNotif([
				'label' => 'danger',
				'err' => 'PO tidak ditemukan !'
			]);

		$po = data_po::join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->where('data_po.id_po', $id)
			->select('data_po.*', 'data_vendor.nm_vendor');
		
		if($po->count() == 0)
			return redirect('/gr/po')->withNotif([
				'label' => 'danger',
				'err' => 'PO tidak ditemukan !'
			]);

		$po = $po->first();
		if($po->status > 2)
			return redirect('/gr/po')->withNotif([
				'label' => 'danger',
				'err' => 'PO tidak ditemukan !'
			]);

		$items = data_po_item::forspbmbypo($id)->get();

		$status = [
			1 => 'Status baru',
			2 => 'Dalam proses'
		];

		return view('Pembelian.SPBM.Creategr', [
			'po' => $po,
			'items' => $items,
			'status' => $status
		]);

	}

	public function postAddbonus(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$item = data_po_item::bypo($req->id);

			$res['content'] = '
				<tr class="bonus-' . $req->id . ' item-bonus">
					<td title="' . $item->nm_barang . '">
						' . $item->nm_barang . '
						<div class="text-muted"><small>' . $item->kode . '</small></div>
						<input type="hidden" name="bonus[]" value="1">
						<input type="hidden" name="id_po_item[]" value="' . $req->id . '">
						<input type="hidden" name="id_barang[]" value="' . $item->id_item . '">
						<input type="hidden" name="barang_sesuai[]" value="1">
						<input type="hidden" name="req_qty[]" value="0">
						
						<input type="hidden" name="id_satuan[]" value="' . $item->id_satuan . '">
						<input type="hidden" name="id_satuan_default[]" value="' . $item->id_satuan_default . '">
					</td>
					<td>
						<input type="text" data-exp="bonus" name="tgl_exp[]" class="form-control" value="' . date('Y-m-d', strtotime('+1 year')) . '" required="required" readonly="readonly" />
					</td>
					<td>
						<div class="input-group transparent">
						 	<span class="input-group-addon"></span>
						  	<input type="number"  name="qty_lg[]" class="text-right form-control" required />
						  	<span class="input-group-addon">
								<small>' . $item->nm_satuan . '</small> &nbsp;&nbsp;
						  	</span>
						</div>
					</td>
					<td>
						<button class="btn btn-white" type="button" data-toggle="tooltip" onclick="rmbonus(' . $req->id . ');" data-placement="bottom" title="Hapus Bonus"><i class="fa fa-times"></i></button>
						<input type="hidden" name="merek[]" value="" class="form-control">
						<input type="hidden" name="kets[]" value="Bonus" class="form-control">
					</td>
				</tr>
			';

			return json_encode($res);
		}
	}

	public function postCreategr(Request $req){
		$gr = $this->dispatch(new CreateSPBMJob($req->all()));
		return redirect('/gr')->withNotif([
			'label' => $gr['label'],
			'err'	=> $gr['err']
		]);
	}


	public function getDetail($id = 0){

		if(empty($id) || !is_numeric($id))
			return redirect('/gr')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf, tidak ditemukan !'
			]);

		$gr = data_spbm::byid($id)->first();

		if($gr == null)
			return redirect('/gr')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf, tidak ditemukan !'
			]);
		$items = data_spbm_item::bygr($id)->get();
		
		$kirim = [
			1 => 'Dikirim oleh Supplier',
			2 => 'Dikirim Oleh Ekspedisi',
			3 => 'Diambil Oleh Onkologi'
		];

		return view('Pembelian.SPBM.Detail', [
			'gr' => $gr,
			'items' => $items,
			'kirim' => $kirim
		]);
	}

	public function getPrint($id = 0){

		if(empty($id) || !is_numeric($id))
			return redirect('/gr')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf, tidak ditemukan !'
			]);
		$gr = data_spbm::byid($id)->first();
		if($gr == null)
			return redirect('/gr')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf, tidak ditemukan !'
			]);

		$items = data_spbm_item::bygr($id)->get();

		$kirim = [
			1 => 'Dikirim oleh Supplier',
			2 => 'Dikirim Oleh Ekspedisi',
			3 => 'Diambil Oleh Onkologi'
		];

		return view("Print.Pembelian.GR.printGR",[
			'items' => $items,
			'gr' => $gr,
			'kirim' => $kirim
			]);

	}

	

}
