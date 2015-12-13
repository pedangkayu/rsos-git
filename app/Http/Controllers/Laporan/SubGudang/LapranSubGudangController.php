<?php

namespace App\Http\Controllers\Laporan\SubGudang;

use Illuminate\Http\Request;

use App\Models\data_skb;
use App\Models\data_spbm;
use App\Models\data_retur;
use App\Models\ref_gudang;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\ref_klasifikasi;
use App\Models\data_log_barang;
use App\Models\data_item_gudang;
use App\Models\data_penyesuaian_stok;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LapranSubGudangController extends Controller {
    
	public function getIndex(){
        $me = \Me::subgudang();
		return view('Laporan.SubGudang.Index', [
            'gudangs' => ref_gudang::all(),
            'me' => $me
        ]);
	}

	 public function getInformasi(Request $req){
    	if($req->ajax()){
    		$res = [];
    		// Load data barang
            $sl = $req->tipe == 1 ? 'Obat' : 'Barang';
    		$barang = '<option value="">Pilih ' . $sl . '</option>';
    		$items = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
                ->where('data_item_gudang.status', 1)
                ->where('data_item_gudang.id_gudang', $req->id_gudang)
                ->where('data_barang.tipe', $req->tipe)
                ->get();
    		foreach($items as $item){
    			$barang .= '<option value="' . $item->id_barang . '">' . $item->nm_barang . '</option>';
    		}

    		$res['items'] = $barang;

    		return json_encode($res);
    	}
    }

    public function getKs(Request $req){

        // $day = \Format::selisih_hari($req->dari, $req->sampai);

        // if($day > 31)
        //     return redirect()->back()->withNotif([
        //         'label' => 'danger',
        //         'err' => 'Pencarian tidak boleh lebih dari 30 hari!'
        //     ]);

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        $items = [];
        // Data Barang
        $barang = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
                ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
                ->where('data_item_gudang.status', 1)
                ->where('data_item_gudang.id_gudang', $req->gudang)
                ->where('data_item_gudang.id_barang', $req->barang)
                ->where('data_barang.tipe', $req->tipe)
                ->select(
                    'data_barang.nm_barang', 
                    'data_barang.kode', 
                    'data_item_gudang.in', 
                    'data_item_gudang.out', 
                    'ref_satuan.nm_satuan'
                )
                ->first();
        
        if($barang == null)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        //Sisa per priode
        $sisapriode = data_log_barang::sisapriode($req->all())->get();
        //dd($sisapriode);
        $sisa = 0;
        foreach($sisapriode as $sp){
            if($sp->kondisi == 1){
                $sisa += $sp->qty;
            }else{
                $sisa -= $sp->qty;
            }
        }
        $lastsisa = $sisa;
        // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();

        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
                //$parent = data_spbm::find($item->id_parent);
                $parent = data_spbm::join('data_spbm_item', 'data_spbm_item.id_spbm', '=','data_spbm.id_spbm')
                    ->where('data_spbm_item.id_barang', $req->barang)
                    ->where('data_spbm.id_spbm', $item->id_parent)
                    ->select('data_spbm.*', 'data_spbm_item.tgl_exp')
                    ->first();
            else if($item->tipe == 3)
                $parent = data_penyesuaian_stok::find($item->id_parent);
            else if($item->tipe == 4)
                $parent = data_retur::find($item->id_parent);
            else if($item->tipe == 5)
                $parent = data_retur::find($item->id_parent);

            if($item->kondisi == 1){
                $lastsisa += $item->qty;
            }else{
                $lastsisa -= $item->qty;
            }
            $items[] = [
                'tipe' => $item->tipe,
                'parent' => $parent,
                'kondisi' => $item->kondisi,
                'qty' => $item->qty,
                'sisa' => $lastsisa,
                'oleh' => $item->nm_depan . ' ' . $item->nm_belakang
            ];
        }
        
        $jenis = [
            1 => 'SKB',
            2 => 'Good Receive',
            3 => 'Penyesuaian',
            4 => 'Retur Gudang',
            5 => 'Retur Pembelian'
        ];

        // Parameters
        $params = '';
        foreach($req->all() as $par => $val){
            if($par != '_token')
                $params .= $par . '=' . $val . '&';
        }
        $param = rtrim($params, '&');
        
        return view('Laporan.SubGudang.KartuStok', [
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'jenis' => $jenis,
            'param' => $params
        ]);

    }

    public function getPrintks(Request $req){

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        $items = [];
        // Data Barang
        $barang = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
                ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
                ->where('data_item_gudang.status', 1)
                ->where('data_item_gudang.id_gudang', $req->gudang)
                ->where('data_item_gudang.id_barang', $req->barang)
                ->where('data_barang.tipe', $req->tipe)
                ->select(
                    'data_barang.nm_barang', 
                    'data_barang.kode', 
                    'data_item_gudang.in', 
                    'data_item_gudang.out', 
                    'ref_satuan.nm_satuan'
                )
                ->first();
        
        if($barang == null)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        //Sisa per priode
        $sisapriode = data_log_barang::sisapriode($req->all())->get();
        //dd($sisapriode);
        $sisa = 0;
        foreach($sisapriode as $sp){
            if($sp->kondisi == 1){
                $sisa += $sp->qty;
            }else{
                $sisa -= $sp->qty;
            }
        }
        $lastsisa = $sisa;
        // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();

        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
                //$parent = data_spbm::find($item->id_parent);
                $parent = data_spbm::join('data_spbm_item', 'data_spbm_item.id_spbm', '=','data_spbm.id_spbm')
                    ->where('data_spbm_item.id_barang', $req->barang)
                    ->where('data_spbm.id_spbm', $item->id_parent)
                    ->select('data_spbm.*', 'data_spbm_item.tgl_exp')
                    ->first();
            else if($item->tipe == 3)
                $parent = data_penyesuaian_stok::find($item->id_parent);
            else if($item->tipe == 4)
                $parent = data_retur::find($item->id_parent);
            else if($item->tipe == 5)
                $parent = data_retur::find($item->id_parent);

            if($item->kondisi == 1){
                $lastsisa += $item->qty;
            }else{
                $lastsisa -= $item->qty;
            }
            $items[] = [
                'tipe' => $item->tipe,
                'parent' => $parent,
                'kondisi' => $item->kondisi,
                'qty' => $item->qty,
                'sisa' => $lastsisa,
                'oleh' => $item->nm_depan . ' ' . $item->nm_belakang
            ];
        }
        
        $jenis = [
            1 => 'SKB',
            2 => 'Good Receive',
            3 => 'Penyesuaian',
            4 => 'Retur Gudang',
            5 => 'Retur Pembelian'
        ];

        // Parameters
        $params = '';
        foreach($req->all() as $par => $val){
            if($par != '_token')
                $params .= $par . '=' . $val . '&';
        }
        $param = rtrim($params, '&');
        
        $gudang = ref_gudang::find($req->gudang);

        return view('Print.Pengadaan.SubGudang.PrintKs', [
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'jenis' => $jenis,
            'param' => $params,
            'gudang' => $gudang
        ]);

    }

    public function getPrintpdf(Request $req){

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        $items = [];
        // Data Barang
        $barang = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
                ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
                ->where('data_item_gudang.status', 1)
                ->where('data_item_gudang.id_gudang', $req->gudang)
                ->where('data_item_gudang.id_barang', $req->barang)
                ->where('data_barang.tipe', $req->tipe)
                ->select(
                    'data_barang.nm_barang', 
                    'data_barang.kode', 
                    'data_item_gudang.in', 
                    'data_item_gudang.out', 
                    'ref_satuan.nm_satuan'
                )
                ->first();
        
        if($barang == null)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        //Sisa per priode
        $sisapriode = data_log_barang::sisapriode($req->all())->get();
        //dd($sisapriode);
        $sisa = 0;
        foreach($sisapriode as $sp){
            if($sp->kondisi == 1){
                $sisa += $sp->qty;
            }else{
                $sisa -= $sp->qty;
            }
        }
        $lastsisa = $sisa;
        // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();

        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
                //$parent = data_spbm::find($item->id_parent);
                $parent = data_spbm::join('data_spbm_item', 'data_spbm_item.id_spbm', '=','data_spbm.id_spbm')
                    ->where('data_spbm_item.id_barang', $req->barang)
                    ->where('data_spbm.id_spbm', $item->id_parent)
                    ->select('data_spbm.*', 'data_spbm_item.tgl_exp')
                    ->first();
            else if($item->tipe == 3)
                $parent = data_penyesuaian_stok::find($item->id_parent);
            else if($item->tipe == 4)
                $parent = data_retur::find($item->id_parent);
            else if($item->tipe == 5)
                $parent = data_retur::find($item->id_parent);

            if($item->kondisi == 1){
                $lastsisa += $item->qty;
            }else{
                $lastsisa -= $item->qty;
            }
            $items[] = [
                'tipe' => $item->tipe,
                'parent' => $parent,
                'kondisi' => $item->kondisi,
                'qty' => $item->qty,
                'sisa' => $lastsisa,
                'oleh' => $item->nm_depan . ' ' . $item->nm_belakang
            ];
        }
        
        $jenis = [
            1 => 'SKB',
            2 => 'Good Receive',
            3 => 'Penyesuaian',
            4 => 'Retur Gudang',
            5 => 'Retur Pembelian'
        ];

        // Parameters
        $params = '';
        foreach($req->all() as $par => $val){
            if($par != '_token')
                $params .= $par . '=' . $val . '&';
        }
        $param = rtrim($params, '&');
        
        $gudang = ref_gudang::find($req->gudang);

        return \PDF::loadView('Print.Pengadaan.SubGudang.Pdf', [
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'jenis' => $jenis,
            'param' => $params,
            'gudang' => $gudang
        ])->stream();;

    }

     public function getPrintexcel(Request $req){

        $kd_barang = data_barang::where('id_barang', $req->barang)->select('kode')->first();
        $gudang = ref_gudang::find($req->gudang);
        \Excel::create('Kartu_Stok_' . str_replace('-', '_', str_slug($gudang->nm_gudang)) . '_' . $kd_barang->kode.'_'.date('d_F_Y'), function($excel) use($req) {

            if($req->barang == 0)
                return redirect()->back()->withNotif([
                    'label' => 'danger',
                    'err' => 'Item tidak ditemukan!'
                ]);

            $items = [];
            // Data Barang
            $barang = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
                    ->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
                    ->where('data_item_gudang.status', 1)
                    ->where('data_item_gudang.id_gudang', $req->gudang)
                    ->where('data_item_gudang.id_barang', $req->barang)
                    ->where('data_barang.tipe', $req->tipe)
                    ->select(
                        'data_barang.nm_barang', 
                        'data_barang.kode', 
                        'data_item_gudang.in', 
                        'data_item_gudang.out', 
                        'ref_satuan.nm_satuan'
                    )
                    ->first();
            
            if($barang == null)
                return redirect()->back()->withNotif([
                    'label' => 'danger',
                    'err' => 'Item tidak ditemukan!'
                ]);

            //Sisa per priode
            $sisapriode = data_log_barang::sisapriode($req->all())->get();
            //dd($sisapriode);
            $sisa = 0;
            foreach($sisapriode as $sp){
                if($sp->kondisi == 1){
                    $sisa += $sp->qty;
                }else{
                    $sisa -= $sp->qty;
                }
            }
            $lastsisa = $sisa;
            // Log stok
            $logs = data_log_barang::kartostokbyitem($req->all())->get();

            foreach($logs as $item){
                if($item->tipe == 1)
                    $parent = data_skb::find($item->id_parent);
                else if($item->tipe == 2)
                    //$parent = data_spbm::find($item->id_parent);
                    $parent = data_spbm::join('data_spbm_item', 'data_spbm_item.id_spbm', '=','data_spbm.id_spbm')
                        ->where('data_spbm_item.id_barang', $req->barang)
                        ->where('data_spbm.id_spbm', $item->id_parent)
                        ->select('data_spbm.*', 'data_spbm_item.tgl_exp')
                        ->first();
                else if($item->tipe == 3)
                    $parent = data_penyesuaian_stok::find($item->id_parent);
                else if($item->tipe == 4)
                    $parent = data_retur::find($item->id_parent);
                else if($item->tipe == 5)
                    $parent = data_retur::find($item->id_parent);

                if($item->kondisi == 1){
                    $lastsisa += $item->qty;
                }else{
                    $lastsisa -= $item->qty;
                }
                $items[] = [
                    'tipe' => $item->tipe,
                    'parent' => $parent,
                    'kondisi' => $item->kondisi,
                    'qty' => $item->qty,
                    'sisa' => $lastsisa,
                    'oleh' => $item->nm_depan . ' ' . $item->nm_belakang
                ];
            }
            
            $jenis = [
                1 => 'SKB',
                2 => 'Good Receive',
                3 => 'Penyesuaian',
                4 => 'Retur Gudang',
                5 => 'Retur Pembelian'
            ];

            // Parameters
            $params = '';
            foreach($req->all() as $par => $val){
                if($par != '_token')
                    $params .= $par . '=' . $val . '&';
            }
            $param = rtrim($params, '&');
            
            $gudang = ref_gudang::find($req->gudang);

            $data = [
                'barang' => $barang,
                'items' => $items,
                'req' => $req,
                'jenis' => $jenis,
                'param' => $params,
                'gudang' => $gudang
            ];

            $excel->sheet('New sheet', function($sheet) use($data){
                $sheet->loadView('Print.Pengadaan.SubGudang.Excel', $data);
            });

        })->export('xlsx');

    }

    public function getGrafik(Request $req){

    $res = [];
    $loga = [];
    $logb = [];
    $ids = [];



    $logobat = data_log_barang::join('data_barang','data_barang.id_barang','=','data_log_barang.id_barang')
    ->where(DB::raw('MONTH(data_log_barang.created_at)'), date('m'))
    ->where('data_log_barang.id_gudang',0)
    ->orderBy('total','DESC')
    ->groupBy('data_log_barang.id_barang')
    ->select('data_barang.id_barang','data_barang.nm_barang',DB::raw('count(data_log_barang.id_barang) as total'))
    ->take(5)
    ->get();

    foreach($logobat as $a){
        $loga[] = [
            'obat' => $a->nm_barang,
            'value' => $a->total,
        ];
        $ids[] = $a->id_barang;
    }
    
    $res['obat'] = $loga;

    return json_encode($res);
}

}
