<?php

namespace App\Http\Controllers\Personalia;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\Recruitment\CreateRecruitmentJob as InsertRecruitment;
use App\Jobs\Recruitment\UpdateRecruitmentJob as UpdateRecruitment;

use App\Models\data_recruitment;
use App\Models\ref_jabatan;
use App\Models\ref_agama;

class RecruitmentController extends Controller
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

    $items = data_recruitment::where('status',1)->paginate(10);
    
    return view('Recruitment.index',[
      'items' => $items,
      ]);
  }

  public function getCreate(){
    
    return view('Recruitment.create',[
      'ref_jabatan' => ref_jabatan::all(),
      'ref_agama' => ref_agama::all()
      ]);
  }

  public function postCreate(Request $req){
   $data = $this->dispatch(new InsertRecruitment($req->all()));

   return redirect()->back()->withNotif([
      'label' => 'success',
      'err' => $req->posisi . ' berhasil tersimpan di Database'
    ]);
 }

 public function getUpdate(Request $req){

  $data = data_recruitment::find($req->id);


   return view('Recruitment.edit',[
    'data' => $data
    ]);
 }

 public function postUpdate(Request $req){
   $data = $this->dispatch(new UpdateRecruitment($req->all()));

   return redirect()->back()->withNotif([
      'label' => 'success',
      'err' => $req->posisi . ' berhasil terupdate di Database'
    ]);
 }

 public function getDetail(Request $req){
  $data = data_recruitment::find($req->id);

  return view('Recruitment.detail',[
      'data' => $data
    ]);
 }

 public function postDestroy(Request $req){

    data_recruitment::find($req->id)->update([
      'status' => 0
    ]);

    return json_encode([
      'result' => true
    ]);
  }

  public function getAllitems(Request $req){
  
    if($req->ajax()):
      $res = [];

      $items = data_recruitment::where('posisi','like',$req->src."%")
      ->paginate(10);

      $out = '';
      if($items->total() > 0){
        $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
        foreach($items as $item){
         if($item->status == 1){  $status = "Aktif"; }else{ $status ="Tidak Aktif"; }
          $out .= '
            <tr class="item_' .  $item->id . ' items">
              <td>' . $no . '</td>
              <td>
              <a href="javascript:;" title="' . $item->posisi . '" data-toggle="tooltip" data-placement="bottom">' . $item->posisi . '</a>
                <div style="display:none;" class="tbl-opsi">
                  <small>[
                    <a href="'. url('recruitment/detail/'. $item->id) .'">Lihat</a>
                    | <a href="' . url('recruitment/update/'. $item->id). '">Edit</a>
                    
                  ]</small>
                </div>
              </td>
              <td>
              <div>' . \Format::indoDate($item->date_open) .' s/d '.\Format::indoDate($item->date_close).'</div>
                    <small class="text-muted">' . \Format::hari($item->date_open) . ', ' . \Format::jam($item->date_open) . '</small>
              </td>
              <td>'. $item->estimasi_gaji .'</td>           
             
              <td>'. $status .'</td>
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
