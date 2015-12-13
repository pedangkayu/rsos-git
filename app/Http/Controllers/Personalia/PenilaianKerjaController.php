<?php

namespace App\Http\Controllers\Personalia;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\Personalia\KinerjaJob as InsertKinerja;

use App\Models\data_penilaian_uraian;
use App\Models\data_kinerja;
use App\Models\data_kinerja_log;
use App\Models\ref_penilaian;
use App\Models\ref_penilaian_uraian;
use App\Models\data_karyawan;
use App\Models\ref_jabatan;
use App\Models\data_departemen;


class PenilaianKerjaController extends Controller
{

  public function __construct(){
    $this->middleware('auth');
  }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @author Yoga | yoga@valdlabs.com
     */
    public function getIndex()
    {   

      return view('Personalia.Penilaian.index',[
       'data' => data_karyawan::all(),
       'jabatans' => ref_jabatan::all(), 
       'departemens' => data_departemen::all(), 

       ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @author Yoga | yoga@valdlabs.com
     */
    public function getDetail(Request $req){
      \Session::put('header', $req->all());

      $items = data_kinerja::join('data_karyawan','data_karyawan.id_karyawan','=','data_kinerja.id_karyawan')
      ->join('ref_jabatan','ref_jabatan.id','=','data_karyawan.jabatan')
      ->where('data_kinerja.id_karyawan',$req->id_karyawan)
      ->select('data_kinerja.id','data_kinerja.created_at','data_karyawan.nm_depan','data_karyawan.nm_belakang','data_karyawan.NIK','ref_jabatan.nm_jabatan')
      ->paginate(10);

      $karyawan = data_karyawan::find($req->id_karyawan);

      return view('Personalia.Penilaian.detail',[
        'items' => $items,
        'nm_karyawan' => $karyawan->nm_karyawan
        ]);
    }

    public function getSection1(Request $req)
    {

      $head = ref_penilaian::with('uraian')
      ->where('section',1)
      ->get();

      return view('Personalia.Penilaian.section1',[
        'jabatans' => ref_jabatan::all(), 
        'head' => $head
        ]);
    }

    public function postSection2(Request $req){
      $uraian = $req->input('data_kuisioner');
      $section1 = \Session::put('section1', $uraian);

      $head = ref_penilaian::with('uraian')
      ->where('section',2)
      ->get();

      return view('Personalia.Penilaian.section2',[
        'head' => $head,
        'jabatans' => ref_jabatan::all(), 
        ]);   
    }

    public function postSection3(Request $req){
      $uraian = $req->input('data_kuisioner');
      $section2 = \Session::put('section2', $uraian);

      $head = ref_penilaian::with('uraian')
      ->where('section',3)
      ->get();

      return view('Personalia.Penilaian.section3',[
        'head' => $head,
        'jabatans' => ref_jabatan::all(), 
        ]);   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     * @author Yoga | yoga@valdlabs.com
     */
    public function postCreate(Request $req)
    {

      $head = session('header');
      $s1 = session('section1');
      $s2 = session('section2');
      $s3 = $req->input('data_kuisioner');
      $kus = array_merge($s1,$s2,$s3);
      $res = array('head' => $head, 'kusioner' => $kus);
      // print_r($s3);
      // exit;
      $data = $this->dispatch(new InsertKinerja($res));


      return redirect('/penilaian/finish/'.$data);
    }

    public function getFinish($id)
    {

     $head = data_kinerja::join('data_karyawan','data_karyawan.id_karyawan','=','data_kinerja.id_karyawan')
      ->join('ref_jabatan','ref_jabatan.id','=','data_karyawan.jabatan')
      ->join('data_departemen','data_departemen.id_departemen','=','data_karyawan.id_departemen')
      ->where('data_kinerja.id',$id)
      ->select('data_kinerja.id','data_kinerja.created_at','data_departemen.nm_departemen','data_karyawan.nm_depan','data_karyawan.nm_belakang','data_karyawan.NIK','ref_jabatan.nm_jabatan')
      ->first();

     $items = data_kinerja::join('data_kinerja_log','data_kinerja_log.id_kinerja','=','data_kinerja.id')
     ->join('ref_penilaian','ref_penilaian.id_penilaian','=','data_kinerja_log.id_penilaian')
     ->join('ref_penilaian_uraian','ref_penilaian_uraian.id','=','data_kinerja_log.id_penilaian_uraian')
     ->where('id_kinerja',$id)
     ->select('ref_penilaian.penilaian','ref_penilaian_uraian.uraian','data_kinerja_log.score','data_kinerja_log.point')
     ->get();
     return view('Personalia.Penilaian.finish',[
      'items' => $items,
      'head' => $head
      ]); 
   }

   public function getChain(Request $req)
   {
    $result = [];
    if($req->ajax()):

      $result['result'] = true;

    $data = data_karyawan::find($req->id);
    $result['departemen'] = $data->id_departemen;
    $result['jabatan'] = $data->jabatan;

    else:
      $result['result'] = false;
    endif;

    return json_encode($result);
  }

 /* public function getList(Request $req){

    $result = [];
    if($req->ajax()):
      $result['result'] = true;

    $datas = data_kinerja::join('data_kinerja_log','data_kinerja_log.id_kinerja','=','data_kinerja.id')
    ->join('ref_penilaian','ref_penilaian.id_penilaian','=','data_kinerja_log.id_penilaian')
    ->join('ref_penilaian_uraian','ref_penilaian_uraian.id','=','data_kinerja_log.id_penilaian_uraian')
    ->where('id_karyawan',$req->id)
    ->select('ref_penilaian.penilaian','ref_penilaian_uraian.uraian','data_kinerja_log.point','data_kinerja_log.score')
    ->get();
    
    $tr = '';

    foreach ($datas as $data) {
      $tr .= '<tr>
      <td>'.$data->penilaian.'</td>
      <td>'.$data->uraian.'</td>
      <td>'.$data->score.'</td>
      <td>'.$data->point.'</td>
    </tr>';
  }

  $html = '<div class="grid simple">
  <div class="grid-title no-border"></div>
  <div class="grid-body no-border">
    <div class="row">
      <table class="table">
        <thead>
          <tr>
            <th width="30%">Nilai</th>
            <th>Uraian</th>
            <th>Score</th>
            <th>Point</th>
          </tr>
        </thead>
        <tbody>
          '.$tr.'
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>';
$result['table'] = $html;
else:
  $result['result'] = false;
endif;
return json_encode($result);
}
*/
  public function getList($id){

     $heads = data_kinerja::join('data_karyawan','data_karyawan.id_karyawan','=','data_kinerja.id_karyawan')
      ->join('ref_jabatan','ref_jabatan.id','=','data_karyawan.jabatan')
      ->join('data_departemen','data_departemen.id_departemen','=','data_karyawan.id_departemen')
      ->where('data_kinerja.id',$id)
      ->select('data_kinerja.id','data_kinerja.created_at','data_departemen.nm_departemen','data_karyawan.nm_depan','data_karyawan.nm_belakang','data_karyawan.NIK','ref_jabatan.nm_jabatan')
      ->first();

    $datas = data_kinerja::join('data_kinerja_log','data_kinerja_log.id_kinerja','=','data_kinerja.id')
    ->join('ref_penilaian','ref_penilaian.id_penilaian','=','data_kinerja_log.id_penilaian')
    ->join('ref_penilaian_uraian','ref_penilaian_uraian.id','=','data_kinerja_log.id_penilaian_uraian')
    ->where('data_kinerja.id',$id)
    ->select('ref_penilaian.penilaian','ref_penilaian_uraian.uraian','data_kinerja_log.point','data_kinerja_log.score')
    ->get();

    return view('Personalia.Penilaian.list',[
      'datas' => $datas,
      'heads' => $heads
      ]);
  
  }


}
