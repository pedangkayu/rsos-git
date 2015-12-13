<?php

namespace App\Http\Controllers\Personalia;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\MasterStatus\StatusAktifJob as InsertStatusAktif;
use App\Jobs\MasterStatus\StatusTidakHadirJob as InsertKehadiran;
use App\Jobs\MasterStatus\StatusCutiJob as InsertCuti;
use App\Jobs\MasterStatus\StatusKeterlambatanJob as InsertKeterlambatan;
use App\Jobs\MasterStatus\StatusPulangJob as InsertPulang;
use App\Jobs\MasterStatus\StatusSKKJob as InsertSKK;

use App\Models\data_karyawan;
use App\Models\ref_status_karyawan;
use App\Models\data_personalia;

class StatusKaryawanController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	* Daftar Master Karyawan
	* @access protected
	* @author yoga@valdlabs.com
	*/

	/*public function getIndex(){

		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		$aktifasi_karyawan = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',2)->get();

		$aktif = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',1)
		->get();

		$tidak_aktif = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',2)
		->get();

		$cuti = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',3)
		->get();

		
		return view('Personalia.Status.MasterStatus',[
			'data' => $data,
			'status_aktif' => ref_status_karyawan::where('tipe_status',1)->get(),
			'status_tidak_aktif' => ref_status_karyawan::where('tipe_status',2)->get(),
			'cuti' => ref_status_karyawan::where('tipe_status',3)->get(),
			'kehadiran' => ref_status_karyawan::where('tipe_status',4)->get(),
			'skk' => ref_status_karyawan::where('tipe_status',5)->get(),
			'aktifasi_karyawan' => $aktifasi_karyawan,
			'aktif' => $aktif,
			'tidak_aktif' => $tidak_aktif,
			'cuti' => $cuti
			]);

	}
	*/

	public function getIndex(){

		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		$aktif = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',1)
		->get();

		return view('Personalia.Status.status_aktif',[
			'status_aktif' => ref_status_karyawan::where('tipe_status',1)->get(),
			'aktif' => $aktif,
			'data' => $data,
			]);
	}

	public function getAktif($id){

		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		$aktif = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',1)
		->get();

		return view('Personalia.Status.aktifkan',[
			'status_aktif' => ref_status_karyawan::where('tipe_status',1)->get(),
			'aktif' => $aktif,
			'data' => $data,
			'id' => $id
			]);
	}


	public function getTidakaktif(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		$tidak_aktif = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',2)
		->get();

		return view('Personalia.Status.status_tidakaktif',[
			'status_tidak_aktif' => ref_status_karyawan::where('tipe_status',2)->get(),
			'tidak_aktif' => $tidak_aktif,
			'data' => $data,
			]);

	}

	public function getCuti(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		$cuti = data_personalia::join('data_karyawan','data_karyawan.id_karyawan','=','data_personalia.id_karyawan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_personalia.id_status')
		->where('tipe_status',3)
		->get();

		return view('Personalia.Status.status_cuti',[
			'cuti' => ref_status_karyawan::where('tipe_status',3)->get(),
			'data' => $data,
			]);
	}

	public function getKehadiran(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		return view('Personalia.Status.status_kehadiran',[
			'kehadiran' => ref_status_karyawan::where('tipe_status',4)->get(),
			'data' => $data,
			]);
	}

	public function getSkk(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		return view('Personalia.Status.status_skk',[
			'skk' => ref_status_karyawan::where('tipe_status',5)->get(),
			'data' => $data,
			]);

	}

	public function getKeterlambatan(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		return view('Personalia.Status.keterlambatan',[
			'kehadiran' => ref_status_karyawan::where('tipe_status',4)->get(),
			'data' => $data,
			]);
	}

	public function getMeninggalkan(){
		$data = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',1)
		->orWhere('id_status',15)
		->get();

		return view('Personalia.Status.status_meninggalkan',[
			'kehadiran' => ref_status_karyawan::where('tipe_status',4)->get(),
			'data' => $data,
			]);
	}

	public function getPegawaitidakaktif(){
		$aktifasi_karyawan = data_karyawan::join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status',2)->get();

		return view('Personalia.Status.status_pegawai_aktif',[
			'status_aktif' => ref_status_karyawan::where('tipe_status',1)->get(),
			'aktifasi_karyawan' => $aktifasi_karyawan,
			]);
	}


	public function postStatusaktif(Request $req){
		
		$this->dispatch(new InsertStatusAktif($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);
	}

	public function postKehadiran(Request $req){
		$this->dispatch(new InsertKehadiran($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);	
	}

	public function postKeterlambatan(Request $req){
		$this->dispatch(new InsertKeterlambatan($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);	
	}

	public function postCuti(Request $req){

		$this->dispatch(new InsertCuti($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);	
	}

	public function postPulang(Request $req){

		$this->dispatch(new InsertPulang($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);	
	}

	public function postSkk(Request $req){

		$this->dispatch(new InsertSKK($req->all()));

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Data berhasil terupdate di Database'
			]);	
	}
}
