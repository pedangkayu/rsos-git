<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ref_jabatan;

class JabatanController extends Controller
{
 public function __construct(){
    $this->middleware('auth');
}

    /**
    * Data Jabatan
    * @access protected
    * @author yoga@valdlabs.com
    */

    public function getIndex(){
      $items = ref_jabatan::paginate(10);

      return view('Personalia.Setting.Jabatan.index',[
        'items' => $items
        ]);
  }

  public function getCreate(){
      return view('Personalia.Setting.Jabatan.create');
  }

  public function postCreate(Request $req){


      ref_jabatan::firstOrCreate(array(
        'nm_jabatan' => $req->nama
        ));

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
  }

  public function getUpdate($id){

      $data = ref_jabatan::find($id);

      return view('Personalia.Setting.Jabatan.update',[
        'data' => $data
        ]);

  }

  public function postUpdate(Request $req){
      ref_jabatan::where('id',$req->id)
      ->update([
        'nm_jabatan' => $req->nama
        ]);

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
  }

  public function postDestroy(Request $req){
        
      ref_jabatan::find($req->id)->delete();

      return json_encode([
        'result' => true
        ]);
  }

  public function getAllitems(Request $req){
      if($req->ajax()):
        $res = [];

    $items = ref_jabatan::where('nm_jabatan',$req->src)
    ->paginate(10);   

    $out = '';
    if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){

          $out .= '
          <tr class="item_' .  $item->id . ' items">
            <td>' . $no . '</td>
            <td>'. $item->nm_jabatan .'</td>
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
      <td colspan="3">Tidak ditemukan</td>
  </tr>
  ';
}

$res['data'] = $out;
$res['pagin'] = $items->render();

return json_encode($res);

endif;
}   
}
