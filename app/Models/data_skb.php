<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_skb extends Model {

    protected $table 		= 'data_skb';
	protected $primaryKey 	= 'id_skb';
	protected $fillable 	= [
		'id_spb',
		'no_skb',
		'id_petugas',
		'id_departemen',
		'keterangan',
		'status',
		'tipe'
	];
	
	public function scopeListskb($query, $req = []){

		$akses = \Me::accessGudang();

		$skb = $query->join('data_spb', 'data_spb.id_spb', '=', 'data_skb.id_spb')
			->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_skb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_skb.id_petugas')
			->whereIn('data_skb.tipe', $akses);
		if(count($req) > 0){
			if(!empty($req['no_spb']))
				$skb->where('data_spb.no_spb', $req['no_spb']);
			if(!empty($req['no_skb']))
				$skb->where('data_skb.no_skb', $req['no_skb']);
			if(!empty($req['dep']))
				$skb->where('data_skb.id_departemen', $req['dep']);
			if(!empty($req['tanggal']))
				$skb->where(\DB::raw('DATE(data_skb.created_at)'), $req['tanggal']);
		}
		$skb->select(
			'data_skb.*',
			'data_spb.id_spb',
			'data_spb.no_spb',
			'data_departemen.nm_departemen',
			'data_karyawan.nm_depan',
			'data_karyawan.nm_belakang'
		);
	}

	public function scopeByid($query, $id){
		return $query->join('data_spb', 'data_spb.id_spb', '=', 'data_skb.id_spb')
			->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_skb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_skb.id_petugas')
			->where('data_skb.id_skb', $id)
			->select(
				'data_skb.*',
				'data_spb.no_spb',
				'data_spb.status AS status_spb',
				'data_departemen.nm_departemen',
				'data_karyawan.nm_depan',
				'data_karyawan.nm_belakang'
			)
			->first();
	}

	public function scopeRetur($query, $req = []){

		$skb = $query->join('data_spb', 'data_spb.id_spb', '=', 'data_skb.id_spb')
			->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_skb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_skb.id_petugas')
			->where('data_skb.tipe', 1)
			->where('data_spb.id_departemen', \Me::data()->id_departemen);

		if(count($req) > 0){
			if(!empty($req['no_spb']))
				$skb->where('data_spb.no_spb', $req['no_spb']);
			if(!empty($req['no_skb']))
				$skb->where('data_skb.no_skb', $req['no_skb']);
			if(!empty($req['dep']))
				$skb->where('data_skb.id_departemen', $req['dep']);
			if(!empty($req['tanggal']))
				$skb->where(\DB::raw('DATE(data_skb.created_at)'), $req['tanggal']);
		}
		$skb->where('data_skb.status', 1)
			->select(
			'data_skb.*',
			'data_spb.id_spb',
			'data_spb.no_spb',
			'data_departemen.nm_departemen',
			'data_karyawan.nm_depan',
			'data_karyawan.nm_belakang'
		);
	}

	public function scopeRekapskb($query, $req = []){
		$item = $query->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_skb.id_departemen')
			->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_skb.id_petugas');
			
		if(!empty($req['tipe']))
			$item->where('data_barang.tipe', $req['tipe']);

		if(!empty($req['waktu']) && $req['waktu'] == 1)
			$item->where(\DB::raw('MONTH(data_skb.created_at)'), $req['bulan'])
				->where(\DB::raw('YEAR(data_skb.created_at)'), $req['tahun']);
		else if(!empty($req['waktu']) && $req['waktu'] == 2)
			$item->whereBetween(\DB::raw('DATE(data_skb.created_at)'), [$req['dari'], $req['sampai']]);

		$item->select(
			'data_skb.*',
			'data_departemen.nm_departemen',
			'data_karyawan.nm_depan',
			'data_karyawan.nm_belakang'
			);
	}

	public function rekap()
	{
		return $this->hasMany('App\Models\data_skb_item','id_skb');
	}

}
