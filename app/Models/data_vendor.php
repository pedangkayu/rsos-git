<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_vendor extends Model {

    protected $table 		= 'data_vendor';
	protected $primaryKey 	= 'id_vendor';
	protected $fillable 	= [
		'kode',
		'nm_vendor',
		'pemilik',
		'alamat',
		'telpon',
		'fax',
		'status',
		'rating',
		'id_karyawan',
		'email',
		'website'
	];

	public function scopeListall($query, $req = [], $status = 1){
		$vendor = $query->where('status', $status);
		if(!empty($req)){
			if($req['nm_vendor'])
				$vendor->where('nm_vendor', 'LIKE', '%' . $req['nm_vendor'] . '%');
			if($req['kode'])
				$vendor->where('kode', $req['kode']);
			if($req['tanggal'])
				$vendor->where(\DB::raw('DATE(`created_at`)'), $req['tanggal']);
		}
	}

	public function scopeView($query, $id){
		return $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_vendor.id_karyawan')
			->where('data_vendor.id_vendor', $id)
			->where('data_vendor.status', 1)
			->select('data_vendor.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang')
			->first();
	}

}
