<?php

namespace App\Http\Controllers\Pengadaan;

use App\Models\data_prq;
use App\Models\ref_gudang;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_prq_item;
use App\Models\data_penyesuaian_stok;
use App\Models\data_penyesuaian_stok_item;

use Illuminate\Http\Request;

use App\Jobs\Pengadaan\StockAdjustment\CreateAdjustmentJob;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StockAdjustmentController extends Controller {

	private $MySession, $ids = [];

    public function __construct(){

        $me = 'itemADJ-' . \Auth::user()->id_user;
        $this->MySession = $me;
        if(!empty(session()->get($me))){
            foreach(session()->get($me) as $val){
                $this->ids[] = $val['id_barang'];
            }
        }
    }

    public function getIndex(){
    	$akses = \Me::statusGudang();
        $items = data_penyesuaian_stok::show()->paginate(10);
		return view('Pengadaan.StockAdjustment.Index', [
			'akses' => $akses,
            'items' => $items
		]);
	}

    public function getAlladj(Request $req){
        if($req->ajax()){
            $res = [];
            $out = '';
            $items = data_penyesuaian_stok::show($req->all())->paginate($req->limit);

            $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
            foreach($items as $item){
                $out .= '
                    <tr>
                        <td>' . $no . '</td>
                        <td>
                            ' . $item->no_penyesuaian_stok . '
                            <div class="link">
                                <small>[
                                        <a target="_blank" href="' . url('/stockadj/print/' . $item->id_penyesuaian_stok ) . '">Print</a>
                                    ]
                                </small>
                            </div>
                        </td>
                        <td>
                            ' . $item->nm_depan . ' ' . $item->nm_belakang . '<br />
                            <small class="text-muted">' . \Format::indoDate($item->created_at) . ' at ' . \Format::jam($item->created_at) . '</small>
                        </td>
                        <td>' . \Format::hari($item->tanggal) . ', ' . \Format::indoDate($item->tanggal) . '</td>
                    </tr>
                ';
                $no++;
            }

            $res['content'] = $out;
            $res['pagin'] = $items->render();

            return json_encode($res);
        }
    }

	public function getSelect($tipe = 0){
		
        $akses = \Me::accessGudang();
        $status = \Me::statusGudang();

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe) )
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);
        if($status == 0)
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf, Anda belum memiliki Akses. Silahkan untuk menghubungu atasan Anda!'
            ]);
        if(!in_array($tipe, $akses))
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf Bukan domain Anda!'
            ]);

    	$items = data_barang::srcprq([], $this->ids, $tipe)->paginate(10);
    	$kats = ref_kategori::all();

        
        if($tipe == 1)
            $title = 'Semua daftar Obat';
        elseif($tipe == 2)
            $title = 'Semua daftar Barang';
        

		return view('Pengadaan.StockAdjustment.Select', [
			'items' => $items,
    		'kats' => $kats,
            'tipe' => $tipe,
            'title' => $title
		]);
	}

	 /* Pemanggilan semua item barang */
    public function getAllitems(Request $req){
    	$result = [];
    	if($req->ajax()):
    		$items = data_barang::srcprq($req->all(), $this->ids, $req->tipe)->paginate($req->limit);
    		$out = '';
    		if(count($items) > 0):
	    		foreach($items as $item){
	    			$out .= '
	    				<tr class="item_' . $item->id_barang . '">
							<td width="20%">
                                <a href="#" data-toggle="modal" data-target="#review" onclick="review(' . $item->id_barang . ')">' . $item->kode . '</a>
                            </td>
							<td colspan="2" width="55%">' . $item->nm_barang . '</td>
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
    		return redirect('/stockadj/select');
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
            $items  = [];
            $find   = false;
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

    /*Mengambil semua item yang sudah dipilih*/
    public function getItemselected(Request $req){
        if($req->ajax()):
            $out = '<tr><td>Tidak ada</td></tr>';
            $count = 0;
            if(!empty($req->session()->get($this->MySession))){
                $out = '';
                $count = [];
                foreach($req->session()->get($this->MySession) as $item){
                    if($req->tipe == $item['tipe']){
                        $out .= '
                            <tr class="hover-item me_' . $item['id_barang'] . '">
                                <td style="position:relative;">
                                    ' . $item['kode'] . '
                                    <div class="oneitem">
                                        <center>
                                            <a href="javascript:void(0);" onclick="trashme(' . $item['id_barang'] . ');"><i class="fa fa-trash"></i></a>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        ';
                        $count[] = 1;
                    }
                }
                $count = count($count);
            }
            return json_encode([
                'data'  => $out,
                'count' => $count
            ]);
        endif;
    }

    public function getCreate($tipe = 0){

    	$akses = \Me::accessGudang();

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe) )
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);
        if(!in_array($tipe, $akses))
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf Bukan domain Anda!'
            ]);


        if(count($this->ids) < 1)
            return redirect('/stockadj/select')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item di bawah ini'
            ]);

        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->whereIn('id_barang', $this->ids)
            ->where('data_barang.tipe', $tipe)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')->get();

        if(count($items) == 0)
            return redirect('/stockadj')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);

        $gudangs = ref_gudang::all();
        
         $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);

        return view('Pengadaan.StockAdjustment.Create', [
            'items' => $items,
            'gudangs' => $gudangs,
            'ids'   => $ids,
            'tipe'  => $tipe
        ]);
    	
    }

    public function postCreate(Request $req){
    	$err = $this->dispatch(new CreateAdjustmentJob($req->all()));

    	if($err['result'] == true){
    		$req->session()->forget($this->MySession);
    		return redirect('/stockadj')->withNotif([
	    		'label' => $err['label'],
	    		'err' => $err['err']
	    	]);
    	}else{
    		return redirect()->back()->withNotif([
	    		'label' => $err['label'],
	    		'err' => $err['err']
	    	]);
    	}

    	
    }

    public function getPrint($id = 0){
        $akses = \Me::statusGudang();

        if(empty($id) || !is_numeric($id))
            return redirect('/stockadj');

        $adj = data_penyesuaian_stok::byid($id)->first();

        if($adj == null)
            return redirect('/stockadj');

        if($adj->id_gudang > 0)
            return redirect('/stockadj');

        $items = data_penyesuaian_stok_item::byhead($id)->get();
        return view('Print.Pengadaan.ADJ', [
            'adj' => $adj,
            'items' => $items
        ]);
    }
    
}
