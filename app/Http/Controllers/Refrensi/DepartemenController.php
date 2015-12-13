<?php

namespace App\Http\Controllers\Refrensi;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\data_departemen;

class DepartemenController extends Controller
{
  public function __construct(){
    $this->middleware('auth');
  }

    /**
    * Data Departemen
    * @access protected
    * @author yoga@valdlabs.com
    */

    public function getIndex(){
      $items = data_departemen::paginate(10);

      return view('Personalia.Setting.Departemen.index',[
        'items' => $items
        ]);
    }

    public function getCreate(){
      return view('Personalia.Setting.Departemen.create');
    }

    public function postCreate(Request $req){


      data_departemen::firstOrCreate(array(
        'nm_departemen' => $req->nama,
        'kd_departemen' => $req->kd_departemen
        ));

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function getUpdate($id){

      $data = data_departemen::find($id);

      return view('Personalia.Setting.Departemen.update',[
        'data' => $data
        ]);

    }

    public function postUpdate(Request $req){
      data_departemen::where('id_departemen',$req->id)
      ->update([
        'nm_departemen' => $req->nama,
        'kd_departemen' => $req->kd_departemen
        ]);

      return redirect()->back()->withNotif([
        'label' => 'success',
        'err' => 'Berhasil terupdate di Database'
        ]);
    }

    public function postDestroy(Request $req){

      data_departemen::find($req->id)->delete();

      return json_encode([
        'result' => true
        ]);
    }

    public function getAllitems(Request $req){
      if($req->ajax()):
        $res = [];

      $items = data_departemen::where('nm_departemen','like',$req->src.'%')
      ->paginate(10);   

      $out = '';
      if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){

          $out .= '
          <tr class="item_' .  $item->id . ' items">
            <td>' . $no . '</td>
            <td>'.$item->kd_departemen.'</td>
            <td>'. $item->nm_departemen .'</td>
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
