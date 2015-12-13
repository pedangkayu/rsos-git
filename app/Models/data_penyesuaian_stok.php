<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_penyesuaian_stok extends Model {

    protected $table 		= 'data_penyesuaian_stok';
	protected $primaryKey 	= 'id_penyesuaian_stok';
	protected $fillable 	= [
		'no_penyesuaian_stok',
		'tipe', /* 1:obat|2:barang */
		'id_karyawan',
		'keterangan',
		'status',
		'tanggal',
		'id_gudang'
	];

	public function scopeShow($query, $req = []){
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_penyesuaian_stok.id_karyawan')
			->where('status', 1)
			->where('id_gudang', 0);
		if(count($req) > 0){
			if(!empty($req['kode']))
				$item->where('data_penyesuaian_stok.no_penyesuaian_stok', $req['kode']);
			if(!empty($req['tanggal']))
				$item->where('data_penyesuaian_stok.tanggal', $req['tanggal']);
			if(!empty($req['tipe']))
				$item->where('data_penyesuaian_stok.tipe', $req['tipe']);
		}

		$akses = \Me::statusGudang();
		if(in_array($akses, [1,2]))
			$item->where('tipe', $akses);

		$item->orderby('data_penyesuaian_stok.id_penyesuaian_stok', 'desc')
			->select('data_penyesuaian_stok.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}

	public function scopeByid($query, $id){
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_penyesuaian_stok.id_karyawan')
			->where('status', 1)
			->where('data_penyesuaian_stok.id_penyesuaian_stok', $id)
			->select('data_penyesuaian_stok.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}

	public function scopeSubgudang($query, $req = []){
		$me = \Me::subgudang();
		$user = \Auth::user()->permission;
		$access = $me->access == false && $user > 2 ? 'admin' : 'user';

		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_penyesuaian_stok.id_karyawan')
			->where('status', 1);

		if($access == 'admin')
			$item->whereNotIn('data_penyesuaian_stok.id_gudang', [0]);
		else
			$item->where('data_penyesuaian_stok.id_gudang', $me->id_gudang);

		if(count($req) > 0){
			if(!empty($req['kode']))
				$item->where('data_penyesuaian_stok.no_penyesuaian_stok', $req['kode']);
			if(!empty($req['tanggal']))
				$item->where('data_penyesuaian_stok.tanggal', $req['tanggal']);
			if(!empty($req['id_gudang']))
				$item->where('data_penyesuaian_stok.id_gudang', $req['id_gudang']);
		}

		$akses = \Me::statusGudang();
		if(in_array($akses, [1,2]))
			$item->where('tipe', $akses);

		$item->orderby('data_penyesuaian_stok.id_penyesuaian_stok', 'desc')
			->select('data_penyesuaian_stok.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}
}
