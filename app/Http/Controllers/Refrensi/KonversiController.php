<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_konversi_satuan;
use App\Models\ref_satuan;

class KonversiController extends Controller
{
    public function __construct(){
		$this->middleware('auth');
	}

	/**
	* Ref Konversi Satuan
	* @access protected
	* @author yoga@valdlabs.com
	*/

	public function getIndex(){
		$items = ref_konversi_satuan::join('ref_satuan AS a','a.id_satuan','=','ref_konversi_satuan.id_satuan_max')
		->join('ref_satuan AS b','b.id_satuan','=','ref_konversi_satuan.id_satuan_min')
		->join('data_barang','data_barang.id_barang','=','ref_konversi_satuan.id_barang')
		->select('data_barang.nm_barang','a.nm_satuan as satuan_max','b.nm_satuan as satuan_min','ref_konversi_satuan.qty','ref_konversi_satuan.id','ref_konversi_satuan.created_at')
		->paginate(10);

		return view('Pengadaan.Setting.Konversi.index',[
			'items' => $items,
			'satuan' => ref_satuan::all()
			]);
	}

	public function getCreate(){
		return view('Pengadaan.Setting.Konversi.create',[
			'satuan' => ref_satuan::all() 
			]);
	}
	
	public function postCreate(Request $req){
		

		ref_konversi_satuan::firstOrCreate(array(
			'id_satuan_max' => $req->satuan_max,
			'id_satuan_min' => $req->satuan_min,
			'qty' => $req->qty
			));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Berhasil terupdate di Database'
		]);
	}

	public function getUpdate($id){

		$data = ref_konversi_satuan::find($id);

		return view('Pengadaan.Setting.Konversi.update',[
			'data' => $data,
			'satuan' => ref_satuan::all() 
			]);

	}

	public function postUpdate(Request $req){
		ref_konversi_satuan::where('id',$req->id)
		->update([
			'id_satuan_max' => $req->satuan_max,
			'id_satuan_min' => $req->satuan_min,
			'qty' => $req->qty
			]);

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Berhasil terupdate di Database'
		]);
	}

	public function postDestroy(Request $req){

		ref_konversi_satuan::find($req->id)->delete();

		return json_encode([
			'result' => true
		]);
	}

	public function getAllitems(Request $req){
		if($req->ajax()):
			$res = [];

			$items = ref_konversi_satuan::join('ref_satuan AS a','a.id_satuan','=','ref_konversi_satuan.id_satuan_max')
					->join('ref_satuan AS b','b.id_satuan','=','ref_konversi_satuan.id_satuan_min')
					->join('data_barang','data_barang.id_barang','=','ref_konversi_satuan.id_barang')
					->select('data_barang.nm_barang','a.nm_satuan as satuan_max','b.nm_satuan as satuan_min','ref_konversi_satuan.qty','ref_konversi_satuan.id','ref_konversi_satuan.created_at')
					->paginate(10);		

			$out = '';
			if($items->total() > 0){
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){

					$out .= '
						<tr class="item_' .  $item->id . ' items">
							<td>' . $no . '</td>
							<td>'. $item->nm_barang .'</td>
							<td>
								<a href="javascript:;" title="' . $item->id_satuan_max . '" data-toggle="tooltip" data-placement="bottom">' . $item->satuan_max . '</a>
							</td>
							<td>' . $item->satuan_min . '</td>
							<td>'.$item->qty.'</td>							
							<td>
								<div>
									' . \Format::indoDate($item->created_at) . '
								</div>
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
							</td>
						</tr>
					';
					$no++;
				}
			}else{
				$out = '
					<tr>
						<td colspan="6">Tidak ditemukan</td>
					</tr>
				';
			}

			$res['data'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);

			endif;
	}
}
