<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_spbm extends Model {

    protected $table 		= 'data_spbm';
	protected $primaryKey 	= 'id_spbm';
	protected $fillable 	= [
		'no_spbm',
		'no_surat_jalan',
		'tgl_terima_barang',
		'tgl_periksa_barang',
		'id_kirim', /* 1:dikirim oleh vendor|2:dikirim oleh Ekspedisi|3:diambil oleh pihak RS */
		'keterangan',
		'id_po',
		'pemeriksa1',
		'pemeriksa2',
		'nm_pengirim',
		'id_karyawan',
		'titipan'
	];

	public function scopeByid($query, $id){
		$gr = $query->join('data_po', 'data_po.id_po', '=', 'data_spbm.id_po')
			->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spbm.id_karyawan')
			->where('data_spbm.id_spbm', $id)
			->select(
				'data_spbm.*', 
				'data_po.no_po', 
				'data_vendor.nm_vendor',
				'data_vendor.alamat',
				'data_vendor.telpon',
				'data_karyawan.nm_depan',
				'data_karyawan.nm_belakang'
			);
	}

	public function scopeShow($query, $req = []){

		$gr = $query->join('data_po', 'data_po.id_po', '=', 'data_spbm.id_po')
			->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_po.id_vendor')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spbm.id_karyawan');
		if(count($req) > 0){
			if(!empty($req['no_spbm']))
				$gr->where('data_spbm.no_spbm', $req['no_spbm']);
			if(!empty($req['no_surat_jalan']))
				$gr->where('data_spbm.no_surat_jalan', $req['no_surat_jalan']);
			if(!empty($req['tgl_terima_barang']))
				$gr->where('data_spbm.tgl_terima_barang', $req['tgl_terima_barang']);
			if(!empty($req['no_po']))
				$gr->where('data_po.no_po', $req['no_po']);
			if(!empty($req['id_kirim']))
				$gr->where('data_spbm.id_kirim', $req['id_kirim']);
			if(!empty($req['id_vendor']))
				$gr->where('data_po.id_vendor', $req['id_vendor']);
			if(!empty($req['titipan']) && $req['titipan'] == 'true')
				$gr->where('data_spbm.titipan', '>', 0);
		}

		$gr->orderby('data_spbm.id_spbm', 'desc')
			->select(
				'data_spbm.*', 
				'data_po.no_po', 
				'data_vendor.nm_vendor',
				'data_karyawan.nm_depan',
				'data_karyawan.nm_belakang'
			);

	}

	public function scopeRekapspbm($query, $req = [])
	{
		$item = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_spbm.id_karyawan');
		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_spbm.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_spbm.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_spbm.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_spbm.*',
			'data_karyawan.nm_depan',
			'data_karyawan.nm_belakang'
			);
	}

	public function rekap()
	{
		return $this->hasMany('App\Models\data_spbm_item','id_spbm');
	}
}
