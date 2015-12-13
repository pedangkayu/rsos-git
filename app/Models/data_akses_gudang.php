<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_akses_gudang extends Model {
    
	protected $table 		= 'data_akses_gudang';
	protected $primaryKey 	= 'id_akses_gudang';
	protected $fillable 	= [
		'id_user',
		'tipe'
	];

	public function scopeShow($query, $req = []){
		$users = $query->join('users', 'users.id_user', '=', 'data_akses_gudang.id_user');
			if(count($req) > 0){
				if(!empty($req['nama']))
					$users->where('users.name', 'LIKE', '%' . $req['nama'] . '%');
				if(!empty($req['tipe']))
					$users->where('data_akses_gudang.tipe', $req['tipe']);
			}
			$users->select('users.name', 'data_akses_gudang.*');
	}

}
