<?php

namespace App\Http\Controllers\Menus;

use App\Models\data_menu;
use App\Models\data_menu_user;
use App\Models\data_level_user;

use App\Jobs\Menus\CreateMenuJob;
use App\Jobs\Menus\UpdateMenuJob;
use App\Jobs\Menus\SavePositionMenuJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MenusController extends Controller{
    
	public function getAdd(){

		$menu = data_menu::whereStatus(1)->whereParent_id(0)->get();
		return view('Menus.show', [
			'parent' => $menu
		]);

	}

	public function postAdd(Request $req){
		$this->dispatch(new CreateMenuJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Menu Berhasil dibuat	'
		]);
	}

	public function getEdit($id = 0){

		if(empty($id))
			return redirect()->back();

		$menu = data_menu::whereStatus(1)
			->whereParent_id(0)
			->get();

		$edit = data_menu::find($id);
		return view('Menus.show', [
			'parent' 	=> $menu,
			'menu' 		=> $edit
		]);
	}

	public function postUpdate(Request $req){
		$this->dispatch(
			new UpdateMenuJob($req->all())
		);

		if(!empty($req->del))
			return redirect()->back()->withNotif([
				'label' => 'success',
				'err' => 'Menu berhasil diperbaharui'
			]);
		else
			return redirect('/menu/add')->withNotif([
				'label' => 'success',
				'err' => 'Menu berhasil diperbaharui'
			]);



	}

	public function postSaveposition(Request $req){
		$save = $this->dispatch(
			new SavePositionMenuJob($req->all())
		);
		
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Posisi Menu berhasil diperbaharui'
		]);
	}

	// Hak akses management
	public function getAccess($id = 0){
		return view('Menus.Access', [
			'level' 	=> data_level_user::whereStatus(1)->get(),
			'id' 		=> $id
		]);
	}

	public function postSaveaccessmenu(Request $req){
		
		data_menu_user::where('id_level', $req->id_level)->delete();
		foreach($req->id_menu as $id){
			data_menu_user::create([
				'id_menu' => $id,
				'id_level' => $req->id_level
			]);
		}

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Menu berhasil diperbaharui'
		]);

	}

}
