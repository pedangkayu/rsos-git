<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_departemen extends Model{

	protected $table = 'data_departemen';
	protected $primaryKey = 'id_departemen';
	protected $fillable = [
		'nm_departemen',
		'kd_departemen'
	];
    
}
