<?php

namespace App\Http\Controllers\Personalia;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\Personalia\KaryawanJob as InsertKaryawan;
use App\Jobs\Personalia\CreateKeluargaJob as InsertKeluarga;
use App\Jobs\Personalia\UpdateKaryawanJob as UpdateKaryawan;
use App\Jobs\Personalia\UpdateKeluargaJob as UpdateKeluarga;

use App\Events\Users\UploadAvatarEvent;

use App\Models\data_karyawan;
use App\Models\data_karyawan_klrg;
use App\Models\ref_jabatan;
use App\Models\ref_agama;
use App\Models\data_personalia;
use App\Models\data_departemen;

class KaryawanController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	* Daftar Master Karyawan
	* @access protected
	* @author yoga@valdlabs.com
	*/

	public function getIndex(){
		
		$items = data_karyawan::details()->paginate(10);
		$pending = data_karyawan::where('id_status',15)->count();
		
		return view('Personalia.Karyawan.MasterKaryawan',[
			'items' => $items,
			'pending' => $pending,
			]);

	}

	public function getAdd(){

		$ref_jabatan 	= ref_jabatan::all();
		$ref_agama 		= ref_agama::all();

		return view('Personalia.Karyawan.CreateKaryawan',[
			'ref_jabatan' => $ref_jabatan,
			'ref_agama' => $ref_agama,
			'departemen' => data_departemen::all()
			]);
	}

	public function postAdd(Request $req){

		$data = $this->dispatch(new InsertKaryawan($req->all()));

		\Session::put('id_karyawan',$data->id_karyawan);


		return redirect('/karyawan/update/'.$data->id_karyawan)->withNotif([
			'label' => 'success',
			'err' => $req->nm_depan . ' berhasil tersimpan di Database'
			]);

	}

	public function postKeluarga(Request $req){

		$data = $this->dispatch(new InsertKeluarga($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $req->nm_depan . ' berhasil tersimpan di Database'
			]);

	}

	public function getUpdate($id){

		$karyawan = data_karyawan::find($id);
		
		return view('Personalia.Karyawan.UpdateKaryawan',[
			'karyawan' => $karyawan,
			'ref_jabatan' => ref_jabatan::all(),
			'ref_agama' => ref_agama::all(),
			'departemen' => data_departemen::all()
			]);
		
	}

	public function postUpdate(Request $req){
		
		$this->dispatch(new UpdateKaryawan($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $req->nm_depan . ' berhasil terupdate di Database'
			]);
	}

	public function postUpdatekeluarga(Request $req){

		$this->dispatch(new UpdateKeluarga($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $req->nm_depan . ' berhasil terupdate di Database'
			]);
	}

	public function getReview($id){

		$karyawan = data_karyawan::find($id);
		$keluarga = data_karyawan_klrg::where('id_karyawan',$karyawan->id_karyawan)->get();

		$status_karyawan = data_personalia::join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',1)
		->where('id_karyawan',$karyawan->id_karyawan)
		->get();


		$catatan = data_personalia::join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',3)
		->orWhere('tipe_status',4)
		->orWhere('tipe_status',5)
		->where('id_karyawan',$karyawan->id_karyawan)
		->get();

		$jabatan = ref_jabatan::where('id',$karyawan->jabatan)->first();
		$agama   = ref_agama::where('id',$karyawan->agama)->first();

		return view('Personalia.Karyawan.ReviewKaryawan',[
			'karyawan' => $karyawan,
			'keluarga' => $keluarga,
			'status_karyawan' => $status_karyawan,
			'jabatan' => $jabatan,
			'agama' => $agama,
			'catatan' => $catatan
			]);

	}

	public function postDestroy(Request $req){

		data_karyawan::find($req->id)->update([
			'id_status' => 0
			]);

		return json_encode([
			'result' => true
			]);
	}

	public function getKeluarga($id = null){
		
		$keluarga = data_karyawan_klrg::where('id_karyawan',$id)->get();

		return view('Personalia.Karyawan.MasterKeluarga',[
			'keluarga' => $keluarga,
			'id' => $id 
			]);
	}
	
	public function getUpdatekeluarga(Request $req){
		
		$keluarga = data_karyawan_klrg::where('id',$req->id)->first();
		$data = data_karyawan_klrg::where('id_karyawan',$keluarga->id_karyawan)->get();
		

		return view('Personalia.Karyawan.UpdateKeluarga',[
			'keluarga' => $keluarga,
			'data' => $data
			]);		
	}



	public function getPending(){

		$items = data_karyawan::where('id_status',15)
		->paginate(10);

		return view('Personalia.Karyawan.Pending',[
			'items' => $items
			]);

	}

	public function getPhoto($id){
		return view('Personalia.Karyawan.Photo',[
			'id_karyawan' => $id,
			]);
	}

	public function postPhoto(UploadAvatarEvent $file, Request $req){
		$protect = [
		'avatar1.png',
		'avatar2.png',
		'avatar3.png',
		'avatar4.png',
		'avatar5.png',
		'avatar6.png',
		'avatar7.png',
		'avatar8.png',
		'avatar9.png',
		'avatar10.png',
		'avatar11.png',
		'avatar12.png',
		];

		$avatar = $req->avatar == null ? $protect[0] : $req->avatar;
		
		if(!empty($_FILES['image']['tmp_name'])){
			$avatar = $file->save($_FILES['image']['tmp_name'], [
				'x' => $req->x,
				'y' => $req->y,
				'w' => $req->w,
				'h' => $req->h,
				'r' => $req->r
				]);
		}
		if(!in_array(\Auth::user()->avatar, $protect)){
			$file->rm(\Auth::user()->avatar);
		}

		$user = data_karyawan::find($req->id)->update([
			'foto' => $avatar
			]);

		return redirect()
		->back()
		->withNotif([
			'label' => 'success',
			'err' => 'Avatar berhasil diperbaharui'
			]);
	}

	function getPrint($id){
		$karyawan = data_karyawan::find($id);
		$keluarga = data_karyawan_klrg::where('id_karyawan',$karyawan->id_karyawan)->get();

		$status_karyawan = data_personalia::join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',1)
		->where('id_karyawan',$karyawan->id_karyawan)
		->get();


		$catatan = data_personalia::join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',3)
		->orWhere('tipe_status',4)
		->orWhere('tipe_status',5)
		->where('id_karyawan',$karyawan->id_karyawan)
		->get();

		$jabatan = ref_jabatan::where('id',$karyawan->jabatan)->first();
		$agama   = ref_agama::where('id',$karyawan->agama)->first();

		return view('Print.Personalia.karyawan',[
			'karyawan' => $karyawan,
			'keluarga' => $keluarga,
			'status_karyawan' => $status_karyawan,
			'jabatan' => $jabatan,
			'agama' => $agama,
			'catatan' => $catatan
			]); 
	}

	public function getAllitems(Request $req){
	
    if($req->ajax()):
      $res = [];

      $items = data_karyawan::details()
      ->where('NIK','like',$req->kode."%")
      ->where('nm_depan','like',$req->src."%")
      ->paginate(10);

      $out = '';
      if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){

          $out .= '
            <tr class="item_' .  $item->id . ' items">
              <td>' . $no . '</td>
              <td>
              <a href="javascript:;" title="' . $item->nm_depan . '" data-toggle="tooltip" data-placement="bottom">' . $item->nm_depan . ' '. $item->nm_belakang .'</a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="'. url('karyawan/review/'. $item->id_karyawan) .'">Lihat</a>
										| <a href="' . url('karyawan/review/'. $item->id_karyawan). '">Detail</a>
										
									]</small>
								</div>
              </td>
              <td>'. $item->NIK .'</td>
              <td>'. $item->nm_jabatan .'</td>           
              <td>
                <div>
                  ' . \Format::indoDate($item->created_at) . '
                </div>
                <small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
              </td>
              <td>'. $item->nm_status .'</td>
            </tr>
          ';
          $no++;
        }
      }else{
        $out = '
          <tr>
            <td colspan="4">Tidak ditemukan</td>
          </tr>
        ';
      }

      $res['data'] = $out;
      $res['pagin'] = $items->render();

      return json_encode($res);

      endif;
  }

}
