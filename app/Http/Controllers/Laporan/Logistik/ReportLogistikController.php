<?php

namespace App\Http\Controllers\Laporan\Logistik;

use App\Models\data_po;
use App\Models\data_skb;
use App\Models\data_spbm;
use App\Models\ref_gudang;
use App\Models\data_retur;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_po_item;
use App\Models\data_skb_item;
use App\Models\ref_klasifikasi;
use App\Models\data_log_barang;
use App\Models\data_penyesuaian_stok;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Excel;
use PDF;
use DB;

class ReportLogistikController extends Controller {

    public function getIndex(){

        return view('Laporan.Logistik.Index');
    }

    public function getInformasi(Request $req){
    	if($req->ajax()){
    		$res = [];
    		// Load data barang
    		$barang = '<option value="0">Pilih Barang</option>';
    		$items = data_barang::where('status', 1)->where('tipe', 2)->get();
    		foreach($items as $item){
    			$barang .= '<option value="' . $item->id_barang . '">' . $item->nm_barang . '</option>';
    		}

    		$res['barang'] = $barang;

    		// Load data barang
    		$obat = '<option value="0">Pilih Obat</option>';
    		$items = data_barang::where('status', 1)->where('tipe', 1)->get();
    		foreach($items as $item){
    			$obat .= '<option value="' . $item->id_barang . '">' . $item->nm_barang . '</option>';
    		}

    		$res['obat'] = $obat;

    		// Load data kategori
           $kategori = '<option value="0">Pilih Kategori</option>';
           $items = ref_kategori::all();
           foreach($items as $item){
               $kategori .= '<option value="' . $item->id_kategori . '">' . $item->nm_kategori . '</option>';
           }

           $res['kategori'] = $kategori;

    		// Load data Klasifikasi
           $klasifikasi = '<option value="0">Pilih Klasifikasi</option>';
           $items = ref_klasifikasi::all();
           foreach($items as $item){
               $klasifikasi .= '<option value="' . $item->id_klasifikasi . '">' . $item->nm_klasifikasi . '</option>';
           }

           $res['klasifikasi'] = $klasifikasi;

    		// total transaksi
           $total = data_log_barang::where('id_gudang', 0)
           ->where(\DB::raw('MONTH(`created_at`)'), date('m'))->where(\DB::raw('YEAR(`created_at`)'), date('Y'))->count();
           $res['total'] = number_format($total, 0,',','.');

           return json_encode($res);
       }
   }

   public function getItems(Request $req){
        if($req->ajax()){
        		// Load data barang
            $barang = '<option value="0">Pilih Barang</option>';
            $items = data_barang::where('status', 1)->where('tipe', $req->tipe)->get();
            foreach($items as $item){
                $barang .= '<option value="' . $item->id_barang . '">' . $item->nm_barang . '</option>';
            }

            $res['items'] = $barang;
            return json_encode($res);
        }
    }

