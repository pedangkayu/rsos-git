<?php

namespace App\Http\Controllers\auth;

use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LockScreenController extends Controller{

	public function __construct(){
		$this->middleware('auth');
	}

    /**
	 * Halaman kondisi dimana user sedang tidak aktif
	 * @return Response
	 */

	public function getIndex(){
		session()->put('lock', 'lock');

		User::find(\Auth::user()->id_user)->update([
			'time_online' => 0
		]);
		
		\Loguser::create('Diam');

		return view('auth.Lock');
	}

	public function postIndex(Request $req){
		$username = \Auth::user()->username;
		if (\Auth::attempt(['username' => $username, 'password' => $req->password])) {
			$req->session()->forget('lock');
            return redirect('/');
        }
        return redirect()->back()->withErr('* Kata sandi tidak cocok!');
	}

}
