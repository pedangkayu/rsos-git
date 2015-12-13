<?php

namespace App\Http\Controllers\Pengadaan;

use Session;

use App\Models\data_spb;
use App\Models\data_skb;
use App\Models\ref_satuan;
use App\Models\ref_gudang;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_karyawan;
use App\Models\data_spb_item;
use App\Models\ref_konversi_satuan;

use App\Jobs\Pengadaan\CreatePMBJob;
use App\Jobs\Pengadaan\EditSPBJob;
use App\Jobs\Pengadaan\AdditemSPBJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PmbUmumController extends Controller{

    private $MySession, $ids = [];

    public function __construct(){

        $me = 'itemPMB_' . \Auth::user()->id_user;
        $this->MySession = $me;
        if(!empty(session()->get($me))){
            foreach(session()->get($me) as $val){
                $this->ids[] = $val['id_barang'];
            }
        }
    }
   
    public function getIndex(){
        //session()->forget($this->MySession);
        $items = data_spb::bydepartement('',0)->paginate(10);
        $status = [
            1 => 'Baru',
            2 => 'Proses',
            3 => 'Selesai',
            4 => 'Batal'
        ];
        return view('Pengadaan.SPB.PMBUmum', [
            'items' => $items,
            'status' => $status
        ]);
    }
    /* Mengambil semua data SPB*/
    public function getAllspb(Request $req){
        if($req->ajax()){
            $result = [];
            $items = data_spb::bydepartement($req->kode,$req->status, $req->gtujuan, $req->all())->paginate($req->limit);
            $out = '';
            $total = $items->total();
            $status = [
                1 => 'Baru',
                2 => 'Proses',
                3 => 'Selesai',
                4 => 'Batal'
            ];

            if($total > 0):
                $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
                foreach($items as $item){

                    $delete = $item->status < 2 && \Auth::user()->permission > 1 ? '
                        | <a href="' . url('/pmbumum/editspb/' . $item->id_spb) . '">Edit</a> |
                        <a href="javascript:void(0);" onclick="delspb(' . $item->id_spb . ');" class="text-danger">Hapus</a>' : '';

                    $skbbtn =  in_array($item->status, [2,3]) ? '| <a href="#" data-toggle="modal" data-target="#detailSKB" onclick="listviewskb(' . $item->id_spb . ');">Lihat SKB</a>' : '';

                    $tujuan = $item->tipe == 1 ? 'PMO' : 'PMB';

                    $tanda = empty($item->id_acc) ? '<i class="fa fa-times text-muted pull-right" title="Belum terverifikasi"></i>' : '<i title="Terverifikasi" class="fa fa-check-circle text-success pull-right"></i>';

                    if(!empty($item->tgl_approval) || $item->tgl_approval != '0000-00-00 00:00:00'){
                        $tgl_approval = '
                            <div>' . \Format::indoDate2($item->tgl_approval) . '</div>
                            <div class="text-muted"><small>' . \Format::hari($item->tgl_approval) . ', ' . \Format::jam($item->tgl_approval) . '</small></div>
                        ';
                    }else{
                        $tgl_approval = '<center>-</center>';
                    }

                    $out .= '
                        <tr class="spb_' . $item->id_spb . '">
                            <td>' . $no . '</td>
                            <td>
                                <div> ' . $item->no_spb . ' ' . $tanda . '</div>
                                <div class="link text-muted">
                                    <small>
                                        [
                                            <a href="#" onclick="detailspb(' . $item->id_spb . ');" data-toggle="modal" data-target="#detail">Lihat</a>
                                            ' . $skbbtn . $delete . '
                                        ]
                                    </small>
                                </div>
                            </td>

                            <td>
                                <div>' . $item->nm_depan . ' ' . $item->nm_belakang . '</div>
                                <div class="text-muted"><small>Dept : ' . $item->nm_departemen . '</small></div>
                            </td>

                            <td>
                                <div>' . $tujuan . '</div>
                            </td>
                            <td>
                                <div>' . \Format::indoDate2($item->created_at) . '</div>
                                <div class="text-muted"><small>' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
                            </td>
                            <td>' . $tgl_approval . '</td>
                            <td class="text-center">' . $status[$item->status] . '</td>
                        </tr>
                    ';

                    $no++;
                }
            else:
                $out = '
                    <tr>
                    <td colspan="7">Tidak ditemukan</td>
                    </tr>
                ';
            endif;

            $result['data'] = $out;
            $result['pagin'] = $items->render();
            $result['total'] = $total;

            return json_encode($result);

        }else{
            return redirect('/pmbumum');
        }

    }

    public function postNoverif(Request $req){
        if($req->ajax()){
            $total = data_spb::bydepartement('',1, '', $req->all())->count();
            return json_encode([
                'total' => $total
            ]);
        }
    }

    public function postDetailspb(Request $req){
        if($req->ajax()){
            $result = [];
            $out = '';

            $spb = data_spb::find($req->id);

            if($spb->status > 2){
                $items = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
                    ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
                    ->where('data_spb_item.id_spb', $req->id)
                    ->whereIn('data_spb_item.status',[1,2])
                    ->select('data_spb_item.*', 'data_barang.nm_barang', 'data_barang.kode', 'data_barang.in', 'data_barang.out', 'ref_satuan.nm_satuan')
                    ->get();
            }else{
                $items = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
                    ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
                    ->where('data_spb_item.id_spb', $req->id)
                    ->where('data_spb_item.status',1)
                    ->select('data_spb_item.*', 'data_barang.nm_barang', 'data_barang.kode', 'data_barang.in', 'data_barang.out', 'ref_satuan.nm_satuan')
                    ->get(); 
            }

            

            if($spb->id_acc > 0){
                $me = data_karyawan::find($spb->id_acc);
                $out .= '<div class="grid simple">
                            <div class="grid-title no-border"></div>
                            <div class="grid-body no-border">
                                <b>Disetujui Oleh : </b> ' . $me->nm_depan . ' ' . $me->nm_belakang . '<br />
                                <small class="text-muted">' . \Format::hari($spb->tgl_approval) . ', ' . \Format::indoDate2($spb->tgl_approval) . ' ' . \Format::jam($spb->tgl_approval) . '</small>
                            </div>
                        </div>
                ';
            }

            $out .= '<div class="grid simple">
                        <div class="grid-title no-border">
                        <h4>' . count($items) . ' barang <strong>ditemukan</strong></4><br />
                        <small>Deadline : ' . \Format::indoDate($spb->deadline) . '</small>
                        </div>
                        <div class="grid-body no-border">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Sisa</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                                </thead>
                                <tbody>
                ';
            foreach($items as $item){
                $out .= '
                    <tr>
                        <td>' . $item->kode . '</td>
                        <td>' . \Format::substr($item->nm_barang,20) . '</td>
                        <td>' . number_format(($item->in - $item->out),0,',','.') . ' ' . $item->nm_satuan . '</td>
                        <td class="text-right">' . number_format($item->qty_lg,0,',','.') . ' ' . $item->nm_satuan . '</td>
                    </tr>
                ';
            }
            $out .= '
                            </tbody>
                        </table>
                    </div>
                </div>';

            $btn = \Auth::user()->permission > 2 && $spb->status < 2 && empty($spb->id_acc) ? '<button data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Proses..." class="btn btn-primary btn-accspb" onclick="acc(' . $req->id . ');"><i class="fa fa-check"></i> Setujui</button>' : '';

            $result['kode']     = $spb->no_spb;
            $result['content']  = $out;
            $result['button']   = $btn;

            return json_encode($result);

        }
    }

    /*Pemilihan Item Obat*/
    public function getSelectitemspmo(){
        //session()->forget($this->MySession);
    	$items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
    		->where('data_barang.status',1)
            ->where('data_barang.tipe', 1)
    		->select('data_barang.*', 'ref_satuan.nm_satuan')
            ->whereNotIn('data_barang.id_barang', $this->ids)
    		->paginate(10);
    	$kats = ref_kategori::all();
    	return view('Pengadaan.SPB.SelectItems',[
    		'items' => $items,
    		'kats' => $kats,
            'tipe' => 1
    	]);
    }

    /*Pemilihan Item barang*/
    public function getSelectitemspmb(){
        //session()->forget($this->MySession);
        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('data_barang.status',1)
            ->where('data_barang.tipe', 2)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')
            ->whereNotIn('data_barang.id_barang', $this->ids)
            ->paginate(10);
        $kats = ref_kategori::all();
        return view('Pengadaan.SPB.SelectItems',[
            'items' => $items,
            'kats' => $kats,
            'tipe' => 2
        ]);
    }

    /* Pemanggilan semua item barang */
    public function getAllitemspmo(Request $req){
    	$result = [];
    	if($req->ajax()):
    		$items = data_barang::srcpmo($req->all(), $this->ids)->paginate($req->limit);
    		$out = '';
    		if(count($items) > 0):
	    		foreach($items as $item){
	    			$out .= '
	    				<tr class="item_' . $item->id_barang . '">
							<td width="20%">
                                <a href="#" data-toggle="modal" data-target="#review" onclick="review(' . $item->id_barang . ')">' . $item->kode . '</a>
                            </td>
							<td width="55%" colspan="2">' . $item->nm_barang . '</td>
							<!-- <td width="15%" class="text-right">' . number_format(($item->in - $item->out),0,',','.') . ' ' . $item->nm_satuan . '</td> -->
							<td width="15%">
								<button onclick="add(' . $item->id_barang . ');" class="btn btn-white btn-block btn-xs btn-mini" title="Advance Searching"><i class="fa fa-plus"></i></button>
							</td>
						</tr>
	    			';
	    		}
	    	else:
	    		$out = '
	    			<tr>
	    			    <td colspan="4"><div class="well">Tidak ditemukan</div></td>
	    			</tr>
	    		';
	    	endif;
    		$result['data'] = $out;
    		$result['pagin'] = $items->render();
    		return json_encode($result);
    	else:
    		return redirect('/pmbumum/selectitems');
    	endif;
    }

    /* Pemanggilan semua item barang */
    public function getAllitemspmb(Request $req){
        $result = [];
        if($req->ajax()):
            $items = data_barang::srcpmb($req->all(), $this->ids)->paginate($req->limit);
            $out = '';
            if(count($items) > 0):
                foreach($items as $item){
                    $out .= '
                        <tr class="item_' . $item->id_barang . '">
                            <td width="20%">
                                <a href="#" data-toggle="modal" data-target="#review" onclick="review(' . $item->id_barang . ')">' . $item->kode . '</a>
                            </td>
                            <td width="55%" colspan="2">' . $item->nm_barang . '</td>
                            <!-- <td width="15%" class="text-right">' . number_format(($item->in - $item->out),0,',','.') . ' ' . $item->nm_satuan . '</td> -->
                            <td width="15%">
                                <button onclick="add(' . $item->id_barang . ');" class="btn btn-white btn-block btn-xs btn-mini" title="Advance Searching"><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    ';
                }
            else:
                $out = '
                    <tr>
                        <td colspan="4"><div class="well">Tidak ditemukan</div></td>
                    </tr>
                ';
            endif;
            $result['data'] = $out;
            $result['pagin'] = $items->render();
            return json_encode($result);
        else:
            return redirect('/pmbumum/selectitems');
        endif;
    }

    /*Menambahkan Item barang ke dalam daptar permohonan */
    public function postAdditem(Request $req){
        if($req->ajax()):
            $item   = data_barang::find($req->id);
            $new    = [
                [
                    'kode'      => $item->kode,
                    'id_barang' => $item->id_barang,
                    'nm_barang' => $item->nm_barang,
                    'sisa'      => ($item->in - $item->out),
                    'tipe'      => $item->tipe
                ]
            ];
            $items      = [];
            $find = false;
            if(!empty($req->session()->get($this->MySession))){
                foreach($req->session()->get($this->MySession) as $val){
                    if($val['id_barang'] == $req->id){
                        $items[] = [
                            'kode'      => $val['kode'],
                            'id_barang' => $val['id_barang'],
                            'nm_barang' => $val['nm_barang'],
                            'sisa'      => $val['sisa'],
                            'tipe'      => $val['tipe']
                        ];
                        $find = true;
                    }else{
                         $items[] = [
                            'kode'      => $val['kode'],
                            'id_barang' => $val['id_barang'],
                            'nm_barang' => $val['nm_barang'],
                            'sisa'      => $val['sisa'],
                            'tipe'      => $val['tipe']
                        ];
                    }
                }
            }
            if($find)
                $value = $items;
            else
                $value = array_merge($new, $items);

            $req->session()->put($this->MySession, $value);

            return json_encode([
                'id' => $req->id
            ]);
        endif;
    }

    /*Mengambil semua item yang sudah dipilih*/
    public function getItemselected(Request $req){
        if($req->ajax()):
            $out = '<tr><td>Tidak ada</td></tr>';
            $count = [];
            if(!empty($req->session()->get($this->MySession))){
                $out = '';
                foreach($req->session()->get($this->MySession) as $item){
                    if($item['tipe'] == $req->tipe){
                        $count[] = 1;
                        $out .= '
                            <tr class="hover-item me_' . $item['id_barang'] . '">
                                <td style="position:relative;">
                                    ' . $item['kode'] . '<br>
                                    <div class="slctd">' . $item['nm_barang'] . '</div>
                                    <div class="oneitem">
                                        <center>
                                            <a href="javascript:void(0);" onclick="trashme(' . $item['id_barang'] . ');"><i class="fa fa-trash"></i></a>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        ';
                    }
                }
                //$count = count($req->session()->get($this->MySession));
            }

            if(count($count) < 1)
                $out = '<tr><td>Tidak ada</td></tr>';
            return json_encode([
                'data'  => $out,
                'count' => count($count)
            ]);
        endif;
    }

    /*Menghapus semua item yang suda dipilih*/
    public function getDellall(Request $req){
        if($req->ajax()){
            $req->session()->forget($this->MySession);
            return json_encode([
                'result' => true
            ]);
        }
    }

    /*Menghapus item yang sudah dipilih satu per satu*/
    public function postTrashme(Request $req){
        if($req->ajax()){
            $items = [];
            foreach($req->session()->get($this->MySession) as $val){
                if($val['id_barang'] != $req->id)
                    $items[] = [
                        'kode'      => $val['kode'],
                        'id_barang' => $val['id_barang'],
                        'nm_barang' => $val['nm_barang'],
                        'sisa'      => $val['sisa'],
                        'tipe'      => $val['tipe']
                    ];
            }

            $req->session()->put($this->MySession, $items);
            return json_encode([
                'result' => true
            ]);

        }
    }

    /*Membuat permohonan Barang dan Obat*/
    public function getCreate(Request $req, $tipe = 0){
        if(count($this->ids) < 1)
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item dib  bawah ini'
            ]);

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe))
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Kesalahan, Silahkan tentukan kembali permintaan anda!'
            ]);

        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('data_barang.tipe', $tipe)
            ->whereIn('id_barang', $this->ids)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')->get();

        if(count($items) < 1)
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item dib  bawah ini'
            ]);

        $gudangs = ref_gudang::all();

        $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);


        return view('Pengadaan.SPB.CreatePMB', [
            'items'     => $items,
            'gudangs'   => $gudangs,
            'tipe'      => $tipe,
            'satuan'    => ref_satuan::all(),
            'ids'       => $ids
        ]);
    }

    /*Mengirim permintaan PMB*/
    public function postCreate(Request $req){
        $total = [];
        foreach($req->qty as $qty){
            if(!empty($qty))
                $total[] = $qty;
        }
        if(count($total) < 1)
            return redirect()->back()->withNotif([
                'label' => 'warning',
                'err' => 'Qty tidak boleh semuanya kosong!'
            ]);

        $spb = $this->dispatch(new CreatePMBJob($req->all()));
        
        if($spb['res']){
            $req->session()->forget($this->MySession);
            return redirect('/pmbumum')->withNotif([
                'label' => $spb['label'],
                'err' => $spb['err']
            ]);
        }else{
            return redirect()->back()->withNotif([
                'label' => $spb['label'],
                'err' => $spb['err']
            ]);
        }
    }

    /*Edit SPB*/
    public function getEditspb($id){

        $find = data_spb::join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spb.id_pemohon')
            ->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
            ->where('data_spb.id_spb', $id)
            ->where('data_spb.id_departemen', \Me::data()->id_departemen)
            ->where('data_spb.status',1)
            ->select('data_spb.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang', 'data_departemen.nm_departemen');

        if($find->count() == 0)
            return redirect('/pmbumum');

        $spb    = $find->first();
        $items = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
            ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
            ->where('data_spb_item.id_spb', $id)
            ->where('data_spb_item.status',1)
            ->orderby('data_barang.nm_barang', 'asc')
            ->select('data_spb_item.*', 'data_barang.id_barang', 'data_barang.in', 'data_barang.out', 'data_barang.nm_barang', 'data_barang.id_satuan AS satuan_default', 'data_barang.kode', 'ref_satuan.nm_satuan')
            ->get();


         $obat = [];
         $id_gudang = '';
        foreach($items as $item){
            if($item->tipe == 1)
                $obat[] = $item->id_barang;

            $id_gudang = $item->id_gudang;
        }


        $ids = [];
        $sels = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
            $sels[$id->id_barang] = $id->id_satuan;
        }
        
        $param['ids'] = $ids;
        $param['tipe'] = $spb->tipe;
        $param['sels'] = $sels;

        $param = json_encode($param);

        return view('Pengadaan.SPB.EditSPB', [
            'spb'   => $spb,
            'items' => $items,
            'obat' => $obat,
            'id_gudang' => $id_gudang,
            'gudangs' => $gudangs = ref_gudang::all(),
            'satuan' => ref_satuan::all(),
            'param' => $param,
            'tipe' => $spb->tipe
        ]);
    }
    /*Simpan Perubahan*/
    public function postEditspb(Request $req){

        $spb = data_spb::where('status', 1)
            ->where('id_spb', $req->id_spb)
            ->count();
        if($spb == 0)
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, perubahan anda ditolak! Karena No Permohonan ' . $req->no_spb . ' barusaja diproses!'
            ]);

        $total = [];
        foreach($req->qty as $qty){
            if(!empty($qty))
                $total[] = $qty;
        }
        if(count($total) < 1)
            return redirect()->back()->withNotif([
                'label' => 'warning',
                'err' => 'Qty tidak boleh semuanya kosong!'
            ]);
        
        $spb = $this->dispatch(new EditSPBJob($req->all()));
        return redirect()->back()->withNotif([
            'label' => $spb['label'],
            'err' => $spb['err']
        ]);
    }

    /*Delete SPB*/
    public function postDelspb(Request $req){
        if($req->ajax()){
            data_spb::find($req->id)->update([
                'status' => 4
            ]);

            return json_encode([
                'result' => true
            ]);
        }
    }

    /*Menyetujui SPB*/
    public function postAccspb(Request $req){
        if($req->ajax()){
            $spb = data_spb::find($req->id);
            $spb->update([
                'id_acc' => \Me::data()->id_karyawan,
                'tgl_approval' => date('Y-m-d H:i:s')
            ]);

            \Loguser::create('Melakukan Verifikasi terhadap PMB/PMO dengan No. ' . $spb->no_spb);
        }
    }

    /*Print SPB*/
    public function getPrintspb($id){

        $find = data_spb::join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spb.id_pemohon')
            ->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
            ->where('data_spb.id_spb', $id)
            ->where('data_spb.id_departemen', \Me::data()->id_departemen)
            ->select('data_spb.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang', 'data_departemen.nm_departemen');

        if($find->count() == 0)
            return redirect('/pmbumum');

        $spb    = $find->first();
        $items  = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
            ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('data_spb_item.id_spb', $id)
            ->select('data_spb_item.*', 'data_barang.id_barang', 'data_barang.in', 'data_barang.out', 'data_barang.nm_barang', 'data_barang.kode', 'ref_satuan.nm_satuan')
            ->get();

        return view('Print.Pengadaan.SPB', [
            'spb' => $spb,
            'items' => $items
        ]);
    }
    /*Menambahkan Item SPB*/
    public function getAdditemspb($tipe = 0){

        if(count($this->ids) < 1)
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item dib  bawah ini'
            ]);

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe))
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Kesalahan, Silahkan tentukan kembali permintaan anda!'
            ]);

        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('data_barang.tipe', $tipe)
            ->whereIn('id_barang', $this->ids)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')->get();

        if(count($items) < 1)
            return redirect('/pmbumum')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item dib  bawah ini'
            ]);

       
        $spb = data_spb::where('id_departemen', \Me::data()->id_departemen)
            ->where('tipe', $tipe)
            ->where('status', 1)
            ->get();

        $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);

        return view('Pengadaan.SPB.AdditemSPB', [
            'items'     => $items,
            'spball'    => $spb,
            'tipe'      => $tipe,
            'satuan'    => ref_satuan::all(),
            'ids'       => $ids
        ]);
        
    }
    /*Menyimpan Perubahan Item SPB*/
    public function postAdditemspb(Request $req){
        $find = data_spb::where('id_spb', $req->id_spb)
            ->where('status',1)
            ->count();
        if($find == 0)
            return redirect()->back()->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Nomor permintaan tersebut sudah diperoses beberapa saat lalu!'
            ]);

            $spb = $this->dispatch(new AdditemSPBJob($req->all()));
            if($spb['res']){
                $req->session()->forget($this->MySession);
                return redirect('/pmbumum/editspb/' . $req->id_spb)->withNotif([
                    'label' => $spb['label'],
                    'err' => $spb['err']
                ]);
            }else{
                return redirect()->back()->withNotif([
                    'label' => $spb['label'],
                    'err' => $spb['err']
                ]);
            }
    }

    /*Menampilkan daftar SKB pada setiap SPB*/
    public function getListskb(Request $req){
        if($req->ajax()){
            $res = [];
            $items = data_skb::whereId_spb($req->id);
            $total = $items->count();
            $res['total'] = $total;
            
            $out = '<table class="table table-hover table-bordered">';

            if($total > 0){
                foreach($items->get() as $item){
                    $out .= '<tr>
                        <td><a href="' . url('/skb/print/' . $item->id_skb) . '" target="_blank">' . $item->no_skb . '</a>
                            <span class="pull-right">' . \Format::indoDate($item->created_at) . '</span>
                        </td>
                    </tr>';
                }
            }else{
                $out .= '
                    <tr>
                        <td>Tidak ditemukan</td>
                    </tr>
                ';
            }
            
            $out .= '<table>';
        }
        $res['content'] = $out;

        return json_encode($res);
    }

    public function getSatuans(Request $req){
        if($req->ajax()){  
            $res = [];
            $res['ids'] = $req->ids;

            $sels   = ($req->sels);
            

            foreach($req->ids as $id){
                $converts = ref_konversi_satuan::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'ref_konversi_satuan.id_satuan_max')
                    ->where('ref_konversi_satuan.id_barang', $id)
                    ->select('ref_konversi_satuan.*', 'ref_satuan.nm_satuan');
                
                if($converts->count() > 0){
                    $out = '<select name="satuan[]" class="form-control">';
                    foreach($converts->get() as $kon){
                        if(count($sels) > 0){
                            $sel = $sels[$id] == $kon->id_satuan_max ? 'selected="selected"' : '';
                            $out .= '<option ' . $sel . ' value="' . $kon->id_satuan_max . '">'  . $kon->nm_satuan . '</option>';
                        }else{
                            $out .= '<option value="' . $kon->id_satuan_max . '" >'  . $kon->nm_satuan . '</option>';
                        }
                    }
                    $out .= '</select>';
                    $res['result'][$id] = true;
                    $res['content'][$id] = $out;
                }else{
                    $res['result'][$id] = false;
                }
            }

            return json_encode($res);
        }
    }

}
