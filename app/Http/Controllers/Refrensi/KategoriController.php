<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_kategori;


class KategoriController extends Controller
{
        public function __construct(){
    $this->middleware('auth');
  }

    /**
    * Ref Klasifikasi Satuan
    * @access protected
    * @author yoga@valdlabs.com
    */

    public function getIndex(){
      $items = ref_kategori::paginate(10);

      return view('Pengadaan.Setting.Kategori.index',[
        'items' => $items
        ]);
    }

    public function getCreate(){
      return view('Pengadaan.Setting.Kategori.create');
    }

    public function postCreate(Request $req){


      ref_kategori::firstOrCreate(array(
        'nm_kategori' => $req->nama,
        'alias' => $req->alias
        ));

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function getUpdate($id){

      $data = ref_kategori::find($id);

      return view('Pengadaan.Setting.Kategori.update',[
        'data' => $data
        ]);

    }

    public function postUpdate(Request $req){
      ref_kategori::where('id_kategori',$req->id)
      ->update([
        'nm_kategori' => $req->nama,
        'alias' => $req->alias
        ]);

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function postDestroy(Request $req){

      ref_kategori::find($req->id)->delete();

      return json_encode([
        'result' => true
        ]);
    }
}
