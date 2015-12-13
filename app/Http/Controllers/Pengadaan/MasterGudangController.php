<?php

namespace App\Http\Controllers\Pengadaan;

use App\Models\ref_gudang;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MasterGudangController extends Controller {
    
	public function getIndex(){
		$items = ref_gudang::listgudang()->paginate(10);
		
		return view('Pengadaan.MasterGudang.Index', [
			'items' => $items,
			'gudang' => null
		]);
	}

	public function getAllgudang(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$items = ref_gudang::listgudang($req->all())->paginate($req->limit);
			if($items->total() > 0):
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				$permission = \Auth::user()->permission;
				foreach($items as $item){
					$link = $permission > 2 ? '<small>[<a href="' . url('/gudang/edit/' . $item->id_gudang) . '">Rubah</a> |<a href="javascript:void(0);" onclick="del({{ $item->id_gudang }});" class="text-danger">Hapus</a> ]</small>' : '';
					$out .= '
						<tr class="item-' . $item->id_gudang . '">
							<td>' . $no . '</td>
							<td>
								' . $item->kode_gudang . '<br />
								' . $link . '
							</td>
							<td>
								' . $item->nm_gudang . '
							</td>
							<td>
								' . \Format::indoDate($item->created_at) . '<br />
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
							</td>
							<?php $no++; ?>
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
		}

		$res['content'] = $out;
		$res['pagin'] = $items->render();

		return json_encode($res);
	}

	public function getEdit($id = 0){

		$items = ref_gudang::where('status', 1)->paginate(10);
		$gudang = null;
		if($id > 0 && is_numeric($id))
			$gudang = ref_gudang::find($id);

		return view('Pengadaan.MasterGudang.Index', [
			'items' => $items,
			'gudang' => $gudang
		]);

	}

	public function postEdit(Request $req){
		$this->validate($req, [
			'nm_gudang' => 'required'
		]);	

		ref_gudang::find($req->id_gudang)->update([
			'nm_gudang' => $req->nm_gudang
		]);

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Gudang berhasil diperbaharui'
		]);

	}

	public function postIndex(Request $req){

		$this->validate($req, [
			'kode_gudang' => 'required|unique:ref_gudang',
			'nm_gudang' => 'required'
		]);

		$gd = ref_gudang::create($req->all());
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Gudang berhasil dibuat dengan Kode. ' . $gd->kode_gudang
		]);

	}

	public function postDel(Request $req){
		if($req->ajax()){
			ref_gudang::find($req->id)->update([
				'status' => 0
			]);

			return json_encode([
				'id' => $req->id
			]);
		}
	}

}


