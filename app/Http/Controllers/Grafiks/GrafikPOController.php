<?php

namespace App\Http\Controllers\Grafiks;

use App\Models\data_po;
use App\Models\data_po_item;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GrafikPOController extends Controller {
    
	public function getIndex(){
		return view('Grafiks.PO.Index');
	}

	public function getDashboard(Request $req){
		if($req->ajax()){

			$res = [];
			$obt = [];
			$brg = [];
			$grf = [];
			$ids = [];
			$vdr = [];
			/* ------------- OBAT -------------------*/
			$obat 	= data_po_item::grafikobatlimit(5)->get();
			foreach($obat as $o){
				$obt[] = [
					'obat' 	=> $o->kode,
					'value' => $o->total
				];
				$ids[] = $o->id_item;
			}

			$res['obat'] = $obt;

			/* ------------- BARANG -------------------*/
			$barang = data_po_item::grafikbaranglimit(5)->get();
			foreach($barang as $b){
				$brg[] = [
					'barang' 	=> $b->kode,
					'value' 	=> $b->total
				];
				$ids[] = $b->id_item;
			}

			$res['barang'] = $brg;

			/* ------------- VENDOR -------------------- */
			$vendors = data_po::top()->get();
			$vtbl = [];
			foreach($vendors as $v){
				$vdr[] = [
					'vendor' 	=> $v->kode,
					'value' 	=> $v->total
				];
				$vtbl[] = [
					'kode' 		=> $v->kode,
					'nama' 		=> $v->nm_vendor,
					'total' 	=> $v->total,
					'alamat' 	=> $v->alamat,
					'telpon' 	=> $v->telpon,
				];
			}

			$res['vendor'] = $vdr;
			$res['tablevendor'] = $vtbl;

			return json_encode($res);

		}
	}
	
	public function getPembelian(){
		return view('Grafiks.PO.Pembelian');
	}

	public function getDatapembelian(Request $req){
		if($req->ajax()){
			$res = [];
			$out = '';

			$kat = [];
			$seri = [];
			
			$items = data_po::grafikpembelian($req->tahun)->get();
			foreach($items as $item){
				$kat[] = \Format::indoDate2($item->tanggal);
				$seri[] = (INT) $item->total;
			}
			$res['po'] = [
				'kategori' => $kat,
				'data'	=> $seri
			];
			
			$kato = [];
			$serio = [];
			$obats = data_po_item::grafikpembelian(1, $req->tahun)->get();
			foreach($obats as $o){
				$kato[] = \Format::indoDate2($o->tanggal);
				$serio[] = (INT) $o->total;
			}

			$res['obat'] = [
				'kategori' => $kato,
				'data'	=> $serio
			];

			$katb = [];
			$serib = [];
			$barangs = data_po_item::grafikpembelian(2, $req->tahun)->get();
			foreach($barangs as $b){
				$katb[] = \Format::indoDate2($b->tanggal);
				$serib[] = (INT) $b->total;
			}

			$res['barang'] = [
				'kategori' => $katb,
				'data'	=> $serib
			];

			return json_encode($res);
		}
	}

}
