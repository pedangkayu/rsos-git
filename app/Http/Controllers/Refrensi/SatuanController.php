<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_satuan;

class SatuanController extends Controller
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
      $items = ref_satuan::paginate(10);

      return view('Pengadaan.Setting.Satuan.index',[
        'items' => $items
        ]);
    }

    public function getCreate(){
      return view('Pengadaan.Setting.Satuan.create');
    }

    public function postCreate(Request $req){


      ref_satuan::firstOrCreate(array(
        'nm_satuan' => $req->nama,
        'satuan' => $req->satuan
        ));

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function getUpdate($id){

      $data = ref_satuan::find($id);

      return view('Pengadaan.Setting.Satuan.update',[
        'data' => $data
        ]);

    }

    public function postUpdate(Request $req){
      ref_satuan::where('id_satuan',$req->id)
      ->update([
        'nm_satuan' => $req->nama,
        'satuan' => $req->satuan
        ]);

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function postDestroy(Request $req){

      ref_satuan::find($req->id)->delete();

      return json_encode([
        'result' => true
        ]);
    }

     public function getAllitems(Request $req){
    if($req->ajax()):
      $res = [];

      $items = ref_satuan::where('satuan','like',$req->src.'%')
      ->paginate(10);   

      $out = '';
      if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){

          $out .= '
            <tr class="item_' .  $item->id . ' items">
              <td>' . $no . '</td>
              <td>'. $item->nm_satuan .'</td>
              <td>
                <a href="javascript:;" title="' . $item->id_satuan . '" data-toggle="tooltip" data-placement="bottom">' . $item->satuan . '</a>
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
