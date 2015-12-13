<?php

namespace App\Http\Controllers\Personalia;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\Recruitment\CreateEmploymentJob as InsertEmployment;
use App\Jobs\Recruitment\UpdateEmploymentJob as UpdateEmployment;

use App\Models\data_recruitment;
use App\Models\ref_agama;
use App\Models\data_employment;
use App\Models\data_employment_portfolio;

class EmploymentController extends Controller
{
	public function getIndex(){
		$items = data_employment::join('data_recruitment','data_employment.id_recruitment','=','data_recruitment.id')
		->where('id_status',1)
		->orWhere('id_status',2)
		->orWhere('id_status',3)
		->paginate(10);

		return view('Employment.index',[
			'items' => $items
			]);

	}

	public function getCreate(Request $req){
		
		$id_recruitment = base64_decode($req->id);

		return view('Employment.create',[
			'id_recruitment' => $id_recruitment,
			'ref_agama' => ref_agama::all()
			]);
	}

	public function postCreate(Request $req){

		$data = $this->dispatch(new InsertEmployment($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $req->nm_depan . ' berhasil tersimpan di Database'
			]);
	}

	public function getList(){
		$items = data_recruitment::paginate(10);

		return view('Employment.list',[
			'items' => $items
			]);
	}

	public function getDetail($id){
		$data = data_employment::join('ref_agama','ref_agama.id','=','data_employment.agama')
		->where('data_employment.id',$id)
		->first();
		$detail = data_employment_portfolio::where('id_employment',$id)->get();

		return view('Employment.detail',[
			'data' => $data,
			'detail' => $detail,
			]);
	}

	public function getLowongan(Request $req){
		$result = [];
		if($req->ajax()):
			$result['result'] = true;
		
		$data =	data_recruitment::find($req->id);

		$html = '
		<div class="grid simple">
			<div class="grid-title no-border"></div>
			<div class="grid-body no-border">
				<div class="row">
					<div class="col-sm-10">
						<strong>Posisi</strong>
						<p>'.$data->posisi.'</p>
						<strong>Tanggal Mulai</strong>
						<p>'.$data->date_open.'</p>
						<strong>Tanggal Selesai</strong>
						<p>'.$data->date_close.'</p>
						<strong>Syarat</strong>
						<p>'.$data->syarat.'</p>
						<strong>Estimasi Gaji</strong>
						<p>'.$data->estimasi_gaji.'</p>
						<strong>Job Desc</strong>
						<p>'.$data->jobdesk.'</p>
						<strong>Catatan</strong>
						<p>'.$data->catatan.'</p>
					</div>
				</div>
			</div>
		</div>
		';
		$result['posisi'] = $data->posisi;
		$result['content'] = $html;
		$result['link'] = '<a href="' . url('employment/create?id='.base64_encode($data->id)) . '" class="btn btn-primary">Apply Job</a>';
		else:
			$result['result'] = false;
		$result['error'] = 'resteric'; 
		endif;
		return json_encode($result);
	}

	public function getUpdate(Request $req){
		$result = [];
		if($req->ajax()):
			$result['result'] = true;
		$data = data_employment::find($req->id);

		$html = '<div class="grid simple">
		<div class="grid-title no-border"></div>
		<div class="grid-body no-border">
			<div class="row">
				<div class="col-sm-10">
					<strong>Nama</strong>
					<p>'.$data->nm_depan.' '.$data->nm_belakang.'</p>
					<strong>Status</strong>
					<p>
						<select name="id_status" required class="form-control">
							<option value="">-Pilih-</option>
							<option value="2">Diterima</option>
							<option value="3">Tidak Diterima</option>
						</select>
					</p>
				</div>
			</div>
		</div>
	</div>';

	$result['content'] = $html;
	$result['link'] = '
	<input type="hidden" name="id" value="'.$data->id.'">
	<input type="hidden" name="_token" value="'. csrf_token() .'">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button class="btn btn-primary" type="submit"> Update </button>
</form>';
else:
	$result['result'] = false;
$result['error'] = 'resteric'; 
endif;


return json_encode($result); 
}

public function postUpdate(Request $req){

	$data = $this->dispatch(new UpdateEmployment($req->all()));

	return redirect()->back()->withNotif([
		'label' => 'success',
		'err' => ' berhasil tersimpan di Database'
		]);
}
public function postDestroy(Request $req){
	data_employment::find($req->id)->update([
			'id_status' => 0
		]);

		return json_encode([
			'result' => true
		]);
}

}
