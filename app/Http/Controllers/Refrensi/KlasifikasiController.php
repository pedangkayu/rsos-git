<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_klasifikasi;

class KlasifikasiController extends Controller
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
      $items = ref_klasifikasi::paginate(10);

      return view('Pengadaan.Setting.Klasifikasi.index',[
        'items' => $items
        ]);
    }

    public function getCreate(){
      return view('Pengadaan.Setting.Klasifikasi.create');
    }

    public function postCreate(Request $req){


      ref_klasifikasi::firstOrCreate(array(
        'kode' => $req->kode,
        'nm_klasifikasi' => $req->nama
        ));

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function getUpdate($id){

      $data = ref_klasifikasi::find($id);

      return view('Pengadaan.Setting.Klasifikasi.update',[
        'data' => $data
        ]);

    }

    public function postUpdate(Request $req){
      ref_klasifikasi::where('id_klasifikasi',$req->id)
      ->update([
        'kode' => $req->kode,
        'nm_klasifikasi' => $req->nama
        ]);

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function postDestroy(Request $req){

      ref_klasifikasi::where("id_klasifikasi",$req->id)->delete();

      return json_encode([
        'result' => true
        ]);
    }

    public function getAllitems(Request $req){
    if($req->ajax()):
      $res = [];

      $items = ref_klasifikasi::where('kode',$req->src)
      ->orWhere('nm_klasifikasi',$req->src)
      ->paginate(10);   

      $out = '';
      if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){

          $out .= '
            <tr class="item_' .  $item->id . ' items">
              <td>' . $no . '</td>
              <td>'. $item->kode .'</td>
              <td>
                <a href="javascript:;" title="' . $item->id_klasifikasi . '" data-toggle="tooltip" data-placement="bottom">' . $item->nm_klasifikasi . '</a>
              </td>           
              <td>
                <div>
                  ' . \Format::indoDate($item->created_at) . '
                </div>
                <small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
              </td>
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
