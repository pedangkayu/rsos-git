<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_sph_grup extends Model {

    protected $table 		= 'data_sph_grup';
	protected $primaryKey 	= 'id_sph_grup';
	protected $fillable 	= [
		'no_sph',
		'id_pembuat',
		'status'
	];

	public function scopeShow($query, $req = [], $status = 1){
		$sph = $query->join('data_karyawan', 'data_karyawan.id_karyawan', '=', 'data_sph_grup.id_pembuat');
			
			if(is_array($status)){
				$sph->whereIn('data_sph_grup.status', $status);
			} else {
				$sph->where('data_sph_grup.status', $status);
			}

			if(count($req) > 0){
				if(!empty($req['no_sph']))
					$sph->where('data_sph_grup.no_sph', $req['no_sph']);
				if(!empty($req['tanggal']))
					$sph->where(\DB::raw('DATE(`data_sph_grup`.`created_at`)'), $req['tanggal']);
			}

			$sph->orderby('data_sph_grup.status', 'asc')
				->orderby('data_sph_grup.id_sph_grup', 'asc')
				->select('data_sph_grup.*', 'data_karyawan.nm_depan', 'data_karyawan.nm_belakang');
	}
}
