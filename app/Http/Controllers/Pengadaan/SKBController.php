<?php

namespace App\Http\Controllers\Pengadaan;

use Illuminate\Http\Request;

use App\Models\data_skb;
use App\Models\data_spb;
use App\Models\ref_satuan;
use App\Models\data_spb_item;
use App\Models\data_skb_item;
use App\Models\data_karyawan;
use App\Models\data_departemen;

use App\Jobs\Pengadaan\CreateSKBJob;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SKBController extends Controller {

    public function getIndex(){
        $items = data_skb::listskb()->paginate(10);
        $depts = data_departemen::all();
        $akses = \Me::accessGudang();
        
        if(count($akses) > 1){
            $title = 'Surat Keluar';
            $btn = [
                'status' => true,
                'title' => 'PMB & PMO'
            ];
        }elseif(count($akses) == 0){
            $title = 'Tidak ada akses';
            $btn = [
                'status' => false,
                'title' => 'PMB & PMO'
            ];
        }else{
            if($akses[0] == 1){
                $title = 'Surat Keluar Obat';
                $btn = [
                    'status' => true,
                    'title' => 'PMO'
                ];
            }else{
                $title = 'Surat Keluar Barang';
                $btn = [
                    'status' => true,
                    'title' => 'PMB'
                ];
            }
        }

        return view('Pengadaan.SKB.MasterSKB', [
            'items' => $items,
            'departements' => $depts,
            'title' => $title,
            'btn' => $btn
        ]);
    }
    /*Mengambil Notifikasi dengan ajax*/
    public function getNotifspb(Request $req){
    	if($req->ajax()){

            $akses = \Me::accessGudang();

    		$spb = data_spb::whereIn('status', [1,2])->whereIn('tipe', $akses)->count();
    		return json_encode([
    			'total' => $spb
    		]);
    	}
    }

    public function getAllskb(Request $req){
        if($req->ajax()){
            $res = [];
            $out = '';
            $items = data_skb::listskb($req->all())->paginate($req->limit);
            if($items->total() > 0){
                $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
                foreach($items as $item){
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>
                                <div>' . $item->no_skb . '</div>
                                <div class="link text-muted">
                                    <small>
                                        [
                                            <a href="' . url('/skb/view/' . $item->id_skb) . '">Lihat</a>
                                            | <a href="' . url('/skb/print/' . $item->id_skb ) . '" target="_blank">Print</a>
                                        ]
                                    </small>
                                </div>
                            </td>
                            <td>' . $item->no_spb . '</td>
                            <td>' . $item->nm_depan . ' ' . $item->nm_belakang . '</td>
                            <td>' . $item->nm_departemen . '</td>
                            <td>
                                ' . \Format::indoDate($item->created_at) . '<br />
                                <small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
                            </td>
                        </tr>
                    ';

                    $no++;
                }
            }else{
                $out = '
                    <tr>
                        <td colspan="6">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;
            $res['pagin'] = $items->render();

            return json_encode($res);
            
        }
    }

    public function getSpb(){
        $items = data_spb::alldepartement('',[1,2],0,0)->paginate(10);
        $status = [
            1 => 'Baru',
            2 => 'Proses',
            3 => 'Selesai',
            4 => 'Batal'
        ];

        $akses = \Me::accessGudang();
        
        if(count($akses) > 1){
            $ket = [
                'status' => true,
                'title' => 'Surat PMB & PMO'
            ];
        }elseif(count($akses) == 0){
            $ket = [
                'status' => false,
                'title' => 'Tidak ada Akses'
            ];
        }else{
            if($akses[0] == 1){
                $ket = [
                    'status' => true,
                    'title' => 'Permohonan Obat'
                ];
            }else{
                $ket = [
                    'status' => true,
                    'title' => 'Permohonan Barang'
                ];
            }
        }

        return view('Pengadaan.SKB.ListSPB', [
            'items' => $items,
            'status' => $status,
            'departements' => data_departemen::all(),
            'ket' => $ket
        ]);
    }
    /* Mengambil semua data SPB*/
    public function getAllspb(Request $req){
        if($req->ajax()){
            $result = [];
            $items = data_spb::alldepartement($req->kode,$req->status, $req->dep,$req->deadline, $req->surat)->paginate($req->limit);
            $out = '';

            $status = [
                1 => 'Baru',
                2 => 'Proses',
                3 => 'Selesai',
                4 => 'Batal'
            ];

            if(count($items) > 0):
            $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
            foreach($items as $item){

                $acc = $item->id_acc > 0 ? '<i class="fa fa-check" title="Telah disetujui Kepala"></i>' : '<i class="fa fa-warning" title="Belum disetujui Kepala"></i>';
                $tipe = $item->tipe == 1 ? 'PMO' : 'PMB';
                $out .= '
                    <tr class="spb_' . $item->id_spb . '">
                        <td>' . $no . '</td>
                        <td>
                            <div> ' . $item->no_spb . ' <span class="pull-right">' . $acc . '</span></div>
                            <div class="link text-muted">
                                <small>
                                    [
                                        <a href="#" onclick="detailspb(' . $item->id_spb . ');" data-toggle="modal" data-target="#detail">Lihat</a>
                                        <!-- | <a href="' . url('/pmbumum/printspb/' . $item->id_spb) . '" target="_blank">Print</a> -->
                                    ]
                                </small>
                            </div>
                        </td>
                        <td>
                            <div>' . $item->nm_depan . ' ' . $item->nm_belakang . '</div>
                            <div class="text-muted"><small>Dept : ' . $item->nm_departemen . '</small></div>
                        </td>
                        <td>' . $tipe . '</td>
                        <td>
                            <div>' . \Format::indoDate($item->created_at) . '</div>
                            <div class="text-muted"><small>' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
                        </td>
                        <td class="text-center">' . $status[$item->status] . '</td>
                    </tr>
                ';

                $no++;
            }
            else:
                $out = '
                    <tr>
                    <td colspan="5">Tidak ditemukan</td>
                    </tr>
                ';
            endif;

            $result['data'] = $out;
            $result['pagin'] = $items->render();

            return json_encode($result);

        }else{
            return redirect('/pmbumum');
        }

    }

    public function postDetailspb(Request $req){
        if($req->ajax()){
            $result = [];
            $out = '';

            $spb = data_spb::find($req->id);
           if($spb->status > 2){
                $items = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
                    ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
                    ->where('data_spb_item.id_spb', $req->id)
                    ->whereIn('data_spb_item.status',[1,2])
                    ->select('data_spb_item.*', 'data_barang.nm_barang', 'data_barang.kode', 'ref_satuan.nm_satuan')
                    ->get();
            }else{
                $items = data_spb_item::join('data_barang','data_barang.id_barang', '=', 'data_spb_item.id_item')
                    ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_spb_item.id_satuan')
                    ->where('data_spb_item.id_spb', $req->id)
                    ->where('data_spb_item.status',1)
                    ->select('data_spb_item.*', 'data_barang.nm_barang', 'data_barang.kode', 'ref_satuan.nm_satuan')
                    ->get(); 
            }

            if($spb->id_acc > 0){
                $me = data_karyawan::find($spb->id_acc);
                $out .= '<div class="grid simple">
                            <div class="grid-title no-border"></div>
                            <div class="grid-body no-border">
                                <b>Disetujui Oleh : </b> ' . $me->nm_depan . ' ' . $me->nm_belakang . '
                            </div>
                        </div>
                ';
            }else{
                $out .= '<div class="grid simple">
                            <div class="grid-title no-border"></div>
                            <div class="grid-body no-border">
                                <i class="fa fa-warning"></i> Permintaan belum disetujui Kepala
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
                                    <th class="text-right">Req Qty</th>
                                </tr>
                                </thead>
                                <tbody>
                ';
            foreach($items as $item){
                $out .= '
                    <tr>
                        <td>' . $item->kode . '</td>
                        <td>' . \Format::substr($item->nm_barang,20) . '</td>
                        <td class="text-right">' . number_format($item->qty_lg,0,',','.') . ' ' . $item->nm_satuan . '</td>
                    </tr>
                ';
            }
            $out .= '
                            </tbody>
                        </table>
                    </div>
                </div>';

            $btn = $spb->id_acc > 0 && \Auth::user()->permission > 1 && in_array($spb->status, [1,2]) ? '<a href="' . url('/skb/process/' . $req->id) . '" class="btn btn-primary">Proses</a>' : '';

            $result['kode']     = $spb->no_spb;
            $result['content']  = $out;
            $result['button']   = $btn;

            return json_encode($result);

        }
    }

    /*Proses SKB dari spb */
    public function getProcess($id){
        $spb = data_spb::join('data_karyawan AS a', 'a.id_karyawan', '=', 'data_spb.id_pemohon')
            ->leftJoin('data_karyawan AS b', 'b.id_karyawan', '=', 'data_spb.id_acc')
            ->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
            ->where('data_spb.id_spb', $id)
            ->select(
                'a.nm_depan',
                'a.nm_belakang',
                'b.nm_depan AS acc_depan',
                'b.nm_belakang AS acc_belakang',
                'data_departemen.nm_departemen',
                'data_spb.*'
            )
            ->first();

        $akses = \Me::accessGudang();
        if(!in_array($spb->tipe, $akses))
            return redirect('/skb/spb')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf, Tidak ada akses untuk anda!'
            ]);

        if($spb->status > 2 || $spb->id_acc == 0)
            return redirect('/skb/spb');
        $items = data_spb_item::byspb($id)->get();
        
        return view('Pengadaan.SKB.ProcessSPB', [
            'spb' => $spb,
            'items' => $items,
            'satuan' => ref_satuan::all()
        ]);
    }

    public function postProcess(Request $req){
        $skb = $this->dispatch(new CreateSKBJob($req->all()));
        return redirect('/skb/spb')->withNotif([
            'label' => 'success',
            'err' => $skb['err']
        ]);
    }

    /* Review SKB */
    public function getView($id){
        $skb = data_skb::byid($id);
        $items = data_skb_item::byskb($id)->get();
        return view('Pengadaan.SKB.viewDetail', [
            'skb' => $skb,
            'items' => $items
        ]);
    }

    public function getTerkait(Request $req){
        if($req->ajax()){
            $res = [];
            $skb = data_skb::whereId_spb($req->id);
            $out = '';
            if($skb->count() > 0){
                foreach($skb->get() as $item){
                    $out .= '<tr>
                        <td><a href="' . url('/skb/view/' . $item->id_skb) . '">' . $item->no_skb . '</a></td>
                    </tr>';
                }
            }else{
                $out = '
                    <tr>
                        <td>Tidak ditemukan</td>
                    </tr>
                ';
            }
            $res['total'] = $skb->count();
            $res['content'] = $out;
            return json_encode($res);
        }
    }

    public function getPrint($id){

        $find = data_spb::join('data_karyawan AS a', 'a.id_karyawan', '=', 'data_spb.id_pemohon')
            ->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_spb.id_departemen')
            ->join('data_skb', 'data_skb.id_spb', '=', 'data_spb.id_spb')
            ->join('data_karyawan AS b', 'b.id_karyawan', '=', 'data_skb.id_petugas')
            ->where('data_skb.id_skb', $id)
            ->select(
                'data_spb.*', 
                'a.nm_depan', 
                'a.nm_belakang', 
                'data_departemen.nm_departemen', 
                'b.nm_depan As petugas_depan', 
                'b.nm_belakang AS petugas_belakang',
                'data_skb.no_skb'
            );
        $spb    = $find->first();

        $items = data_skb_item::byskb($id)->get();

        //dd($items);
        return view('Print.Pengadaan.SKB', [
            'spb' => $spb,
            'items' => $items
        ]);
    }

}
