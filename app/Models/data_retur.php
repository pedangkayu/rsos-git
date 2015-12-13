<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_retur extends Model {

    protected $table 		= 'data_retur';
	protected $primaryKey 	= 'id_retur';
	protected $fillable 	= [
		'no_retur',
		'tipe', /* 1:Internal|2:eksternal */
		'id_gudang_asal',
		'id_po',
		'id_vendor',
		'status',
		'id_karyawan'
	];

	public function scopePembelian($query, $req = []){
		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_retur.id_po')
			->join('data_vendor', 'data_vendor.id_vendor', '=', 'data_retur.id_vendor')
			->where('data_retur.tipe', 2);

		if(count($req) > 0){
			if(!empty($req['no_retur']))
				$item->where('data_retur.no_retur', $req['no_retur']);
			if(!empty($req['no_po']))
				$item->where('data_po.no_po', $req['no_po']);
			if(!empty($req['id_vendor']))
				$item->where('data_vendor.id_vendor', $req['id_vendor']);
			if(!empty($req['tanggal']))
				$item->where(\DB::raw('DATE(data_retur.created_at)'), $req['tanggal']);
		}

		$item->select(
			'data_retur.*',
			'data_po.no_po',
			'data_vendor.nm_vendor',
			'data_vendor.telpon'
		);
	}

	public function scopeGudang($query, $req = []){

		$me = \Me::subgudang();

		$item = $query->where('data_retur.tipe', 1);

		if(count($req) > 0){
			if(!empty($req['no_retur']))
				$item->where('data_retur.no_retur', $req['no_retur']);
			if(!empty($req['tanggal']))
				$item->where(\DB::raw('DATE(data_retur.created_at)'), $req['tanggal']);
		}

		if($me->id_gudang > 0)
			$item->where('data_retur.id_gudang_asal', $me->id_gudang);

		$item->select(
			'data_retur.*'
		);
	}

	public function scopeForvendor($query, $id, $req = []){

		$item = $query->join('data_po', 'data_po.id_po', '=', 'data_retur.id_po')
			->where('data_retur.tipe', 2)
			->where('data_retur.id_vendor', $id);

		if(count($req) > 0){
			if(!empty($req['no_retur']))
				$item->where('data_retur.no_retur', $req['no_retur']);
			if(!empty($req['no_po']))
				$item->where('data_po.no_po', $req['no_po']);
			if(!empty($req['tanggal']))
				$item->where(\DB::raw('DATE(data_retur.created_at)'), $req['tanggal']);
		}

		$item->select(
			'data_retur.*',
			'data_po.no_po'
		);

	}
}
