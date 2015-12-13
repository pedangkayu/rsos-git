<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_aktivitas extends Model {

    protected $table = 'data_aktivitas';
	protected $primaryKey = 'id_aktivitas';
	protected $fillable = [
		'id_karyawan',
		'keterangan'
	];

	public function scopeShow($query, $req = []){
		$log = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_aktivitas.id_karyawan')
			->join('data_departemen', 'data_departemen.id_departemen', '=', 'data_karyawan.id_departemen');

		if(count($req) > 0){
			if(!empty($req['karyawan']))
				$log->where('data_karyawan.nm_depan', 'LIKE', '%' . $req['karyawan'] . '%')
					->orWhere('data_karyawan.nm_belakang', 'LIKE', '%' . $req['karyawan'] . '%');
			if(!empty($req['tanggal']))
				$log->where(\DB::raw('DATE(data_aktivitas.created_at)'), $req['tanggal']);
			if(!empty($req['dept']))
				$log->where('data_karyawan.id_departemen', $req['dept']);
		}

		$log->orderby('data_aktivitas.id_aktivitas', 'desc')
			->select('data_karyawan.nm_depan', 'data_karyawan.nm_belakang', 'data_departemen.nm_departemen', 'data_aktivitas.*');
	}
}
