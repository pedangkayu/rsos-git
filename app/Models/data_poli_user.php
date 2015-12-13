<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_poli_user extends Model{

    protected $table 		= 'data_gudang_user';
	protected $primaryKey 	= 'id_gudang_user';
	protected $fillable 	= [
		'id_user',
		'id_gudang'
	];
}
