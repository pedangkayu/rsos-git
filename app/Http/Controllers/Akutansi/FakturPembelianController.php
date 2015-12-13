<?php

namespace App\Http\Controllers\Akutansi;

use App\Models\data_po;
use App\Models\ref_coa;
use App\Models\data_faktur;
use App\Models\data_vendor;
use App\Models\data_barang;
use App\Models\data_jurnal;
use App\Models\data_po_item;
use App\Models\data_faktur_item;
use App\Models\ref_payment_terms;
use App\Models\ref_payment_method;

use App\Jobs\Akutansi\Faktur\SaveJurnalJob;

use App\Jobs\Akutansi\Faktur\CreateFakturJob;
use App\Jobs\Akutansi\Faktur\EditFaktur;

use App\Jobs\Pembelian\Vendor\AddVendorJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FakturPembelianController extends Controller {

	public function getIndex(){
		$status = [
			0 => 'Belum Bayar',
			1 => 'Nyicil',
			2 => 'Lunas',
			3 => 'Batal'
		];
		$items = data_faktur::daftar()->paginate(10);
		return view('Akutansi.FakturPembelian.Index', [
			'items' => $items,
			'status' => $status
		]);
	}

	public function getItemsfaktur(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$status = [
				0 => 'Belum Bayar',
				1 => 'Nyicil',
				2 => 'Lunas',
				3 => 'Batal'
			];

			$items = data_faktur::daftar($req->all())->paginate($req->limit);
			$total = $items->total();
			if($total > 0){
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								' . $item->nomor_faktur . '
								<div class="link">
									<small>
										[
											<a href="' . url('/fakturpembelian/view/' . $item->id_faktur) . '">Lihat</a> |
											<a href="' . url('/fakturpembelian/edit/' . $item->id_faktur) . '">Edit</a> |
											<a target="_blank" href="' . url('/fakturpembelian/print/' . $item->id_faktur) . '">Print</a> |
											<a href="javascript:void(0);" onclick="hapus(' . $item->id_faktur . ');" class="text-danger">Batal</a>
										]
									</small>
								</div>
							</td>
							<td class="text-right">' . number_format($item->total,0,',','.') . '</td>
							<td>' . \Format::indoDate2($item->tgl_faktur) . '<br />&nbsp;</td>
							<td>' . \Format::indoDate2($item->duodate) . '<br />&nbsp;</td>
							<td>' . $status[$item->status] . '</td>
						</tr>
					';
					$no++;
				}

			}else{
				$out = '<tr>
							<td colspan="6">Tidak ditemukan</td>
						</tr>';
			}

			$res['total'] = $total;
			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);
		}
	}

	public function getBaru(){

		$terms = ref_payment_terms::all();

		return view('Akutansi.FakturPembelian.Baru', [
			'terms' => $terms
		]);
	}

	public function getAlamat(Request $req){
		if($req->ajax()){
			$res = [];
			$vendor = data_vendor::find($req->id);
			if($vendor == null)
				$res['alamat'] = '';
			else
				$res['alamat'] = $vendor->alamat;
			return json_encode($res);
		}
	}

	public function postAddsupplier(Request $req){
		if($req->ajax()){
			$arr = $this->dispatch(new AddVendorJob($req->all()));
			return json_encode($arr);
		}
	}

	public function getLoaditems(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$items = data_barang::active($req->all())->paginate(5);
			$total = $items->total();

			if($total > 0):
				foreach($items as $item){
					$out .= '
						<tr class="barang-' . $item->id_barang . '">
							<td>' . $item->kode . '</td>
							<td>' . $item->nm_barang . ' <small class="pull-right hide item-loading-' . $item->id_barang . '">Memuat...</small></td>
							<td class="text-right"><button class="btn btn-white btn-small btn-item-' . $item->id_barang . '" onclick="add_item(' . $item->id_barang . ');"><i class="fa fa-plus"></i></button></td>
						</tr>
					';
				}
			else:
				$out = '
					<tr>
						<td colspan="3">Tidak ditemukan</td>
					</tr>
				';
			endif;

			$res['total'] = $total;
			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);
		}
	}

	public function getLoadpo(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$items = data_po::active($req->all())->paginate(5);
			$total = $items->total();

			$status = [
				1 => 'Baru',
				2 => 'Proses',
				3 => 'Selesai'
			];

			if($total > 0):
				foreach($items as $item){
					$out .= '
						<tr class="po-' . $item->id_po . '">
							<td>' . $item->no_po . '</td>
							<td>' . \Format::hari($item->created_at) . ', ' . \Format::indoDate2($item->created_at) . '</td>
							<td>' . $status[$item->status] . '</td>
							<td class="text-right"><button onclick="add_itempo(' . $item->id_po . ');" class="btn btn-po-' . $item->id_po . ' btn-white btn-small"><i class="fa fa-plus"></i></button></td>
						</tr>
					';
				}
			else:
				$out = '
					<tr>
						<td colspan="4">Tidak ditemukan</td>
					</tr>
				';
			endif;

			$res['total'] = $total;
			$res['content'] = $out;
			$res['pagin'] = $items->render();


			return json_encode($res);
		}

	}

	/* Mengambil Barang berdasarkan ID */
	public function getAdditem(Request $req){
		if($req->ajax()){
			$item = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
				->where('data_barang.id_barang', $req->id)
				->select(
					'data_barang.id_barang',
					'data_barang.id_satuan',
					'data_barang.kode',
					'data_barang.nm_barang',
					'data_barang.harga_beli',
					'ref_satuan.nm_satuan'
				)
				->first();

			return json_encode($item);
		}
	}

	/* Menambahkan Barang berdasarkan PO */
	public function getAdditempo(Request $req){
		if($req->ajax()){
			$res = [];
			$out = [];
			$po = data_po::find($req->id);
			$items = data_po_item::allpo($req->id)->get();
			foreach($items as $item){

				/* MATEMATIKA */
				$diskon = $item->harga * $item->diskon / 100;
				$aftdiskon = $item->harga - $diskon;
				$ppn = $aftdiskon + $item->ppn;
				$pph = $aftdiskon + $item->pph;
				$harga = $aftdiskon + $ppn + $pph;

				$out[] = [
					'id_barang' => $item->id_item,
					'kode' => $item->kode,
					'nm_barang' => $item->nm_barang,
					'id_satuan' => $item->id_satuan,
					'nm_satuan' => $item->nm_satuan,
					'qty' => $item->req_qty,
					'diskon' => $item->diskon,
					'ppn' => $item->ppn,
					'pph' => $item->pph,
					'harga' => $item->harga,
					'total' => $harga * $item->req_qty
				];
			}
			$res['po'] = $po;
			$res['items'] = $out;
			return json_encode($res);
		}
	}

    

	public function postBaru(Request $req){

		if(count($req->id_barang) == 0)
			return redirect()->back()->withNotif([
				'label' => 'danger',
				'err' => '<center>OOps!, Item tidak ditemukan</center>'
			]);

		$arr = $this->dispatch(new CreateFakturJob($req->all()));
		if($arr['res'])
			return redirect('/fakturpembelian')->withNotif([
					'label' => $arr['label'],
					'err' => $arr['err']
				]);
		else
			return redirect()->back()->withNotif([
					'label' => $arr['label'],
					'err' => $arr['err']
				]);
	}


	public function getView($id){

		if(empty($id))
			return redirect('/fakturpembelian');

		$faktur = data_faktur::views($id)->first();
		$items = data_faktur_item::byfaktur($id)->get();

		if($faktur->status == 3)
			return redirect('/fakturpembelian');

		$status = [
			0 => [
				'label' => 'important',
				'err' => 'Unpaid'
			],
			1 => [
				'label' => 'warning',
				'err' => 'Partially Paid'
			],
			2 => [
				'label' => 'info',
				'err' => 'Paid'
			],
			3 => [
				'label' => 'important',
				'err' => 'Batal'
			]
		];

		$methods = ref_payment_method::all();

		$jurnals = data_jurnal::faktur($id)->get();

		$coas = [];
		foreach(ref_coa::orderby('no_coa', 'asc')->get() as $coa){
			$coas[$coa->parent_id][] = $coa;
		}

		$select_coa = \Format::select_coa($coas);

		$total_bayar = 0;
		foreach($jurnals as $ju){
			$total_bayar += $ju->total;
		}
		
		return view('Akutansi.FakturPembelian.view', [
			'faktur' => $faktur,
			'items' => $items,
			'status' => $status,
			'methods' => $methods,
			'jurnals' => $jurnals,
			'select_coa' => $select_coa,
			'total_bayar' => $total_bayar
		]);
	}

	public function postSavejurnal(Request $req){
		$arr = $this->dispatch(new SaveJurnalJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => $arr['label'],
			'err' => $arr['err']
		]);
	}

	public function getEdit($id = 0){

		if(empty($id))
			return redirect('/fakturpembelian');

		$faktur = data_faktur::find($id);
		$items = data_faktur_item::byfaktur($id)->get();

		if($faktur->status == 3)
			return redirect('/fakturpembelian');

		$terms = ref_payment_terms::all();		
		return view('Akutansi.FakturPembelian.edit', [
			'faktur' => $faktur,
			'items' => $items,

			'terms' => $terms
		]);
	}

	public function postEdit(Request $req){

		$arr = $this->dispatch(new EditFaktur($req->all()));

		return redirect()->back()->withNotif([
			'label' => $arr['label'],
			'err' => $arr['err']
		]);

	}

	public function postDelete(Request $req){
		if($req->ajax()){
			data_faktur::find($req->id)->update([
				'status' => 3
			]);

			return json_encode([
				'id' => $req->id
			]);
		}
	}

	public function getPrint($id){

		if(empty($id))
			return redirect('/fakturpembelian');

		$faktur = data_faktur::views($id)->first();
		$items = data_faktur_item::byfaktur($id)->get();

		if($faktur->status == 3)
			return redirect('/fakturpembelian');

		$status = [
			0 => [
				'label' => 'danger',
				'err' => 'Unpaid'
			],
			1 => [
				'label' => 'info',
				'err' => 'Partially Paid'
			],
			2 => [
				'label' => 'primary',
				'err' => 'Paid'
			],
			3 => [
				'label' => 'important',
				'err' => 'Batal'
			]
		];

		$jurnals = data_jurnal::faktur($id)->get();

		return view('Akutansi.FakturPembelian.print', [
			'faktur' => $faktur,
			'items' => $items,
			'status' => $status,
			'jurnals' => $jurnals
		]);
	}

	public function postStatus(Request $req){
		if($req->ajax()){
			data_faktur::find($req->id)->update([
				'status' => $req->status
			]);

			$status = [
				0 => [
					'label' => 'important',
					'err' => 'Unpaid'
				],
				1 => [
					'label' => 'warning',
					'err' => 'Partially Paid'
				],
				2 => [
					'label' => 'info',
					'err' => 'Paid'
				],
				3 => [
					'label' => 'important',
					'err' => 'Batal'
				]
			];

			$out = '
				<span class="label label-' . $status[$req->status]['label'] . '">
					' . $status[$req->status]['err'] . '
				</span>
			';

			return json_encode([
				'err' => $out,
				'status' => $status[$req->status]['err']
			]);

		}
	}

}