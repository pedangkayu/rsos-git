<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ratings_vendor extends Model {

    protected $table 		= 'ratings_vendor';
	protected $primaryKey 	= 'id_ratings_vendor';
	protected $fillable 	= [
		'id_vendor',
		'ketepatan',
		'kecepatan',
		'pelayanan',
		'keterangan',
		'id_karyawan'
	];


	public function scopeByvendor($query, $id){
		return $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'ratings_vendor.id_karyawan')
			->where('ratings_vendor.id_vendor', $id)
			->select('ratings_vendor.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}
}
