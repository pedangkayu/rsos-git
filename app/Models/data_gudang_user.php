<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_gudang_user extends Model {
    
	protected $table 		= 'data_gudang_user';
	protected $primaryKey 	= 'id_gudang_user';
	protected $fillable 	= [
		'id_user',
		'id_gudang'
	];

	public function scopeShow($query, $req = []){
		$item = $query->join('users', 'users.id_user', '=', 'data_gudang_user.id_user')
			->select('data_gudang_user.*', 'users.name');

		if(count($req) > 0){
			if(!empty($req['nama']))
				$item->where('users.name', 'LIKE', '%' . $req['nama'] . '%');
			if(!empty($req['gd']))
				$item->where('data_gudang_user.id_gudang', $req['gd']);
		}
	}

}