    public function getKs(Request $req){
        $day = \Format::selisih_hari($req->dari, $req->sampai);

        if($day > 31)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Pencarian tidak boleh lebih dari 30 hari!'
            ]);

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
            ]);

        $items = [];
        
        // Data Barang
        $barang = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('id_barang',$req->barang)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')
            ->first();

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

        $lastsisa = $barang->stok_awal + $sisa;
        $aftersisa = $lastsisa;
        // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();
        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
            $parent = data_spbm::join('data_spbm_item', 'data_spbm_item.id_spbm', '=','data_spbm.id_spbm')
                ->where('data_spbm_item.id_barang', $req->barang)
                ->where('data_spbm.id_spbm', $item->id_parent)
                ->select('data_spbm.*', 'data_spbm_item.tgl_exp')
                ->first();
            else if($item->tipe == 3)
                $parent = data_penyesuaian_stok::where('id_penyesuaian_stok', $item->id_parent)->where('id_gudang', 0)->first();
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

        return view('Laporan.Logistik.KartuStok', [
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'lastsisa' => $aftersisa,
            'jenis' => $jenis,
            'param' => $params
          ]);
    }

    public function getPrintks(Request $req){
        $day = \Format::selisih_hari($req->dari, $req->sampai);

        if($day > 31)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Pencarian tidak boleh lebih dari 30 hari!'
                ]);

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
                ]);

        $items = [];
            // Data Barang
        $barang = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
        ->where('id_barang',$req->barang)
        ->select('data_barang.*', 'ref_satuan.nm_satuan')
        ->first();

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
        $lastsisa = $barang->stok_awal + $sisa;
        $aftersisa = $lastsisa;
            // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();
        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
                $parent = data_spbm::find($item->id_parent);
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

        return view('Print.Pengadaan.KartuStok.printKS', [
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'lastsisa' => $aftersisa,
            'jenis' => $jenis
            ]);
    }

    public function getPrintexcel(Request $req){
        $kd_barang = data_barang::where('id_barang', $req->barang)->select('kode')->first();

        Excel::create('Kartu_Stok_'.$kd_barang->kode.'_'.date('d_F_Y'), function($excel) use($req) {

            $day = \Format::selisih_hari($req->dari, $req->sampai);

            if($day > 31)
                return redirect()->back()->withNotif([
                    'label' => 'danger',
                    'err' => 'Pencarian tidak boleh lebih dari 30 hari!'
                    ]);

            if($req->barang == 0)
                return redirect()->back()->withNotif([
                    'label' => 'danger',
                    'err' => 'Item tidak ditemukan!'
                    ]);

            $items = [];
            // Data Barang
            $barang = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('id_barang',$req->barang)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')
            ->first();
            
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
            $lastsisa = $barang->stok_awal + $sisa;
            $aftersisa = $lastsisa;
            // Log stok
            $logs = data_log_barang::kartostokbyitem($req->all())->get();
            foreach($logs as $item){
                if($item->tipe == 1)
                    $parent = data_skb::find($item->id_parent);
                else if($item->tipe == 2)
                    $parent = data_spbm::find($item->id_parent);
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

            $r = array(
                'barang' => $barang,
                'items' => $items,
                'req' => $req,
                'lastsisa' => $aftersisa,
                'jenis' => $jenis
                );    
            $excel->sheet('New sheet', function($sheet) use($r){

                $sheet->loadView('Print.Pengadaan.KartuStok.excelKS',$r);

            });
        })->export('xlsx');
    }

    public function getPrintpdf(Request $req){
        $day = \Format::selisih_hari($req->dari, $req->sampai);

        if($day > 31)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Pencarian tidak boleh lebih dari 30 hari!'
                ]);

        if($req->barang == 0)
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Item tidak ditemukan!'
                ]);

        $items = [];
            // Data Barang
        $barang = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('id_barang',$req->barang)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')
            ->first();

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
        $lastsisa = $barang->stok_awal + $sisa;
        $aftersisa = $lastsisa;
            // Log stok
        $logs = data_log_barang::kartostokbyitem($req->all())->get();
        foreach($logs as $item){
            if($item->tipe == 1)
                $parent = data_skb::find($item->id_parent);
            else if($item->tipe == 2)
                $parent = data_spbm::find($item->id_parent);
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

        $r = array(
            'barang' => $barang,
            'items' => $items,
            'req' => $req,
            'lastsisa' => $aftersisa,
            'jenis' => $jenis
            );    
        $pdf = PDF::loadView('Print.Pengadaan.KartuStok.pdfKS', $r);

        return $pdf->stream();
            //$file = 'KartuStok-'.date('Y-m-d').'.pdf';
            //return $pdf->download($file);
        
    }

    public function getGrafik(Request $req){

        $res = [];
        $loga = [];
        $logb = [];
        $ids = [];



        $logobat = data_log_barang::join('data_barang','data_barang.id_barang','=','data_log_barang.id_barang')
            ->where(DB::raw('MONTH(data_log_barang.created_at)'), date('m'))
            ->where('data_barang.tipe',1)
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

        $logbarang = data_log_barang::join('data_barang','data_barang.id_barang','=','data_log_barang.id_barang')
            ->where('data_barang.tipe',2)
            ->where(DB::raw('MONTH(data_log_barang.created_at)'), date('m'))
            ->orderBy('total','DESC')
            ->groupBy('data_log_barang.id_barang')
            ->select('data_barang.id_barang','data_barang.nm_barang',DB::raw('count(data_log_barang.id_barang) as total'))
            ->take(5)
            ->get();


        foreach($logbarang as $b){
            $logb[] = [
                'barang' => $b->nm_barang,
                'value' => $b->total,
            ];
            $ids[] = $b->id_barang;
        }

        $res['barang'] = $logb;
        

        return json_encode($res);
    }

    public function getLaporanbelanjabarang(){

        return view('Laporan.Logistik.Laporanbelanjabarang');
    }

    public function getLaporanbelanjabarangajax(Request $req){
        if($req->ajax()){
            $res = [];
            $out = '';
            
            /* OBAT */
            $items = data_po_item::lpbdo($req->all(), 1)->get();
            $total = count($items);
            if($total > 0){

                $reg_total      = 0;
                $reg_nominal    = 0;

                $out .= '
                    <tr>
                        <td class="text-center semi-bold" colspan="8">Obat</td>
                    </tr>
                ';

                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>
                                <a href="javascript:void(0);" onclick="detail('  . $item->id_kategori . ',1);">' . $item->nm_kategori . '</a>
                            </td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_nominal += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';

            }

            $tobat = $total;

            /* BARANG */
            $reg_totalb      = 0;
            $reg_nominalb    = 0;

            $items = data_po_item::lpbdo($req->all(), 2)->get();
            $total = count($items);
            if($total > 0){


                $out .= '
                    <tr>
                        <td class="text-center semi-bold" colspan="8">Barang</td>
                    </tr>
                ';

                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>
                                <a href="javascript:void(0);" onclick="detail('  . $item->id_kategori . ',1);">' . $item->nm_kategori . '</a>
                            </td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_totalb += $item->total;
                    $reg_nominalb += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_totalb,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominalb,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_totalb,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominalb,2,',','.') . '</td>
                    </tr>
                ';

            }

            if(($tobat + $total) < 1)
                $out .= '
                    <tr>
                        <td colspan="8">Barang Tidak ditemukan</td>
                    </tr>
                ';

            $res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

            return json_encode($res);
       }
    }

    public function getRekapbelanja(){

        return view('Laporan.Logistik.RekapBelanja');
    }

    /* LAPORAN PENBELIAN */
    public function getRekapbelanjaajax(Request $req){
         if($req->ajax()){
            $res = [];
            $out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
            $items = data_po_item::rekapbelanja($req->all())->get();
            $total = count($items);
            if($total > 0){

                $reg_total      = 0;
                $reg_nominal    = 0;


                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>
                                <a href="#" onclick="detail('  . $item->id_kategori . ',1);" data-toggle="modal" data-target="#detail">' . $item->nm_kategori . '</a>
                            </td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_nominal += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';

            }else{
                $out = '
                    <tr>
                        <td colspan="8">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

            return json_encode($res);
       }
    }

    public function getRekapbelanjadetailajax(Request $req){
         if($req->ajax()){
            $res = [];
            $out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
            $items = data_po_item::rekapbelanjadetail($req->all())->get();
            $total = count($items);
            if($total > 0){

                $reg_total      = 0;
                $reg_nominal    = 0;
                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>' . $item->nm_barang . '</td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_nominal += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';

            }else{
                $out = '
                    <tr>
                        <td colspan="8">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;
            $res['id_kategori'] = $req->id_kategori;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

            return json_encode($res);
       }
    }

    /* END LAPORAN PENBELIAN */

    // public function getRekapatk(){
    //     return view('Laporan.Logistik.RekapAtk');
    // }

    // public function getRekapatkajax(Request $req){
    //      if($req->ajax()){
    //         $res = [];
    //         $out = '';
    //         //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
    //         $items = data_po_item::rekapatk($req->all())->get();
    //         $total = count($items);
    //         if($total > 0){

    //             $reg_total      = 0;
    //             $reg_nominal    = 0;


    //             $no = 1;
    //             foreach ($items as $item) {
    //                 $out .= '
    //                     <tr>
    //                         <td>' . $no . '</td>
    //                         <td>' . $item->nm_barang . '</td>
    //                         <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
    //                         <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
    //                         <td class="text-right"></td>
    //                         <td class="text-right"></td>
    //                         <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
    //                         <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
    //                     </tr>
    //                 ';
    //                 $no++;

    //                 $reg_total += $item->total;
    //                 $reg_nominal += $item->harga;

    //             }


    //             $out .= '
    //                 <tr>
    //                     <td class="text-center" colspan="2"><b>Total</b></td>
    //                     <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
    //                     <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
    //                     <td class="text-right"></td>
    //                     <td class="text-right"></td>
    //                     <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
    //                     <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
    //                 </tr>
    //             ';

    //         }else{
    //             $out = '
    //                 <tr>
    //                     <td colspan="8">Tidak ditemukan</td>
    //                 </tr>
    //             ';
    //         }

    //         $res['content'] = $out;
    //         // $res['pagin'] = $items->render();
    //         // $res['total'] = $total;

    //         return json_encode($res);
    //    }
    // }

    /* REKAP DISTRIBUTOR */

    public function getRekapdistributor(){
        return view('Laporan.Logistik.RekapDistributor');
    }

    public function getRekapdistributorajax(Request $req){
         if($req->ajax()){
            $res = [];
            $out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
            $items = data_po_item::rekapdistributor($req->all())->get();
            $total = count($items);
            if($total > 0){

                $reg_total      = 0;
                $reg_nominal    = 0;
                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>' . $item->nm_vendor . '</td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_nominal += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';

            }else{
                $out = '
                    <tr>
                        <td colspan="8">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

            return json_encode($res);
       }
    }




    /* REKAP PRODUSEN */
    public function getRekapprodusen(){
        return view('Laporan.Logistik.RekapProdusen');
    }

    public function getRekapprodusenajax(Request $req){
         if($req->ajax()){
            $res = [];
            $out = '';
            //$items = data_po_item::lpbdo($req->all())->paginate($req->limit);
            $items = data_po_item::rekapprodusesn($req->all())->get();
            $total = count($items);
            if($total > 0){

                $reg_total      = 0;
                $reg_nominal    = 0;
                $no = 1;
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>' . $item->nm_vendor . '</td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                            <td class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_nominal += $item->harga;

                }


                $out .= '
                    <tr>
                        <td class="text-center" colspan="2"><b>Total</b></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';

            }else{
                $out = '
                    <tr>
                        <td colspan="8">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;
            // $res['pagin'] = $items->render();
            // $res['total'] = $total;

            return json_encode($res);
       }
    }

    public function getPrintbelanjabarang(Request $req) {
        
        if($req->id_kategori > 0)
            $items = data_po_item::rekapbelanjadetail($req->all())->get();
        else
            $items =  data_po_item::lpbdo($req->all(), 1)->get();
        
        return view('Print.Laporan.BelanjaBarang',[
            'items' => $items,
            'req' => $req
            ]);
    }

    public function getPrintdistributor(Request $req)
    {
        $items =  $items = data_po_item::rekapdistributor($req->all())->get();

        return view('Print.Laporan.BelanjaDistributor', [
            'items' => $items
        ]);
    }

    public function getPrintprodusen(Request $req){
         $items =  $items = data_po_item::rekapprodusesn($req->all())->get();

        return view('Print.Laporan.BelanjaProdusen', [
            'items' => $items
        ]);
    }

    /* LAPORAN PERPINDAHAN BARANG */
    public function getLpb(){
        $items = ref_gudang::listgudang()->get();
        return view('Laporan.Logistik.Lpb', [
            'items' => $items
        ]);
    }

    public function getLpbajax(Request $req){
        if($req->ajax()){
            // dd($req->all());
            $res = [];
            $out = '';

            $reg_total = 0;
            $reg_satuan = 0;
            $reg_nominal = 0;

            $items = data_skb_item::lpb($req->all(), 1)->get();
            $total = count($items);
            if($total > 0):
                $no = 1;
                $out .= '<tr>
                        <td colspan="5" class="text-center">
                            <strong>Obat</strong>
                        </td>
                    </tr>';
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>' . $item->nm_barang . '</td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga_beli,2,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_total += $item->total;
                    $reg_satuan += $item->harga_beli;
                    $reg_nominal += $item->harga;

                }

                $out .= '
                    <tr>
                        <td colspan="2" class="semi-bold text-center">Total</td>
                        <td class="text-right semi-bold">' . number_format($reg_total,0,'','') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_satuan,2,',','.') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominal,2,',','.') . '</td>
                    </tr>
                ';
            endif;

            $reg_totalb = 0;
            $reg_satuanb = 0;
            $reg_nominalb = 0;
            $items = data_skb_item::lpb($req->all(), 2)->get();
            $totalb = count($items);
            if($totalb > 0):
                $no = 1;
                $out .= '<tr>
                        <td colspan="5" class="text-center">
                            <strong>Barang</strong>
                        </td>
                    </tr>';
                foreach ($items as $item) {
                    $out .= '
                        <tr>
                            <td>' . $no . '</td>
                            <td>' . $item->nm_barang . '</td>
                            <td class="text-right">' . number_format($item->total,0,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga_beli,2,',','.') . '</td>
                            <td class="text-right">' . number_format($item->harga,2,',','.') . '</td>
                        </tr>
                    ';
                    $no++;

                    $reg_totalb += $item->total;
                    $reg_satuanb += $item->harga_beli;
                    $reg_nominalb += $item->harga;

                }

                $out .= '
                    <tr>
                        <td colspan="2" class="semi-bold text-center">Total</td>
                        <td class="text-right semi-bold">' . number_format($reg_totalb,0,'','') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_satuanb,0,'','') . '</td>
                        <td class="text-right semi-bold">' . number_format($reg_nominalb,2,',','.') . '</td>
                    </tr>
                ';
            
            endif;

            if(($total + $totalb) < 1){
                $out = '
                    <tr>
                        <td colspan="4">Tidak ditemukan</td>
                    </tr>
                ';
            }

            $res['content'] = $out;

            return json_encode($res);
        }
    }

    public function getLpbprint(Request $req){
        $obats = data_skb_item::lpb($req->all(), 1)->get();
        $barangs = data_skb_item::lpb($req->all(), 2)->get();
        return view('Print.Laporan.Lpbprint', [
            'obats' => $obats,
            'barangs' => $barangs,
            'req' => $req
        ]);
    }

    public function getLaporanstok(){
        $klasifikasis = ref_klasifikasi::all();
        $kategoris = ref_kategori::all();
        return view('Laporan.Logistik.LaporanStok', [
            'klasifikasis' => $klasifikasis,
            'kategoris' => $kategoris
        ]);
    }

    public function getLaporanstokview(Request $req){
        //dd($req->all());
        if($req->tipe == 'klasifikasi' && ($req->id_klasifikasi) == '')
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Anda belum menenetukan Klasifikasi'
            ]);
        if($req->tipe == 'kategori' && ($req->id_kategori) == '')
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Anda belum menenetukan Kategori'
            ]);

        $items = data_barang::laporanstok($req->all())->get();
        $tipe = [
            1 => 'Obat',
            2 => 'Barang'
        ];

        return view('Laporan.Logistik.LaporanStokView', [
            'items' => $items,
            'req' => $req,
            'tipe' => $tipe
        ]);
    }

    public function getLaporanstokprint(Request $req){
        //dd($req->all());
        if($req->tipe == 'klasifikasi' && ($req->id_klasifikasi) == '')
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Anda belum menenetukan Klasifikasi'
            ]);
        if($req->tipe == 'kategori' && ($req->id_kategori) == '')
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Anda belum menenetukan Kategori'
            ]);

        $items = data_barang::laporanstok($req->all())->get();
        $tipe = [
            1 => 'Obat',
            2 => 'Barang'
        ];
        
        return view('Laporan.Logistik.LaporanStokPrint', [
            'items' => $items,
            'req' => $req,
            'tipe' => $tipe
        ]);
    }

}