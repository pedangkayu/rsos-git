<?php

namespace App\Http\Controllers\Pengadaan;

use App\User;
use App\Models\ref_satuan;
use App\Models\data_harga;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\data_karyawan;
use App\Models\data_spbm_item;
use App\Models\ref_klasifikasi;
use App\Models\data_item_gudang;
use App\Models\data_akses_gudang;
use App\Models\data_barang_detail;
use App\Models\ref_konversi_satuan;

use App\Jobs\Pengadaan\CreateBarangJob as InsertBarang;
use App\Jobs\Pengadaan\UpdateBarangJob as UpdateBarang;
use App\Jobs\Pengadaan\AddAccessUserJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogistikController extends Controller{
    
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	* Daftar Barang Logistik
	* @access Admin Logistik
	* @author hexters
	*/
	public function getIndex(){

		$items = data_barang::details()->paginate(10);
		
		$tipes = [
			1 => 'Obat',
			2 => 'Barang'
		];

		$akses = \Me::accessGudang();

		if(count($akses) > 1){
			$title = 'Logistik';
		}else{
			if(count($akses) == 0){
				$title = 'Tidak ada Akses';
			}else{
				$tipe = $akses[0];
				$title = $tipe == 1 ? 'Master Obat-obatan' : 'Master Barang';
			}
		}

		return view('Pengadaan.MasterLogistik', [
			'items' => $items,
			'kategoris' => ref_kategori::all(),
			'tipes' => $tipes,
			'akses' => $akses,
			'title' => $title
		]);
	}

	public function getAllitems(Request $req){
		if($req->ajax()):
			$res = [];

			$tipes = [
				1 => 'Obat',
				2 => 'Barang'
			];

			$fileds = $req->sort;
			switch($fileds){
				case"barang":
					$filed = 'data_barang.nm_barang';
					break;
				case"kode":
					$filed = 'data_barang.kode';
					break;
				case"kategori":
					$filed = 'ref_kategori.nm_kategori';
					break;
				case"waktu":
					$filed = 'data_barang.created_at';
					break;
				default:
					$filed = 'data_barang.nm_barang';
			}

			if($req->stok === 'true'){
				$items = data_barang::detailslimit($req->src, $req->kat, $filed, $req->orderby, $req->tipe, $req->kode)->paginate($req->limit);
			}else{
				$items = data_barang::details($req->src, $req->kat, $filed, $req->orderby, $req->tipe, $req->kode)->paginate($req->limit);
			}

			$out = '';
			$total = $items->total();
			if($total > 0){
				$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
				foreach($items as $item){

					$link = \Auth::user()->permission > 1 ? '| <a href="' . url('/logistik/update/' . $item->id_barang ) . '">Edit</a> ' : '';
					$info = $item->stok_minimal >= ( $item->in - $item->out ) ? '<small class="text-danger semi-bold">(Stok Limit)</small>' : '';
					$btn = \Auth::user()->permission > 2 ? '<button type="button" class="close hapus" onclick="hapus(\'' . $item->nm_barang . '\', ' . $item->id_barang . ');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' : '';

					$out .= '
						<tr class="item_' .  $item->id_barang . ' items">
							<td>' . $no . '</td>
							<td>
								<a href="javascript:;" title="' . $item->nm_barang . '" data-toggle="tooltip" data-placement="bottom">' . \Format::substr($item->nm_barang,15) . '</a>
								<div style="display:none;" class="tbl-opsi">
									<small>[
										<a href="#" data-toggle="modal" data-target="#review" onclick="review(' . $item->id_barang . ')">Lihat</a>
										| <a href="' . url('/logistik/detail/' . $item->id_barang) . '">Rinci</a>
										' . $link . '
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
							<td class="text-right">
								' . $btn . '
							</td>
						</tr>
					';
					$no++;
				}
			}else{
				$out = '
					<tr>
						<td colspan="7">Tidak ditemukan</td>
					</tr>
				';
			}

			$res['data'] = $out;
			$res['pagin'] = $items->render();
			$res['total'] = number_format($total,0,',','.');

			return json_encode($res);

			endif;
	}

	/* Delete Item barang */
	public function postDestroy(Request $req){
		$barang = data_barang::find($req->id);
		$barang->update([
			'status' => 0
		]);

		\Loguser::create('Menghapus data master barang Kode. ' . $barang->kode);
		return json_encode([
			'result' => true
		]);
	}

	/**
	* Tambah item barang
	* @access Admin Logistik
	* @author @hexters
	*/
	public function getAdd(){

		$access = \Me::accessGudang();

		if(count($access) == 0)
			return redirect('/logistik')->withNotif([
				'label' => 'danger',
				'err' => 'Hubngi atasan anda untuk memiliki ijin mengakses Gudang Logistik !'
			]);

		return view('Pengadaan.Additem', [
			'kategoris' => ref_kategori::all(),
			'satuan' => ref_satuan::all(),
			'klasifikasi' => ref_klasifikasi::all(),
			'akses' => $access
		]);
	}

	/**
	* Menyimpan barang
	* @access Admin Logistik
	* @author @hexters
	*/
	public function postAdd(Request $req){
		$this->validate($req, [
			'nm_barang' => 'required',
			'id_kategori' => 'required',
			'id_satuan' => 'required',
			'stok_awal' => 'required|numeric',
			'stok_minimal' => 'required|numeric',
		]);

		$err = $this->dispatch(new InsertBarang($req->all()));
		return redirect()->back()->withNotif([
			'label' => $err['label'],
			'err' => $err['err']
		]);
	}

	/**
	* Edit barang
	* @access Admin Logistik
	* @author @hexters
	*/
	public function getUpdate($id){
		$item = data_barang::find($id);

		if($item == null)		
			return redirect('/logistik')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf, Tidak ditemukan !'
			]);

		if(!in_array($item->tipe, \Me::accessGudang()))
			return redirect('/logistik')->withNotif([
				'label' => 'danger',
				'err' => 'Maaf Bukan domain anda!'
			]);

		$user = data_karyawan::find($item->id_karyawan);
		$details = data_barang_detail::where('id_barang', $item->id_barang)->get();
		$konversi = ref_konversi_satuan::where('ref_konversi_satuan.id_barang', $id)->whereNotIn('ref_konversi_satuan.id_satuan_max', [$item->id_satuan])->get();
		return view('Pengadaan.Update', [
			'kategoris' => ref_kategori::all(),
			'satuan' => ref_satuan::all(),
			'item' => $item,
			'oleh' => $user->nm_depan . ' ' . $user->nm_belakang,
			'details' => $details,
			'klasifikasi' => ref_klasifikasi::all(),
			'konversi' => $konversi
		]);
	}

	/**
	* Penyimpanan pembaharuan
	*/
	public function postUpdate(Request $req){
		$err = $this->dispatch(new UpdateBarang($req->all()));
		return redirect()->back()->withNotif([
			'label' => $err['label'],
			'err' => $err['err']
		]);
	}

	/**
	* Detail barang
	* @access From Ajax
	* @author @hexters
	*/
	public function postReview(Request $req){
		$result = [];
		if($req->ajax()):
			$result['result'] 	= true;
			
			$item = data_barang::byid($req->id)->first();
			$details = data_barang_detail::whereId_barang($req->id)->get();
			$result['kode'] = $item->kode;
			$tipes = [
				1 => 'Obat-obatan',
				2 => 'Barang'
			];

			$warnig = $item->stok_minimal >= ( $item->in - $item->out ) ? '<span class="text-danger semi-bold">(Stok Limit)</span>' : '';

			/*star grid*/
			$html = '
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<div class="row">
							<div class="col-sm-6">
								<address>
									<strong>Nama Barang</strong>
									<p>' . $item->nm_barang . '</p>
									<strong>Kategori</strong>
									<p>' . $item->nm_kategori . '</p>
									<strong>Tanggal Buat</strong>
									<p>' . \Format::hari($item->created_at) . ', ' . \Format::indoDate($item->created_at) . '<br />' . \Format::jam($item->created_at) . '</p>
									<strong>Oleh</strong>
									<p>' . $item->nm_depan . '  ' . $item->nm_belakang . '</p>
								</address>
							</div>
							<div class="col-sm-6">
							<address class="stok-barang">
								<strong>Jenis Barang</strong>
								<p>' . $tipes[$item->tipe] . '</p>
								<strong>Stok Awal</strong>
								<p>' . number_format($item->stok_awal,0,',','.') . ' ' . $item->nm_satuan . '</p>
								<strong>Stok Minimal</strong>
								<p>' . number_format($item->stok_minimal,0,',','.') . ' ' . $item->nm_satuan . '</p>
								<strong>Sisa Stok</strong>
								<p>' . number_format(( $item->in - $item->out ),0,',','.') . ' ' . $item->nm_satuan . ' ' . $warnig . '</p>
							</address>
							</div>
						</div>
					</div>
				</div>
			'; /*End grid*/

			if(count($details) > 0){
				$html .= '
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<address>
							';

					foreach($details as $detail){
						$html .= '
							<p>
							<div class="row">
								<div class="col-sm-6">
									<strong>' . $detail->label . '</strong>
								</div>
								<div class="col-sm-6">' . $detail->nm_detail . '</div>
							</div>
							</p>
						';
					}

				$html .= '
							</address>
						</div>
					</div>
				';
			}

			$result['content'] = $html;
			$result['link'] = '<a href="' . url('/logistik/detail/' . $item->id_barang) . '" class="btn btn-primary">Lihat Lebih Rinci</a>';

		else:
			$result['result'] 	= false;
			$result['error'] 	= 'restrict';
		endif;
		return json_encode($result);
	}

	/**
	* Rincian detail barang
	*/
	public function getDetail($id){
		$item = data_barang::byid($id)->first();

			if(count($item) < 1)
				return redirect('/logistik');

		$details = data_barang_detail::whereId_barang($id)->get();
		return view('Pengadaan.Detail',[
			'item' => $item,
			'details' => $details,
			'tipes' => [1=>'Obat-obatan',2=>'Barang']
		]);
	}
	/**
	* Mengambil data stok berdasarkan Poli
	* @access Ajax
	*/
	public function postPoli(Request $req){
		$result = [];
		if($req->ajax()):
			$result['result'] = true;
			$items = data_item_gudang::byidbarang($req->id);
			$html = '';
			if(count($items) > 0){
				foreach($items as $item){
					$stok = $item->in - $item->out;
					$html .= '
						<tr>
							<td>' . $item->nm_gudang . '</td>
							<td class="text-right">' . number_format($stok,0,',','.') . ' ' . $item->nm_satuan . '</td>
						</tr>
					';
				}
			}else{
				$html = '
					<tr>
						<td colspan="2">Tidak ditemukan</td>
					</tr>
				';
			}

			$result['content'] = $html;
			else:
				$result['result'] = false;
				$result['err'] = 'restrict';
			endif;
		return json_encode($result);
	}

	/**
	* Mengambil data limit Stok
	* @access Ajax
	*/
	public function getLimitstok(Request $req){
		
		if($req->ajax()){
			$total = data_barang::stoklimit()->count();
			return json_encode([
				'total' => $total
			]);
		}
	}

	/**
	* Menampilkan data LIMIT
	*/
	public function getLimit(){

		$src 	= null;
		$kat 	= 0;
		$order 	= 'asc';
		$limit 	= 10;
		$tipe = 0;
		if(isset($_GET['src'])){
			$src 	= $_GET['src'];
			$kat 	= is_numeric($_GET['kat']) ? $_GET['kat'] : 0;
			$order 	= $_GET['orderby'];
			$limit 	= is_numeric($_GET['limit']) ? $_GET['limit'] : 10;
			$tipe = empty($_GET['tipe']) ? 0 : $_GET['tipe'];
		}
		$fileds = isset($_GET['sort']) ? $_GET['sort'] : null;
		switch($fileds){
			case"barang":
				$filed = 'data_barang.nm_barang';
				break;
			case"kode":
				$filed = 'data_barang.kode';
				break;
			case"kategori":
				$filed = 'ref_kategori.nm_kategori';
				break;
			case"waktu":
				$filed = 'data_barang.created_at';
				break;
			default:
				$filed = 'data_barang.nm_barang';
		}

		$items = data_barang::detailslimit($src, $kat, $filed, $order, $tipe)->paginate($limit);
		
		$tipes = [
			1 => 'Obat',
			2 => 'Barang'
		];

		return view('Pengadaan.LimitStok', [
			'items' => $items,
			'kategoris' => ref_kategori::all(),
			'src' => $src,
			'kat' => $kat,
			'filed' => $fileds,
			'order' => $order,
			'limit' => $limit,
			'tipes' => $tipes,
			'tipe' => $tipe
		]);
	}

	public function getAccess(){
		if(\Auth::user()->permission < 3)
			return redirect('/logistik');

		$users = data_akses_gudang::show()
			->paginate(10);

		$tipe = [
			1 => 'Obat',
			2 => 'Barang Umum'
		];

		return view('Pengadaan.AccessUsers', [
			'users' => $users,
			'tipe' => $tipe
		]);
	}

	public function postAccess(Request $req){

		$this->dispatch(new AddAccessUserJob($req->all()));
		return redirect()->back()->withNotif([
			'label' => 'success',
			'err' => 'Berhail ditambahkan'
		]);

	}

	public function getAccessusers(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '<option value="">Pilih Users</option>';

			$users = User::leftJoin('data_akses_gudang', 'data_akses_gudang.id_user', '=', 'users.id_user')
				->where('users.status', 1)
				->whereIn('users.permission', [1,2])
				->WhereNull('data_akses_gudang.id_user')
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

	public function postEditaksesuser(Request $req){
		if($req->ajax()){
			data_akses_gudang::find($req->id)->update([
				'tipe' => $req->tipe
			]);
			\Loguser::create('Melakukan perubahan User akses gudang!');
			return json_encode([
				'result' => true
			]);
		}
	}

	public function postDelaccesgudang(Request $req){
		if($req->ajax()){
			data_akses_gudang::find($req->id)->delete();
			\Loguser::create('Menghapus User akses gudang!');
			return json_encode([
				'id' => $req->id
			]);
		}
	}

	public function getGetusergudang(Request $req){
		if($req->ajax()){

			$res = [];
			$out = '';

			$users = data_akses_gudang::show($req->all())
				->paginate($req->limit);

			$no = $users->currentPage() == 1 ? 1 : ($users->perPage() * $users->currentPage()) - $users->perPage() + 1;
			
			if($users->total() > 0):

			foreach($users as $user){

				$tipe1 =  $user->tipe == 1 ? 'checked="checked"' : '';
				$tipe2 =  $user->tipe == 2 ? 'checked="checked"' : '';

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
							<label for="obat' . $user->id_akses_gudang . '">
								<input type="radio" value="1" name="tipe' . $user->id_akses_gudang . '" onclick="edit(1, ' .$user->id_akses_gudang . ');" id="obat' . $user->id_akses_gudang . '" ' . $tipe1 . '> 
								<small>Obat</small>
							</label>
							
							<label for="barang' . $user->id_akses_gudang . '">
								<input type="radio" value="2" name="tipe' . $user->id_akses_gudang . '" onclick="edit(2, ' . $user->id_akses_gudang . ');" id="barang' . $user->id_akses_gudang . '" ' . $tipe2 . '>
								<small>barang</small>
							</label>
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

	public function getLogharga(Request $req){
		if($req->ajax()){
			$res = [];
			
			$harga = data_harga::join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_harga.id_karyawan')
				->leftJoin('data_po', 'data_po.id_po', '=', 'data_harga.id_po')
				->where('data_harga.id_barang', $req->id)
				->where('data_harga.tipe', $req->tipe)
				->orderby('data_harga.id_harga', 'desc')
				->select('data_harga.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang', 'data_po.no_po', 'data_po.id_po')
				->paginate(10);

			$hpo 	= $req->tipe == 1 ? '<th>PO</th>' : '';

			$out = '<table class="table table-striped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th class="text-right">Harga</th>
						<th>Oleh</th>
						' . $hpo . '
					<tr>
				</thead>
				<tbody>
			';


			if($harga->total() > 0):

				foreach($harga as $item){
					$po = $item->no_po == null ? '-' : '<a href="' . url('/po/print/' . $item->id_po) . '" target="_blank">' . $item->no_po . '</a>';
					$tpo 	= $req->tipe == 1 ? '<td>' . $po . '</td>' : '';
					$out .= '
						<tr title="' . $item->keterangan . '">
							<td>
								' . \Format::indoDate($item->created_at) . '
								<div><small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small></div>
							</td>
							<td class="text-right">' . number_format($item->harga,0,',','.') . '</td>
							<td>' . $item->nm_depan . ' ' . $item->nm_belakang . '</td>
							' . $tpo . '
						</tr>
					';
				}
			else:
				$out .= '<tr>
					<td colspan="4">Tidak ditemukan</td>
				</tr>';
			endif;

			$out .= '</tbody><table>';

			$res['pagin'] 	= $harga->render();
			$res['content'] = $out;

			return json_encode($res);

		}
	}

	public function getListexpired(Request $req){
		if($req->ajax()){
			$items = data_spbm_item::join('data_spbm', 'data_spbm.id_spbm', '=', 'data_spbm_item.id_spbm')
				->where('data_spbm_item.id_barang', $req->id_barang)
				->select('data_spbm_item.tgl_exp', 'data_spbm.no_spbm', 'data_spbm.id_spbm')
				->paginate(5);

			$res = [];
			$out = '';
			if($items->total() > 0):
			foreach($items as $item){
				$out .= '
					<tr>
						<td>
							<a href="' . url('/gr/print/' . $item->id_spbm) . '" target="_blank">' . $item->no_spbm . '</a>
						</td>
						<td class="text-right">' . \Format::indoDate($item->tgl_exp) . '</td>
					</tr>
				';
			}
			else:
				$out = '
					<tr>
						<td colspan="2">Tidak ditemukan</td>
					</tr>
				';
			endif;

			$res['content'] = $out;
			$res['pagin'] = $items->render();
			$res['id_barang'] = $req->id_barang;

			return json_encode($res);
		}
	}

}
