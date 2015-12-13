<?php

namespace App\Http\Controllers\Pengadaan;

use App\Models\data_prq;
use App\Models\ref_gudang;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_prq_item;

use App\Jobs\Pengadaan\PRQ\CreatePRQJob;
use App\Jobs\Pengadaan\PRQ\EditPRQJob;
use App\Jobs\Pengadaan\PRQ\AddItemPRQJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PRQController extends Controller {
    
	private $MySession, $ids = [];

    public function __construct(){

        $me = 'itemPRQ_' . \Auth::user()->id_user;
        $this->MySession = $me;
        if(!empty(session()->get($me))){
            foreach(session()->get($me) as $val){
                $this->ids[] = $val['id_barang'];
            }
        }
    }

    public function getIndex(){

        $akses = \Me::statusGudang();

    	$prq = data_prq::listprq()->paginate(10);

    	$status = [
	    	1 => 'Baru',
	    	2 => 'Proses',
	    	3 => 'Selesai',
	    	4 => 'Hapus'
	    ];

        if($akses == 0)
            $title = 'Tidak ada akses!';
        elseif($akses == 1)
            $title = 'Pengajuan Obat';
        elseif($akses == 2)
            $title = 'Pengajuan Barang';
        elseif($akses == 3)
            $title = 'Surat Pengajuan';

		return view('Pengadaan.PRQ.ListPRQ', [
			'items' => $prq,
			'status' => $status,
            'akses' => $akses,
            'title' => $title
		]);
	}

	public function getGetallprq(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$items = data_prq::listprq($req->all(), $req->status)->paginate($req->limit);
			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;

			$status = [
		    	1 => 'Baru',
		    	2 => 'Proses',
		    	3 => 'Selesai',
		    	4 => 'Hapus'
		    ];

		    if($items->total() > 0):

				foreach($items as $item){

					$class = strtotime($item->target) > strtotime(date('Y-m-d')) ? '' : 'class=text-danger';
					$selisih = strtotime($item->target) > strtotime(date('Y-m-d')) ? '<small class="text-muted">' . \Format::selisih_hari($item->target, date('Y-m-d')) . 'hari dari sekarang</small>' : '';
                    $tipe = $item->tipe == 1 ? 'Obat' : 'Barang';

                    $tanda = empty($item->id_acc) ? '<i class="fa fa-times text-muted pull-right" title="Belum terverifikasi"></i>' : '<i title="Terverifikasi" class="fa fa-check-circle text-success pull-right"></i>';

					$edit 	= $item->status < 2 ? '| <a href="' .  url('/prq/edit/' . $item->id_prq). '">Edit</a>' : '';
					$del 	= \Auth::user()->permission > 1 && $item->status < 2 ? '| <a href="javascript:;" onclick="hapusprq(' . $item->id_prq . ');" class="text-danger">Hapus</a>' : '';

                    if(!empty($item->tgl_approval) || $item->tgl_approval != '0000-00-00 00:00:00'){
                        $tgl_approval = '
                            <div>' . \Format::indoDate2($item->tgl_approval) . '</div>
                            <div class="text-muted"><small>' . \Format::hari($item->tgl_approval) . ', ' . \Format::jam($item->tgl_approval) . '</small></div>
                        ';
                    }else{
                        $tgl_approval = '<center>-</center>';
                    }

					$out .= '
						<tr class="item-prq-' . $item->id_prq . '">
							<td>' . $no . '</td>
							<td>
								<div>
                                    ' . $item->no_prq . '
                                    ' . $tanda . '
                                </div>
								<div class="links">
									<small>
										[
											<a href="#" data-toggle="modal" data-target="#detailprq" onclick="detailprq(' . $item->id_prq . ')">Lihat</a>
											' . $edit . '
											' . $del . '
										]
									</small>
								</div>
							</td>
							<td>
								<div ' . $class . '> ' . \ Format::indoDate2($item->target) . '</div>
								' . $selisih . '
							</td>
							<td title="' .  $item->nm_depan . ' ' . $item->nm_belakang . '">
								' . \Format::substr($item->nm_depan . '  ' . $item->nm_belakang,10) . '
								<div><small class="text-muted">' . \Format::indoDate($item->created_at) . '</small></div>
							</td>
                            <td>' . $tipe . '</td>
                            <td>' . $tgl_approval . '</td>
							<td>' . $status[$item->status] . '</td>
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

			$res['pagin'] = $items->render();
			$res['content'] = $out;

			return json_encode($res);

		}
	}

	public function getSelect($tipe = 0){
		
        $akses = \Me::accessGudang();
        $status = \Me::statusGudang();

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe) )
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);
        if($status == 0)
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf, Anda belum memiliki Akses. Silahkan untuk menghubungu atasan Anda!'
            ]);
        if(!in_array($tipe, $akses))
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf Bukan domain Anda!'
            ]);

    	$items = data_barang::srcprq([], $this->ids, $tipe)->paginate(10);
    	$kats = ref_kategori::all();

        
        if($tipe == 1)
            $title = 'Semua daftar Obat';
        elseif($tipe == 2)
            $title = 'Semua daftar Barang';
        

		return view('Pengadaan.PRQ.SelectItem', [
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
    		return redirect('/prq/select');
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
                                    ' . $item['kode'] . ' <br />
									<div class="slctd">' . $item['nm_barang'] . '</div>
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
    public function getCreate(Request $req, $tipe){

        $akses = \Me::accessGudang();

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe) )
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);
        if(!in_array($tipe, $akses))
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf Bukan domain Anda!'
            ]);


        if(count($this->ids) < 1)
            return redirect('/prq/select')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item di bawah ini'
            ]);

        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->whereIn('id_barang', $this->ids)
            ->where('data_barang.tipe', $tipe)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')->get();

        if(count($items) == 0)
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);

        $gudangs = ref_gudang::all();
        
         $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);

        return view('Pengadaan.PRQ.CreatePRQ', [
            'items' => $items,
            'gudangs' => $gudangs,
            'ids'   => $ids,
            'tipe'  => $tipe
        ]);
    }

     /*Mengirim permintaan PRQ*/
    public function postCreate(Request $req){
        if(array_sum($req->qty) < 1)
            return redirect()->back()->withNotif([
                'label' => 'warning',
                'err' => 'Qty tidak boleh semuanya kosong!'
            ]);

        // if(empty($req->satuan))
        //     return redirect()->back()->withNotif([
        //         'label' => 'warning',
        //         'err' => 'Satuan belum terbaca, tunggu beberapa saat sampai nama satuannya keluar!'
        //     ]);

        $prq = $this->dispatch(new CreatePRQJob($req->all()));
        $req->session()->forget($this->MySession);
        return redirect('/prq')->withNotif([
            'label' => 'success',
            'err' => 'Pengajuan Barang berhasil terkirim dengan Nomor ' . $prq->no_prq
        ]);
    }

    
    public function getDetailprq(Request $req){
    	if($req->ajax()){
    		$res = [];

    		$prq = data_prq::find($req->id_prq);
    		$items = data_prq_item::join('data_barang', 'data_barang.id_barang', '=', 'data_prq_item.id_barang')
    			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_prq_item.id_satuan')
    			->where('data_prq_item.id_prq', $req->id_prq)
    			->select('data_prq_item.*', 'data_barang.nm_barang', 'data_barang.kode', 'data_barang.in', 'data_barang.out', 'ref_satuan.nm_satuan')
    			->get();

    		$out = '<h5>' . count($items) . ' barang <b>ditemukan</b></h5>';
    			
    		$out .= '
    		<table class="table table-striped">
    		<thead>
    			<tr>
    				<th>Kode</th>
    				<th>Barang</th>
                    <th>Sisa</th>
    				<th class="text-right">REQ QTY</th>
    			</tr>
    		</thead>
    		<tbody>
    		';

    		foreach($items as $item){
    			$out .= '
    				<tr title="' . $item->nm_barang . '">
    					<td>' . $item->kode . '</td>
    					<td>' . \Format::substr($item->nm_barang,10) . '</td>
                        <td>' . number_format(($item->in - $item->out),0,',','.') . ' ' . $item->nm_satuan . '</td>
    					<td class="text-right">' . number_format($item->qty,0,',','.') . ' ' . $item->nm_satuan . '</td>
    				</tr>
    			';
    		}

    		$out .= '</tbody></table>';

    		$btn = \Auth::user()->permission > 2 && $prq->id_acc < 1 && $prq->status < 4 ? '<button data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Proses..." class="btn btn-primary btn-accs" onclick="accprq(' . $req->id_prq . ');"><i class="fa fa-check"></i> Disetujui</button>' : '';

    		$res['no_prq'] = $prq->no_prq;
    		$res['content'] = $out;
    		$res['btn'] = $btn;

    		return json_encode($res);
    	}
    }

    public function postAccprq(Request $req){
    	if($req->ajax()){
    		data_prq::find($req->id)->update([
    			'id_acc' => \me::data()->id_karyawan
    		]);

    		return json_encode([
    			'return' => true
    		]);
    	}
    }

    public function getEdit($id){

    	$prq = data_prq::join('data_karyawan AS a', 'a.id_karyawan', '=', 'data_prq.id_pemohon')
    		->leftJoin('data_karyawan AS b', 'b.id_karyawan', '=', 'data_prq.id_acc')
            ->where('data_prq.id_prq', $id)
    		->select('data_prq.*', 'a.nm_depan', 'a.nm_belakang', 'b.nm_depan AS acc_depan', 'b.nm_belakang AS acc_belakang')
    		->first();

    	$items = data_prq_item::join('data_barang', 'data_barang.id_barang', '=', 'data_prq_item.id_barang')
    		->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->where('data_prq_item.id_prq', $id)
            ->select(
                'data_barang.kode', 
                'data_barang.nm_barang', 
                'data_barang.in', 
                'data_barang.out', 
                'data_prq_item.*', 
                'ref_satuan.nm_satuan'
            )->get();

        $ids = [];
        $sels = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
            $sels[$id->id_barang] = $id->id_satuan;
        }
        
        $param['ids'] = $ids;
        $param['tipe'] = $prq->tipe;
        $param['sels'] = $sels;

        $param = json_encode($param);
        
    	return view('Pengadaan.PRQ.EditPRQ', [
    		'prq' => $prq,
    		'items' => $items,
            'param' => $param,
            'tipe'  => $prq->tipe
    	]);
    }

    public function postEdit(Request $req){

    	$cek = data_prq::find($req->id_prq);
        
    	if($cek->status > 1)
    		 return redirect('/prq')->withNotif([
	            'label' => 'danger',
	            'err' => $cek->no_prq . ' tidak dapat di perbaharui, karena sedang dalam proses!'
	        ]);   

    	$prq = $this->dispatch(new EditPRQJob($req->all()));
		 return redirect()->back()->withNotif([
            'label' => 'success',
            'err' => $prq->no_prq . ' berhasil diperbaharui!'
        ]);    	
    }

    public function getAdditemprq($tipe = 0){

        $akses = \Me::accessGudang();

        if(empty($tipe) || $tipe > 2 || !is_numeric($tipe) )
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);
        if(!in_array($tipe, $akses))
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Maaf Bukan domain Anda!'
            ]);


    	if(count($this->ids) < 1)
            return redirect('/prq/select')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item di bawah ini'
            ]);

        $items = data_barang::join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->whereIn('id_barang', $this->ids)
            ->where('data_barang.tipe', $tipe)
            ->select('data_barang.*', 'ref_satuan.nm_satuan')->get();

        if(count($items) == 0)
            return redirect('/prq')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);

            $prqs = data_prq::where('status', 1)->where('tipe', $tipe)->get();

        $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);

        return view('Pengadaan.PRQ.AddItemPRQ', [
            'items' => $items,
            'prqs' => $prqs,
            'ids' => $ids,
            'tipe' => $tipe
        ]);

    }

    public function postAdditemprq(Request $req){

        if(($req->id_prq) == 0){
            return redirect()->back()->withNotif([
                'label' => 'danger',
                'err' => 'Maaf, Anda belum menentukan No. Pengajuan'
            ]);
        }

    	$cek = data_prq::find($req->id_prq);
    	if($cek->status > 1)
    		 return redirect('/prq')->withNotif([
	            'label' => 'danger',
	            'err' => $cek->no_prq . ' tidak dapat di perbaharui, karena sedang dalam proses!'
	        ]);   

        if(array_sum($req->qty) < 1)
            return redirect()->back()->withNotif([
                'label' => 'warning',
                'err' => 'Qty tidak boleh semuanya kosong!'
            ]);

        $this->dispatch(new AddItemPRQJob($req->all()));
        $req->session()->forget($this->MySession);
        return redirect('/prq')->withNotif([
                'label' => 'success',
                'err' => 'Pengajuan dengan No.' . $cek->no_prq . ' berhasil ditambahkan!'
            ]);
    }

    public function postDetailprq(Request $req){
    	if($req->ajax()){
    		data_prq::find($req->id)->update([
    			'status' => 4
    		]);

    		return json_encode([
    			'result' => true
    		]);
    	}
    }

}
