<?php

namespace App\Http\Controllers\Users;

use App\Models\data_aktivitas;
use App\Models\data_departemen;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogUserController extends Controller {
    
	public function getIndex(){
		$items = data_aktivitas::show()->paginate(10);
		$depts = data_departemen::all();
		return view('Users.Loguser', [
			'items' => $items,
			'depts' => $depts
		]);
	}

	public function getGetlogs(Request $req){

		if($req->ajax()){
			$res = [];
			$out = '';

			$items = data_aktivitas::show($req->all())->paginate($req->limit);

			if($items->total() > 0):
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								' . $item->nm_depan . ' ' . $item->nm_belakang . '
								<div><small class="text-muted">Dept. ' . $item->nm_departemen  . '</small></div>
							</td>
							<td>
								' . \Format::indoDate($item->created_at) . '
								<div><small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
							</td>
							<td>
								<small>' . $item->keterangan . '</small>
							</td>
						</tr>
					';
					$no++;
				}
			else:
				$out = '
					<tr>
						<td colspan="4">Tidak ditemukan</td>
					<tr>
				';
			endif;

			$res['pagin'] = $items->render();
			$res['content'] = $out;

			return json_encode($res);
		}

	}

}
