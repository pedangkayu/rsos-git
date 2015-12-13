<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_level_user extends Model{
    protected $table 		= 'data_level_user';
	protected $primaryKey 	= 'id_level_user';
	protected $fillable 		= [
		'nm_level',
		'status'
	];
}
