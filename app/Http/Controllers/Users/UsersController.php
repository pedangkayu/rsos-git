<?php

namespace App\Http\Controllers\Users;

use App\User;
use App\Models\data_karyawan;
use App\Models\data_level_user;
use App\Models\data_level;

use App\Jobs\Users\editUserJob;
use App\Jobs\Users\CreateUserJob;

use Illuminate\Http\Request;

use App\Events\Users\UploadAvatarEvent;

use App\Http\Requests;
use App\Http\Requests\Users\AddUserRequest;

use App\Http\Controllers\Controller;

class UsersController extends Controller{
    
	public function __construct(){
		$this->middleware('auth');
	}
	/* Daftar Pemgguna */
	public function getIndex(){
		$src = empty($_GET['src']) ? '' : $_GET['src'];
		$users = User::listusers($src)
			->paginate(10);

		$permission = [
			1 => 'Read',
			2 => 'Write',
			3 => 'Execute'
		];
		return view('Users.ListUsers', [
			'users' => $users,
			'permission' => $permission,
			'src' => $src
		]);
	}

	/* Tambah Pnegguna */
	public function getAdd(){
		$karyawan = data_karyawan::leftJoin('users', 'users.id_karyawan', '=', 'data_karyawan.id_karyawan')
			->whereNull('users.id_user')
			->select('data_karyawan.id_karyawan', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang')
			->get();
		$levels = data_level_user::whereStatus(1)->get();
		return view('Users.AddUsers', [
			'stafs' => $karyawan,
			'levels' => $levels
		]);
	}

	public function postAdd(AddUserRequest $req){
		$user = $this->dispatch(new CreateUserJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => $user->name . ' berhasil ditambahkan sebagai Pengguna <a href="' . url('/users') . '" class="btn btn-danger btn-mini pull-right btn-xs"><b>Lihat daftar</b></a>'
		]);
	}

	public function getEdit($id){

		if(\Auth::user()->id_user == $id || \Auth::user()->permission < 2)
			return redirect('/');

		$user = User::find($id);
		$levels = data_level_user::whereStatus(1)->get();
		$levelss = [];
		foreach (data_level::whereId_user($id)->get() as $val) {
			$levelss[] = $val->id_level_user;
		}
		return view('Users.EditUser', [
			'user' => $user,
			'levels' => $levels,
			'lev' => $levelss
		]);
	}

	public function postEdit(Request $req){

		if($req->username != $req->first_username){
			$this->validate($req, [
				'karyawan'  => 'required',
	            'username'  => 'required|unique:users',
	            'password'  => 'required',
	            'levels'    => 'required'
			]);
		}else{
			$this->validate($req, [
				'karyawan'  => 'required',
	            'levels'    => 'required'
			]);
		}

		$this->dispatch(new editUserJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Pengguna berhasil diperbaharui'
		]);
	}

	public function postDeluser(Request $req){
		$result = [];

		if($req->ajax()):
			User::find($req->id)->delete();
			data_level::whereId_user($req->id)->delete();
			$result['result'] 	= true;
		else:
			$result['result'] 	= false;
			$result['err'] 		= 500;
		endif;

		return json_encode($result);
	}

	public function getAvatar(){

		return view('Users.Avatar');
	}

	public function postAvatar(UploadAvatarEvent $file, Request $req){
		
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

		$user = User::find(\Auth::user()->id_user)->update([
			'avatar' => $avatar
		]);

		return redirect()
			->back()
			->withNotif([
				'label' => 'success',
				'err' => 'Avatar berhasil diperbaharui'
			]);

	}

	public function getAccount(){
		$levels = data_level::me(\Auth::user()->id_user)->get();
		$permission = [
			1 => 'Read',
			2 => 'Write',
			3 => 'Execute'
		];
		return view('Users.Acount', [
			'levels' => $levels,
			'permission' => $permission
		]);
	}

	public function postAccount(Request $req){

		$user = User::find(\Auth::user()->id_user);

		if($req->username != $req->last_username || $req->password != ""){
			
			if($req->username != $req->last_username){
				$this->validate($req, [
					'name'		=> 'required',
					'username' 	=> 'required|unique:users',
					'password' 	=> 'required|confirmed'
				]);
			}else{
				$this->validate($req, [
					'name'		=> 'required',
					'password' 	=> 'required|confirmed'
				]);
			}
			
			$user->name = $req->name;
			$user->status_user = $req->status_user;
			$user->username = $req->username;
			$user->password = bcrypt($req->password);
		}else{
			$this->validate($req, [
				'name'		=> 'required'
			]);

			$user->name = $req->name;
			$user->status_user = $req->status_user;
		}

		$user->save();
		return redirect()
			->back()
			->withNotif([
				'label' => 'success',
				'err' => 'Account berhasil diperbaharui'
			]);
	}
}
