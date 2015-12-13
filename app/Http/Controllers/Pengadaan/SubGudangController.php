<?php

namespace App\Http\Controllers\Pengadaan;

use App\User;
use App\Models\data_skb;
use App\Models\ref_gudang;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_departemen;
use App\Models\data_gudang_user;
use App\Models\data_item_gudang;
use App\Models\data_penyesuaian_stok;
use App\Models\data_penyesuaian_stok_item;

use App\Jobs\Pengadaan\SubGudang\CreateAdjustemtJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubGudangController extends Controller {
	
	private $MySession, $ids = [];

    public function __construct(){

        $me = 'gudangADJ-' . \Auth::user()->id_user;
        $this->MySession = $me;
        if(!empty(session()->get($me))){
            foreach(session()->get($me) as $val){
                $this->ids[] = $val['id_barang'];
            }
        }
    }

	public function getIndex(){
		$me = \Me::subgudang();
		$title = $me->access == false  ? 'Sub Gudang' : $me->nm_gudang;
		
		$tipes = [
			1 => 'Obat',
			2 => 'Barang'
		];


		// Have Access
		$items = data_item_gudang::detail()->paginate(10);

		return view('Pengadaan.SubGudang.index', [
			'title' => $title,
			'items' => $items,
			'tipes' => $tipes,
			'kategoris' => ref_kategori::all(),
			'me' => $me,
			'gudangs' => ref_gudang::all()
		]);
	}

	public function getItems(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$me = \Me::subgudang();
			$tipes = [
				1 => 'Obat',
				2 => 'Barang'
			];

			$items = data_item_gudang::detail($req->all())->paginate($req->limit);
			if($items->total() > 0):
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){

					$info = $item->stok_minimal >= ( $item->in - $item->out ) ? '<small class="text-danger semi-bold">(Stok Limit)</small>' : '';
						

					$out .= '
						<tr class="item_' .  $item->id_barang . ' items">
							<td>' . $no . '</td>
							<td>
								<a href="javascript:;" title="' . $item->nm_barang . '" data-toggle="tooltip" data-placement="bottom">' . \Format::substr($item->nm_barang,15) . '</a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="#" data-toggle="modal" data-target="#review" onclick="review(' . $item->id_barang . ')">Lihat</a>
									]</small>
								</div>
							</td>
							<td>
								' . $item->kode . '<br />
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::indoDate($item->created_at) . '</small>
							</td>
							<td title="' . $item->nm_kategori . '">' . \Format::substr($item->nm_kategori,20) . '</td>
							<td>' . $tipes[$item->tipe] . '</td>
							<td class="text-right">
								' . number_format(($item->in - $item->out),0,',','.') . ' ' . $item->nm_satuan . '
								<div>' . $info . '</div>
							</td>
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

			$res['data'] = $out;
			$res['total'] = $items->total();
			$res['pagin'] = $items->render();

			return json_encode($res);

		}
	}

	public function getAccess(){

		$users = data_gudang_user::join('users', 'users.id_user', '=', 'data_gudang_user.id_user')
			->select('data_gudang_user.*', 'users.name')
			->paginate(10);

		return view('Pengadaan.SubGudang.access', [
			'users' 	=> $users,
			'gudangs' 	=> ref_gudang::all()
		]);
	}

	public function postAccess(Request $req){

		data_gudang_user::create([
			'id_user' => $req->user,
			'id_gudang' => $req->gudang
		]);

		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Berhasil ditambahkan'
		]);

	}

	public function getAccessusers(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '<option value="">Pilih Users</option>';

			$users = User::leftJoin('data_gudang_user', 'data_gudang_user.id_user', '=', 'users.id_user')
				->where('users.status', 1)
				->whereIn('users.permission', [1,2])
				->WhereNull('data_gudang_user.id_user')
				->orderby('users.name', 'asc')
				->select('users.*')
				->get();

			foreach($users as $user){
				$out .= '<option value="' . $user->id_user . '">' . $user->name . '</option>';
			}

			$res['content'] = $out;
			return json_encode($res);
		}
	}

	public function getGetusergudang(Request $req){
		if($req->ajax()){

			$res = [];
			$out = '';

			$users = data_gudang_user::show($req->all())->paginate($req->limit);

			$no = $users->currentPage() == 1 ? 1 : ($users->perPage() * $users->currentPage()) - $users->perPage() + 1;
			
			if($users->total() > 0):

			foreach($users as $user){

				$tipe1 =  $user->tipe == 1 ? 'checked="checked"' : '';
				$tipe2 =  $user->tipe == 2 ? 'checked="checked"' : '';

				$select = '<select style="width:100%;">';

				foreach (ref_gudang::all() as $gudang) {
					$sl = $gudang->id_gudang == $user->id_gudang ? 'selected="selected"' : '';
					$select .= '<option value="' . $gudang->id_gudang . '" ' . $sl . '>' . $gudang->nm_gudang . '</option>';
				}

				$select .= '</select>';
				$out .= '
					<tr class="user-' . $user->id_akses_gudang . '">
						<td>' . $no . '</td>
						<td>
							' . $user->name . '
							<div class="link">
								<small>[
									<a href="javascript:;" onclick="del(' . $user->id_akses_gudang . ');" class="text-danger">Hapus</a>
								]</small>
							</div>
						</td>
						<td>
							' . $select . '
						</td>
						<td>
							' .  \Format::indoDate($user->created_at) . '
							<div><small class="text-muted">' . \Format::hari($user->created_at) . ', ' . \Format::jam($user->created_at) . '</small></div>
						</td>
					</tr>
				';
				$no++;
			}

			else:
				$out = '
					<tr>
						<td colspan="4">Tidak ditemukan</td>
					</tr>';
			endif;

			$res['pagin'] 	= $users->render();
			$res['content'] = $out;

			return json_encode($res);

		}
	}

	public function postDelaccesgudang(Request $req){
		if($req->ajax()){
			data_gudang_user::where('id_gudang_user', $req->id)->delete();
			return json_encode([
				'id' => $req->id
			]);
		}
	}

	public function postEditaksesuser(Request $req){
		if($req->ajax()){
			data_gudang_user::find($req->id)->update([
				'id_gudang' => $req->gudang
			]);
			return json_encode([
				'res' => true
			]);
		}
	}

	public function getLimitstok(Request $req){
		if($req->ajax()) {

			$total = data_item_gudang::habis()->count();
			$res = $total > 10 ? '10+' : $total;

			return json_encode([
				'total' => $res
			]);

		}
	}

	public function getAdjustment(){
		$me = \Me::subgudang();

		$items = data_penyesuaian_stok::subgudang()->paginate(10);

		return view('Pengadaan.SubGudang.Adjustment', [
			'me' => $me,
			'items' => $items,
			'gudangs' => ref_gudang::all()
		]);
	}

	public function getAlladj(Request $req){
        if($req->ajax()){
            $res = [];
            $out = '';
            $items = data_penyesuaian_stok::subgudang($req->all())->paginate($req->limit);

            if($items->total() > 0):
	            $no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
	            foreach($items as $item){
	                $out .= '
	                    <tr>
	                        <td>' . $no . '</td>
	                        <td>
	                            ' . $item->no_penyesuaian_stok . '
	                            <div class="link">
	                                <small>[
	                                        <a target="_blank" href="' . url('/subgudang/printadj/' . $item->id_penyesuaian_stok ) . '">Print</a>
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
	           else:
	           	$out = '
	           		<tr>
	           			<td colspan="4">Tidak ditemukan</td>
	           		</tr>
	           ';
	           endif;

            $res['content'] = $out;
            $res['pagin'] = $items->render();

            return json_encode($res);
        }
    }

	public function getSelect(){

		$me = \Me::subgudang();
    	if(!$me->access)
    		return redirect('/subgudang/adjustment')->withNotif([
                'label' => 'warning',
                'err' => 'Penyesuaian stok hanya bisa dilakukan oleh user yang memiliki akses terhadap sub gudang'
            ]);

    	$items = data_item_gudang::adj([], $this->ids)->paginate(10);
    	$kats = ref_kategori::all();

        
		return view('Pengadaan.SubGudang.SelectAdjustment', [
			'items' => $items,
    		'kats' => $kats,
            'tipe' => 0,
            'title' => ''
		]);	
	}

	/* Pemanggilan semua item barang */
    public function getAllitems(Request $req){
    	$result = [];
    	if($req->ajax()):
    		$items = data_item_gudang::adj($req->all(), $this->ids)->paginate($req->limit);
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

    public function getCreateadj(){

    	$me = \Me::subgudang();
    	if(!$me->access)
    		return redirect('/subgudang/adjustment')->withNotif([
                'label' => 'warning',
                'err' => 'Penyesuaian stok hanya bisa dilakukan oleh user yang memiliki akses terhadap sub gudang'
            ]);

        if(count($this->ids) < 1)
            return redirect('/subgudang/select')->withNotif([
                'label' => 'warning',
                'err' => 'Maaf, Anda belum menentukan item Barang yang akan diproses.<br /> Silahkan pilih beberapa item di bawah ini'
            ]);

        $items = data_item_gudang::join('data_barang', 'data_barang.id_barang', '=', 'data_item_gudang.id_barang')
        	->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
            ->whereIn('data_item_gudang.id_barang', $this->ids)
            ->where('data_barang.tipe', 1)
            ->select('data_barang.nm_barang', 'data_barang.kode', 'data_item_gudang.*', 'ref_satuan.nm_satuan')
            ->get();

        if(count($items) == 0)
            return redirect('/subgudang')->withNotif([
                'label' => 'danger',
                'err'   => 'Kesalahan, Silahkan buat pengajuan baru!'
            ]);

        $gudangs = ref_gudang::all();
        
        $ids = [];
        foreach($items as $id){
            $ids[] = $id->id_barang;
        }

        $ids = json_encode($ids);
        
    	return view('Pengadaan.SubGudang.CreateAdjustment', [
    		'items' 	=> $items,
            'gudangs' 	=> $gudangs,
            'ids'   	=> $ids,
            'tipe'  	=> 1,
            'me' 		=> $me
    	]);
    }

    public function postCreateadj(Request $req){
    	$arr = $this->dispatch(new CreateAdjustemtJob($req->all()));
    	if($arr['result'] == true){
    		return redirect('/subgudang/adjustment')->withNotif([
                'label' => $arr['label'],
                'err' => $arr['err']
            ]);
    	}else{
    		$req->session()->forgate($this->MySession);
    		return redirect()->back()->withNotif([
                'label' => $arr['label'],
                'err' => $arr['err']
            ]);
    	}
    }

    public function getPrintadj($id){

        if(empty($id) || !is_numeric($id))
            return redirect('/stockadj');

        $adj = data_penyesuaian_stok::byid($id)->first();

        if($adj == null)
            return redirect('/stockadj');

        if($adj->id_gudang == 0)
        	return redirect('/stockadj');

        $gudang = ref_gudang::find($adj->id_gudang);

        $items = data_penyesuaian_stok_item::byhead($id)->get();
        return view('Print.Pengadaan.ADJ', [
            'adj' => $adj,
            'items' => $items,
            'gudang' => $gudang
        ]);

    }
    

}
