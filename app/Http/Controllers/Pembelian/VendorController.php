<?php

namespace App\Http\Controllers\Pembelian;

use App\Models\data_vendor;
use App\Models\ratings_vendor;

use App\Models\data_po;
use App\Models\data_retur;

use App\Jobs\Pembelian\Vendor\AddVendorJob;
use App\Jobs\Pembelian\Vendor\EditVendorJob;
use App\Http\Requests\Pembelian\Vendor\AddVendorValidate;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class VendorController extends Controller {
    
	public function getIndex(){

		$items = data_vendor::listall()->paginate(10);
		return view('Pembelian.Vendor.DaftarVendors', [
			'items' => $items
		]);
	}

	public function getAllvendor(Request $req){
		if($req->ajax()){

			$res = [];
			$out = '';

			$status = $req->disabled === 'true' ? 0 : 1;

			$items = data_vendor::listall($req->all(), $status)->paginate($req->limit);

			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;

			if($items->total() > 0):

			foreach($items as $item){

				if($item->status == 0){

					$link = '
							<div class="links">
								<small>
									[
										<a href="javascript:;" onclick="restore(' . $item->id_vendor . ');">Restore</a>
									]
								</small>
							</div>
						';

				}else{

					if(\Auth::user()->permission > 1){

						$del = \Auth::user()->permission > 2 ? '| <a href="javascript:;" onclick="disabled(' . $item->id_vendor . ');" class="text-danger">Disable</a>' : '';

						$link = '
							<div class="links">
								<small>
									[
										<a href="' . url('/vendor/edit/' . $item->id_vendor ) . '">Edit</a>
										' . $del . '
									]
								</small>
							</div>
						';
					}else{
						$link = '';
					}

				}
				

				$out .= '
					<tr class="vendor-' . $item->id_vendor . '">
						<td>' . $no . '</td>
						<td>
							<a href="#" data-toggle="modal" data-target="#detail" onclick="detail(' . $item->id_vendor . ');">' . $item->kode . '</a>
							' . $link . '
						</td>
						<td>
							' . $item->nm_vendor . '
							<div><small class="text-muted"><i class="fa fa-phone"></i> ' . $item->telpon . '</small></div>
						</td>
						<td>
							' .  \Format::indoDate($item->created_at) . '
							<div><small class="text-muted">
								' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '
							</small></div>
						</td>
					</tr>
				';
				$no++;
			}

			else:	
				$out = '<tr>
							<td colspan="4">Tidak ditemukan</td>
						</tr>';
			endif;

			$res['pagin'] = $items->render();
			$res['content'] = $out;

			return json_encode($res);
		}
	}

	public function getAdd(){

		return view('Pembelian.Vendor.AddVendor');
	}

	public function postAdd(AddVendorValidate $req){
		$vendor = $this->dispatch(new AddVendorJob($req->all()));

		return redirect('/vendor')->withNotif([
			'label' => 'success',
			'err' => 'Penyedia berhasil tersimpan dengan Kode. ' . $vendor->kode
		]);
	}

	public function getDetail(Request $req){
		if($req->ajax()){
			$vendor = data_vendor::find($req->id);

			$res = [];
			$out = '
				<h4>' . $vendor->nm_vendor . '</h4>
				<table style="width:100%;" cellpadding="5">
					<tr>
						<td class="semi-bold" width="20%">Pemilik</td>
						<td width="80%"> : ' . $vendor->pemilik . '</td>
					</tr>

					<tr>
						<td class="semi-bold" width="20%">Telpon</td>
						<td width="80%"> : ' . $vendor->telpon . '</td>
					</tr>

					<tr>
						<td class="semi-bold" width="20%">Fax</td>
						<td width="80%"> : ' . $vendor->fax . '</td>
					</tr>

					<tr>
						<td class="semi-bold" width="20%">Email</td>
						<td width="80%"> : ' . $vendor->email . '</td>
					</tr>

					<tr>
						<td class="semi-bold" width="20%">Website</td>
						<td width="80%"> : ' . $vendor->website . '</td>
					</tr>

				</table>
				<br />
				<div class="well well-sm"><em>' . $vendor->alamat . '</em></div>
					
					
			';

			$btn = '<a href="' . url('/vendor/review/' . $vendor->id_vendor) . '" class="btn btn-primary">Lihat Rinci</a>';

			$res['kode'] = $vendor->kode;
			$res['content'] = $out;
			$res['btn'] = $btn;

			return json_encode($res);

		}
	}

	public function getReview($id){
		$vendor = data_vendor::view($id);
		return view('Pembelian.Vendor.View', [
			'vendor' => $vendor
		]);
	}

	public function getRatings(Request $req){
		if($req->ajax()){
			$res = [];

			$kecepatan = ratings_vendor::where('id_vendor', $req->id)
				->avg('kecepatan');
			$ketepatan = ratings_vendor::where('id_vendor', $req->id)
				->avg('ketepatan');
			$pelayanan = ratings_vendor::where('id_vendor', $req->id)
				->avg('pelayanan');

			$out = '
				<address>
					<strong>Kecepatan</strong>
					<p><input type="hidden" value="' . number_format($kecepatan,2,'.','') . '" name="kecepatan" data-rel="rating" readonly /></p>
					<strong>Ketepatan</strong>
					<p><input type="hidden" value="' . number_format($ketepatan,2,'.','') . '" name="ketepatan" data-rel="rating" readonly /></p>
					<strong>Pelayanan</strong>
					<p><input type="hidden" value="' . number_format($pelayanan,2,'.','') . '" name="pelayanan" data-rel="rating" readonly /></p>
				</address>
			';
				
			$res['content'] = $out;

			return json_encode($res);
		}
	}

	public function getViewrats(Request $req) {
		if($req->ajax()){
			$res = [];
			$out = '
			<div>
			<button class="btn btn-primary pull-right btn-mini" onclick="closeRating();">Keluar</button>
				<h4>Overview</h4>
			</div>
			<table class="table table-bordered">';

			$items = ratings_vendor::byvendor($req->id)->paginate(10);

			if($items->total() > 0):

				foreach($items as $item){
					$out .= '
						<tr>
							<td>
								<div class="row">

									<div class="col-sm-6">
									<small class="text-muted pull-right">' . \Format::indoDate($item->created_at) . '</small>
										<p>
											<b>' . $item->nm_depan . ' ' . $item->nm_belakang . ' </b>
										</p>
										<div class="row">

											<div class="col-sm-4">
												<address>
													<small class="semi-bold">Kecepatan</small>
													<p><input type="hidden" value="' . number_format($item->kecepatan,2,'.','') . '" name="kecepatan" data-rel="ratings" readonly /></p>
												</address>
											</div>

											<div class="col-sm-4 text-center">
												<address>
													<small class="semi-bold">Ketepatan</small>
													<p><input type="hidden" value="' . number_format($item->ketepatan,2,'.','') . '" name="ketepatan" data-rel="ratings" readonly /></p>
												</address>
											</div>

											<div class="col-sm-4 text-center">
												<address>
													<small class="semi-bold">Pelayanan</small>
													<p><input type="hidden" value="' . number_format($item->pelayanan,2,'.','') . '" name="pelayanan" data-rel="ratings" readonly /></p>
												</address>
											</div>

										</div>
									</div>

									<div class="col-sm-6">
										<div class="well well-sm">' . $item->keterangan . '</div>
									</div>
								</div>
							</td>
						</tr>
					';
				}

			else:

				$out .= '<tr>
					<td>Tidak ditemukan</td>
				</tr>';

			endif;

			$out .= '</table>';

			$res['content'] = $out;
			$res['pagin'] 	= $items->render();
			$res['id'] 		= $req->id;

			return json_encode($res);
		}
	}


	public function getEdit($id){

		if(\Auth::user()->permission < 2)
			return redirect('/vendor')->withNotif([
				'lable' => 'warning',
				'err' => 'Maaf, Anda tidak memiliki hak akses ke halaman tersebut!'
			]);

		$vendor = data_vendor::find($id);
		return view('Pembelian.Vendor.Edit', [
			'vendor' => $vendor
		]);

	}

	public function postEdit(Request $req){
		$this->dispatch(new EditVendorJob($req->all()));
		return redirect('/vendor')->withNotif([
			'label' => 'success',
			'err' => 'Penyedia berhasil diperbaharui.'
		]);
	}

	public function postDisable(Request $req){

		if($req->ajax()){
			$p = data_vendor::find($req->id);
			$p->update([
				'status' => 0
			]);

			\Loguser::create('Me none aktifkan Penyedia Kode. ' . $p->kode);

			return json_encode([
				'id' => $req->id
			]);
		}

	}


	public function postActivated(Request $req){

		if($req->ajax()){
			$p = data_vendor::find($req->id);
			$p->update([
				'status' => 1
			]);

			\Loguser::create('Meng aktifkan Penyedia Kode. ' . $p->kode);

			return json_encode([
				'id' => $req->id
			]);
		}

	}

	public function getPo($id = 0){

		if(empty($id))
			return abort(404);

		$vendor = data_vendor::find($id);

		if($vendor == null)
			return abort(404);

		$items = data_po::forvendor($id, [], [1,2,3])->paginate(10);

		$status = [
    		1 => 'Baru',
    		2 => 'Proses',
    		3 => 'Selesai',
    	];

		return view('Pembelian.Vendor.PO', [
			'vendor' => $vendor,
			'items' => $items,
			'status' => $status
		]);

	}

	public function getAllpo(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$status = $req->status == 0 ? [1,2,3] : $req->status;

			$items = data_po::forvendor($req->id, $req->all(), $status)->paginate($req->limit);
			$total = $items->total();

			$status = [
	    		1 => 'Baru',
	    		2 => 'Proses',
	    		3 => 'Selesai',
	    	];

			if($total > 0){
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								' . $item->no_po . '
								<div class="link">
									<small>
										[<a href="' . url('/po/print/' . $item->id_po) . '" target="_balnk">Print</a>]
									</small>
								</div>	
							</td>
							<td>
								<span>' . \Format::indoDate($item->created_at) . '</span>
								<div><small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
							</td>
							<td>
								<span>' . \Format::indoDate($item->deadline) . '</span>
							</td>
							<td>' . $status[$item->status] . '</td>
						</tr>
					';

					$no++;
				}
			}else{
				$out = '
					<tr>
    					<td colspan="5">Tidak ditemukan</td>
    				</tr>
				';
			}

			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);

		}
	}

	public function getRetur($id = 0){

		if(empty($id))
			return abort(404);

		$vendor = data_vendor::find($id);

		if($vendor == null)
			return abort(404);

		$items = data_retur::forvendor($id, [], [1,2,3])->paginate(10);

		$status = [
    		1 => 'Baru',
    		2 => 'Proses',
    		3 => 'Selesai',
    	];

		return view('Pembelian.Vendor.Retur', [
			'vendor' => $vendor,
			'items' => $items,
			'status' => $status
		]);

	}

	public function getAllretur(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$items = data_retur::forvendor($req->id, $req->all())->paginate($req->limit);
			if($items->total() > 0 ):
			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
			foreach($items as $item){
				$out .= '
					<tr>
						<td>' . $no . '</td>
						<td>' . $item->no_retur . '</td>
						<td>' . $item->no_po . '</td>
						<td>&nbsp;</td>
						<td>
							' . \Format::indoDate2($item->created_at) . '<br />
							<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
						</td>
						<td>
							<a class="btn btn-white" href="' . url('returvendor/print/' . $item->id_retur) . '" target="_blank"><i class="fa fa-print"></i></a>
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


}
