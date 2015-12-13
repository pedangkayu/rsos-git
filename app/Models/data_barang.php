<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_barang extends Model{
    
	protected $table = 'data_barang';
	protected $primaryKey = 'id_barang';
	protected $fillable = [
		'nm_barang',
		'kode',
		'id_kategori',
		'stok_awal',
		'id_satuan',
		'stok_minimal',
		'status',
		'id_karyawan',
		'in',
		'out',
		'keterangan',
		'tipe', /* 1:Obat|2:Barang */
		'id_klasifikasi',
		'harga_beli',
		'ppn',
		'harga_jual'
	];

	/*Mengambil semua daftar barang*/
	public function scopeDetails($query, $src = null, $kat = 0, $field = 'data_barang.nm_barang', $order = 'asc', $tipe = 0, $kode = 0){
		
		$akses = \Me::accessGudang();

		$item = $query->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
		->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori');
		if(!empty($src)){
			$item->where('data_barang.nm_barang','LIKE', '%' . $src . '%');
		}

		if(count($akses) > 1){
			if(!empty($tipe)){
				$item->where('data_barang.tipe', $tipe);
			}
		}else{
			$item->whereIn('data_barang.tipe', $akses);
		}
		
		if($kat > 0){
			$item->where('data_barang.id_kategori',$kat);
		}
		if(!empty($kode))
			$item->where('data_barang.kode',$kode);	
		$item->where('data_barang.status', 1)
		->orderBy($field, $order)
		->select('data_barang.*', 'ref_satuan.nm_satuan', 'ref_satuan.id_satuan', 'ref_kategori.id_kategori', 'ref_kategori.nm_kategori');
		return $item;
	}
	/*Mengambil semua daftar barang yang sudah masuk limit*/
	public function scopeDetailslimit($query, $src = null, $kat = 0, $field = 'data_barang.nm_barang', $order = 'asc', $tipe = 0, $kode = 0){

		$akses = \Me::accessGudang();

		$item = $query->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
		->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori');
		if(!empty($src))
			$item->where('data_barang.nm_barang','LIKE', '%' . $src . '%');

		if(!empty($tipe)){
			$item->where('data_barang.tipe', $tipe);
		}

		if(count($akses) > 1){
			if(!empty($tipe)){
				$item->where('data_barang.tipe', $tipe);
			}
		}else{
			$item->whereIn('data_barang.tipe', $akses);
		}

		if($kat > 0){
			$item->where('data_barang.id_kategori',$kat);
		}
		if(!empty($kode))
			$item->where('data_barang.kode',$kode);
		$item->where('data_barang.status', 1)
		->where('stok_minimal', '>=', \DB::raw('(`in` - `out`)'))
		->orderBy($field, $order)
		->select('data_barang.*', 'ref_satuan.nm_satuan', 'ref_satuan.id_satuan', 'ref_kategori.id_kategori', 'ref_kategori.nm_kategori');
		return $item;
	}

	/*Mengambil data barang berdasarkan id barang*/
	public function scopeByid($query, $id){
		return $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_barang.id_karyawan')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori')
			->leftJoin('ref_klasifikasi', 'ref_klasifikasi.id_klasifikasi', '=', 'data_barang.id_klasifikasi')
			->where('data_barang.id_barang', $id)
			->select('data_barang.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang', 'ref_kategori.nm_kategori', 'ref_satuan.nm_satuan', 'ref_klasifikasi.nm_klasifikasi');
	}

	/*Mengambil data barang berdasarkan stok limit*/
	public function scopeStoklimit($query){
		$akses = \Me::accessGudang();

		return $query->where('stok_minimal', '>=', \DB::raw('(`in` - `out`)'))
			->whereIn('tipe', $akses);
	}

	/*Pengabil data barang untuk keperluan modul Permohinan barang*/
	public function scopeSrcpmb($query,array $req, $ids){
		$items = $query->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_barang.tipe', 2);
		if(!empty($req['kode']))
			$items->where('data_barang.kode', 'LIKE',  '%' .$req['kode'] . '%');
		if(!empty($req['item']))
			$items->where('data_barang.nm_barang', 'LIKE', '%' . $req['item'] . '%');
		if(!empty($req['kat']))
			$items->where('data_barang.id_kategori',  $req['kat']);
		if(count($ids) > 0)
			$items->whereNotIn('data_barang.id_barang', $ids);
	}

	/*Pengabil data barang untuk keperluan modul Permohinan barang*/
	public function scopeSrcpmo($query,array $req, $ids){
		$items = $query->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_barang.tipe', 1);
		if(!empty($req['kode']))
			$items->where('data_barang.kode', 'LIKE',  '%' .$req['kode'] . '%');
		if(!empty($req['item']))
			$items->where('data_barang.nm_barang', 'LIKE', '%' . $req['item'] . '%');
		if(!empty($req['kat']))
			$items->where('data_barang.id_kategori',  $req['kat']);
		if(count($ids) > 0)
			$items->whereNotIn('data_barang.id_barang', $ids);
	}

	/*Pengabil data barang untuk keperluan modul perngajuan barang*/
	public function scopeSrcprq($query,array $req, $ids, $tipe){

		$akses = \Me::accessGudang();

		$items = $query->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan')
			->where('data_barang.tipe', $tipe);
		if(!empty($req['kode']))
			$items->where('data_barang.kode', 'LIKE',  '%' .$req['kode'] . '%');
		if(!empty($req['item']))
			$items->where('data_barang.nm_barang', 'LIKE', '%' . $req['item'] . '%');
		if(!empty($req['kat']))
			$items->where('data_barang.id_kategori',  $req['kat']);
		if(count($ids) > 0)
			$items->whereNotIn('data_barang.id_barang', $ids);

		$items->select('data_barang.*','ref_satuan.nm_satuan', \DB::raw('(data_barang.in - data_barang.out) AS sisa'))
		->orderby('sisa', 'asc');
	}

	/* FAKTUR */
	public function scopeActive($query, $req = []){
		$item = $query->where('status', 1);
		if(!empty($req['kode']))
			$item->where('kode', $req['kode']);
		if(!empty($req['barang']))
			$item->where('nm_barang','LIKE', '%' . $req['barang'] . '%');
	}

	/* LAPORAN STOK */
	public function scopeLaporanstok($query, $req = []){
		$item = $query->join('ref_klasifikasi', 'ref_klasifikasi.id_klasifikasi', '=', 'data_barang.id_klasifikasi')
			->join('ref_kategori', 'ref_kategori.id_kategori', '=', 'data_barang.id_kategori')
			->join('ref_satuan', 'ref_satuan.id_satuan', '=', 'data_barang.id_satuan');

		if($req['tipe'] == 'klasifikasi')
			$item->whereIn('data_barang.id_klasifikasi', $req['id_klasifikasi']);
		if($req['tipe'] == 'kategori')
			$item->whereIn('data_barang.id_kategori', $req['id_kategori']);
		if(!empty($req['jenis']))
			$item->where('data_barang.tipe', $req['jenis']);

		$item->select(
			'data_barang.kode',
			'data_barang.nm_barang',
			'ref_kategori.nm_kategori',
			'data_barang.tipe',
			'ref_klasifikasi.nm_klasifikasi',
			'ref_satuan.nm_satuan',
			'data_barang.harga_beli',
			'data_barang.in',
			'data_barang.out'

		);
	}
}
