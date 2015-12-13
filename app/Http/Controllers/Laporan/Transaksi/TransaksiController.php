<?php

namespace App\Http\Controllers\Laporan\Transaksi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\data_spb;
use App\Models\data_skb;	
use App\Models\data_spbm;	

class TransaksiController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function getSpb()
	{
		return view('Laporan.Transaksi.SPB');
	}

	public function getSpbajax(Request $req)
	{
		if($req->ajax()){
			$res = [];
			$out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
		//	$items = data_spb::rekapspb($req->all())->get();
			$items = data_spb::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_spb_item.id_item')
					  ->join('ref_satuan','data_barang.id_satuan','=','ref_satuan.id_satuan');
			}])->rekapspb($req->all())->get();
			$total = count($items);
			if($total > 0){

				$no = 1;
				$id_spb = '';
				foreach ($items as $item) {
					if($id_spb != $item->id_spb){
						$out .= '<tr style="background-color: #d3d3d3">
						<td class="text-left">'. $item->no_spb .'</td>
						<td class="text-left">' . $item->nm_depan . ' ' . $item->nm_belakang . '</td>
						<td class="text-left">' . $item->nm_departemen . '</td>
						<td class="text-left">' . \Format::indoDate2($item->deadline). '</td>
						<td class="text-left">' . \Format::indoDate2($item->created_at). '</td>
						<td colspan="3"></td>
					</tr>';
				}
				foreach($item->rekap as $data){
					$out .= '
					<tr>

						<td colspan="5"></td>
						<td class="text-left">' . $data->nm_barang . '</td>
						<td class="text-left">' . $data->nm_satuan . '</td>
						<td class="text-left">' . $data->qty . '</td>
					</tr>
					';
					$no++;
				}
			}

		}else{
			$out = '
			<tr>
				<td colspan="8">Tidak ditemukan</td>
			</tr>
			';
		}

		$res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

		return json_encode($res);
	}
}

public function getSkb()
{
	return view('Laporan.Transaksi.SKB');
}

public function getSkbajax(Request $req)
{
	if($req->ajax()){
		$res = [];
		$out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
		$items = data_skb::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_skb_item.id_item')
				->join('ref_satuan','data_barang.id_satuan','=','ref_satuan.id_satuan');
			}])->rekapskb($req->all())->get();
		$total = count($items);
		if($total > 0){

			$no = 1;
			$id_skb = '';
				foreach ($items as $item) {
					if($id_skb != $item->id_spb){
						$out .= '<tr style="background-color: #d3d3d3">
						<td class="text-left">'. $item->no_skb .'</td>
						<td class="text-left">' . $item->nm_depan . ' ' . $item->nm_belakang . '</td>
						<td class="text-left">' . $item->nm_departemen . '</td>
						<td class="text-left">' . \Format::indoDate2($item->created_at). '</td>
						<td colspan="6"></td>
					</tr>';
				}
				foreach($item->rekap as $data){
					$out .= '
					<tr>

						<td colspan="4"></td>
						<td class="text-left">' . $data->nm_barang . '</td>
						<td class="text-right">' . $data->qty . '</td>
						<td class="text-right">' . $data->qty_lg . '</td>
						<td class="text-right">' . $data->sisa . '</td>
						<td class="text-left">' . $data->nm_satuan . '</td>
					</tr>
					';
					$no++;
				}
			}

		}else{
			$out = '
			<tr>
				<td colspan="8">Tidak ditemukan</td>
			</tr>
			';
		}

		$res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

		return json_encode($res);
	}
}

public function getGr()
{
	return view('Laporan.Transaksi.GR');
}

public function getGrajax(Request $req)
{
	if($req->ajax()){
		$res = [];
		$out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
		$items = data_spbm::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_spbm_item.id_barang')
					  ->join('ref_satuan','data_barang.id_satuan','=','ref_satuan.id_satuan');
			}])->rekapspbm($req->all())->get();
		$total = count($items);
		if($total > 0){

			$no = 1;
			$id_spbm = '';
				foreach ($items as $item) {
					if($id_spbm != $item->id_spbm){
						$out .= '<tr style="background-color: #d3d3d3">
						<td class="text-left">'. $item->no_spbm .'</td>
						<td class="text-left">' . $item->no_surat_jalan . '</td>
						<td class="text-left"></td>
						<td class="text-left"></td>
						<td colspan="8"></td>
					</tr>';
				}
				foreach($item->rekap as $data){
					$out .= '
					<tr>

						<td colspan="3"></td>
						<td class="text-left">'. $data->kode .'</td>
						<td class="text-left">' . $data->nm_barang . '</td>
						<td class="text-left">' . $data->merek . '</td>
						<td class="text-left">' . $data->qty_lg . '</td>
						<td class="text-left">' . $data->qty . '</td>
						<td class="text-left">' . $data->sisa . '</td>
						<td class="text-left">' . $data->bonus . '</td>
						<td class="text-left"></td>
						<td class="text-left">' . $data->satuan . '</td>
					</tr>
					';
					$no++;
				}
			}

		}else{
			$out = '
			<tr>
				<td colspan="8">Tidak ditemukan</td>
			</tr>
			';
		}

		$res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

		return json_encode($res);
	}
}

public function getRetur()
{
	return view('Laporan.Transaksi.retur');
}

public function postRetur(Request $req)
{
	if($req->ajax()){
		$res = [];
		$out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
		$items = data_po_item::rekapprodusesn($req->all())->get();
		$total = count($items);
		if($total > 0){

			$no = 1;
			foreach ($items as $item) {
				$out .= '
				<tr>
					<td>' . $no . '</td>
					<td>' . $item->nm_vendor . '</td>
					<td class="text-right">' . number_format($item->total,0,',','.') . '</td>
					<td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
					<td class="text-right"></td>
					<td class="text-right"></td>
					<td class="text-right">' . number_format($item->total,0,',','.') . '</td>
					<td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
				</tr>
				';
				$no++;
			}

		}else{
			$out = '
			<tr>
				<td colspan="8">Tidak ditemukan</td>
			</tr>
			';
		}

		$res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

		return json_encode($res);
	}
}

public function getPrintspb(Request $req)
{
	$items = data_spb::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_spb_item.id_item')
					  ->join('ref_satuan','data_barang.id_satuan','=','ref_satuan.id_satuan');
			}])->rekapspb($req->all())->get();

	return view('Print.Transaksi.SPB',[
		'items' => $items,
		'req' 	=> $req
		]);
}

public function getPrintskb(Request $req)
{
	$items = data_skb::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_skb_item.id_item');
			}])->rekapskb($req->all())->get();


	return view('Print.Transaksi.SKB',[
		'items' => $items,
		'req' => $req
		]);
}

public function getPrintgr(Request $req)
{
	$items = data_spbm::with(['rekap'=>function($query){
				$query->join('data_barang','data_barang.id_barang','=','data_spbm_item.id_barang')
					  ->join('ref_satuan','data_barang.id_satuan','=','ref_satuan.id_satuan');
			}])->rekapspbm($req->all())->get();

	return view('Print.Transaksi.GR',[
		'items' => $items,
		'req' => $req
		]);
}
}
