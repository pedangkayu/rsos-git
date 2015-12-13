<?php

namespace App\Http\Controllers;

use App\Models\data_feedback;
use App\Models\data_departemen;

use App\Jobs\Feedback\CreateFeedbackJob;
use App\Jobs\Feedback\EditFeedbackJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller {
    
	public function getIndex(){
		$items = data_feedback::me()->paginate(10);
		return view('Feedback.Index', [
			'items' => $items
		]);
	}

	public function getAllfeedback(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';
			$items = data_feedback::me($req->all())->paginate($req->limit);
			if($items->total() > 0){
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){
					$status = $item->status == 1 ? 'Open' : 'Closed';
					$out .= '
						<tr>
							<td>' . $no . '</td>
							<td>
								<a href="' . url('/feedback/jawab/' . $item->id_feedback) . '">#' . $item->kode . '</a>
							</td>
							<td>
								<a href="' . url('/feedback/jawab/' . $item->id_feedback) . '">' . $item->title . '</a>
							</td>
							<td>' . $status . '</td>
							<td>
								' . \Format::indoDate($item->created_at) . '<br />
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
							</td>
							<td>' . $item->nm_depan . '</td>
							<td class="text-right"><span class="badge">' . $item->notif . '</span></td>
						</tr>
					';
					$no++;
				}
			}else{
				$out = '
					<tr>
						<td colspan="7">Tidak ditemukan</td>
					</tr>
				';
			}

			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);
		}
	}

	public function postIndex(Request $req){

		$arr = $this->dispatch(new CreateFeedbackJob($req->all()));

		return redirect()->back()->withNotif([
			'label' => $arr['label'],
			'err' => $arr['err']
		]);
	}

	public function getJawab($id = 0){
		if(empty($id))
			return abort(404);

		$feed = data_feedback::byid($id)->first();

		if($feed == null)
			return abort(404);

		if($feed->id_karyawan == \Me::data()->id_karyawan)
			data_feedback::find($id)->update([
				'notif' => 0
			]);
		$id_karyawan = \Me::data()->id_karyawan;
		$items = data_feedback::comment($feed->id_feedback)->get();
		return view('Feedback.Jawab', [
			'feed' => $feed,
			'items' => $items,
			'id_karyawan' => $id_karyawan
		]);
	}

	public function postJawab(Request $req){

		$arr = $this->dispatch(new CreateFeedbackJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => $arr['label'],
			'err' => 'Komentar terkirim'
		]);
	}

	public function postStatus(Request $req){
		if($req->ajax()){
			data_feedback::find($req->id)->update([
				'status' => $req->val
			]);

			return json_encode([
				'result' => true
			]);
		}
	}


	public function postDel(Request $req){
		if($req->ajax()){
			data_feedback::find($req->id)->update([
				'status' => 0
			]);

			return json_encode([
				'result' => true
			]);
		}
	}

	public function getDev(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '<option value="0">Semua</option>';
			$items = data_departemen::where('status', 1)->get();
			foreach($items as $item){
				$out .= '<option value="' . $item->id_departemen . '">' . $item->nm_departemen . '</option>';
			}
			$res['content'] = $out;
			return json_encode($res);
		}
	}

	public function getEdit($id = 0){
		if(empty($id))
			return abort(404);

		$feed = data_feedback::find($id);

		if($feed == null)
			return abort(404);

		return view('Feedback.Edit', [
			'feed' => $feed
		]);
	}

	public function postEdit(Request $req){
		$arr = $this->dispatch(new EditFeedbackJob($req->all()));

		return redirect()->back()->withNotif([
			'label' => $arr['label'],
			'err' => $arr['err']
		]);
	}

	public function postDelcommen(Request $req){
		if($req->ajax()){
			$item = data_feedback::find($req->id);

			if(!empty($item->file)){
				$file = public_path() . '/img/feedback/' . $item->file;
				if(file_exists($file))
                	@unlink($file);
			}

			$item->delete();

			return json_encode([
				'id' => $req->id
			]);
		}
	}

}
