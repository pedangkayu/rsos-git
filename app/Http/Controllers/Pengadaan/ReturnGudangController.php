<?php

namespace App\Http\Controllers\Pengadaan;

use App\Models\data_skb;
use App\Models\data_retur;
use App\Models\data_skb_item;
use App\Models\data_retur_item;

use App\Jobs\Pengadaan\ReturGudang\CreateReturGudangJob;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReturnGudangController extends Controller {
    
    public function getIndex(){

    	$items = data_retur::gudang()->paginate(10);
		return view('Pengadaan.ReturGudang.Index', [
			'items' => $items
		]);
    }

    public function getAllretur(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$items = data_retur::gudang($req->all())->paginate($req->limit);
			if($items->total() > 0 ):
			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
			foreach($items as $item){
				$out .= '
					<tr>
						<td>' . $no . '</td>
						<td>' . $item->no_retur . '</td>
						<td>
							' . \Format::indoDate2($item->created_at) . '<br />
							<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
						</td>
						<td class="text-right">
							<a class="btn btn-white" href="' . url('/returgudang/print/' . $item->id_retur) . '" target="_blank"><i class="fa fa-print"></i></a>
						</td>
					</tr>
				';
				$no++;
			}
			else:
				$out = '
					<tr>
						<td colspan="4">Tidak ditemukan!</td>
					</tr>
				';
			endif;

			$res['content'] = $out;
			$res['pagin'] = $items->render();

			return json_encode($res);

		}
	}

    public function getSkb(){

    	$items = data_skb::retur()->paginate(10);
    	return view('Pengadaan.ReturGudang.SKB', [
    		'items' => $items,
    		'btn' => 2
    	]);
    }

    public function getAllskb(Request $req){
    	if($req->ajax()){
    		$res = [];
    		$out = '';
    		$items = data_skb::retur($req->all())->paginate($req->limit);

    		if($items->total() > 0):
    			$no = $items->currentPage() == 1 ? 1 : ($items->perPage() * $items->currentPage()) - $items->perPage() + 1;
    			foreach($items as $item){
    				$tipe = $item->tipe == 1 ? 'Obat' : 'Barang';
    				$out .= '
    					<tr>
							<td>' . $no . '</td>
							<td>
								<div>' . $item->no_skb . '</div>
								<div class="link text-muted">
									<small>
										[
											<a href="' . url('/returgudang/create/' . $item->id_skb) . '">Proses</a>
											| <a href="' . url('/skb/print/' . $item->id_skb) . '" target="_blank">Print</a>
										]
									</small>
								</div>
							</td>
							<td>' . $item->no_spb . '</td>
							<td>' . $tipe . '</td>
							<td>' . $item->nm_departemen . '</td>
							<td>
								' . \Format::indoDate($item->created_at) . '<br />
								<small class="text-muted">' . \Format::hari($item->created_at) . ', ' . \Format::jam($item->created_at) . '</small>
							</td>
						</tr>
    				';
    				$no++;
    			}
    		else:
    			$out = '
    				<tr>
    					<td colspan="6">Tidak ditemukan</td>
    				</tr>
    			';
    		endif;

    		$res['content'] = $out;
    		$res['pagin'] = $items->render();

    		return json_encode($res);

    	}
    }

    public function getCreate($id){
    	$skb 	= data_skb::find($id);
    	if($skb == null)
    		return redirect('/returgudang')->withNotif([
    			'label' => 'danger',
    			'err' => 'Tidak ditemukan!'
    		]);

    	if($skb->tipe > 1)
    		return redirect('/returgudang')->withNotif([
    			'label' => 'danger',
    			'err' => 'Tidak ditemukan!'
    		]);

    	$items 	= data_skb_item::retur($id)->get();

    	$me = \Me::subgudang();

    	return view('Pengadaan.ReturGudang.Create', [
    		'skb' => $skb,
    		'items' => $items,
    		'me' => $me
    	]);
    }

    public function postCreate(Request $req){
    	if($req->access == 'false')
    		return redirect()->back()->withNotif([
    			'label' => 'danger',
    			'err' => 'Maaf Anda tidak memiliki akses untuk melakukan pengenbalian barang!'
    		]);

    	if(array_sum($req->qty) == 0)
    		return redirect()->back()->withNotif([
    			'label' => 'danger',
    			'err' => 'Qty tidak boleh kosong seluruhnya!'
    		]);

    	$arr = $this->dispatch(new CreateReturGudangJob($req->all()));

    	if($arr['result']){
    		return redirect('/returgudang')->withNotif([
    			'label' => $arr['label'],
    			'err' => $arr['err']
    		]);
    	}else{
    		return redirect()->back()->withNotif([
    			'label' => $arr['label'],
    			'err' => $arr['err']
    		]);
    	}

    }

    public function getPrint($id){
		if(empty($id) || !is_numeric($id))
			return redirect('/returgudang')->withNotif([
				'label' => 'danger',
				'err' => 'Tidak ditemukan!'
			]);

		$retur = data_retur::join('ref_gudang', 'ref_gudang.id_gudang', '=', 'data_retur.id_gudang_asal')
			->where('id_retur',$id)
			->first();

		$items = data_retur_item::join('data_barang','data_retur_item.id_barang','=','data_barang.id_barang')
			->join('ref_satuan','ref_satuan.id_satuan','=','data_retur_item.id_satuan')
			->where('data_retur_item.id_retur',$id)
			->get();

		return view('Print.Pengadaan.Retur.printRetur', [
			'retur' => $retur,
			'items' => $items
		]);
	}

}
