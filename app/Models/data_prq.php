<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_prq extends Model {
    
	protected $table 		= 'data_prq';
	protected $primaryKey 	= 'id_prq';
	protected $fillable 	= [
		'no_prq',
		'target',
		'id_pemohon',
		'id_acc',
		'keterangan',
		'status', /* 1:baru|2:proses|3:selesai|4:hapus */
		'tipe',
		'titipan',
		'tgl_approval'
	];


	public function scopeListprq($query, $req = [], $status = 1){

		$akses = \Me::accessGudang();

		$prq = $query->join('data_karyawan AS a', 'a.id_karyawan', '=', 'data_prq.id_pemohon')
			->whereIn('data_prq.tipe', $akses);
			if(count($req) > 0){
				if(!empty($req['no_prq']))
					$prq->where('data_prq.no_prq', $req['no_prq']);
				if(!empty($req['tanggal']))
					$prq->where(\DB::raw('DATE(`data_prq`.`created_at`)'), $req['tanggal']);
				if(!empty($req['deadline']))
					$prq->where('data_prq.target', $req['deadline']);
				if(!empty($req['titipan']) && $req['titipan'] == 'true')
					$prq->where('data_prq.titipan', '>', 0);
			}

		if(!empty($status)){
			if(is_array($status))
				$prq->whereIn('data_prq.status', $status);
			else
				$prq->where('data_prq.status', $status);
		}
		
		$prq->select('data_prq.*', 'a.nm_depan', 'a.nm_belakang');
		return $prq;
	}

	public function scopeForsph($query, $req, $status = 1){
		
		if(is_array($status))
			$prq = $query->whereIn('status', $status);
		else
			$prq = $query->where('status', $status);

		if(!empty($req)){
			if(!empty($req['no_prq']))
				$prq->where('no_prq', $req['no_prq']);
			if(!empty($req['deadline']))
				$prq->where('target', $req['deadline']);
			if(!empty($req['tanggal']))
				$prq->where(\DB::raw('DATE(`created_at`)'), $req['tanggal']);
		}
		
		return $prq;

	}
}
