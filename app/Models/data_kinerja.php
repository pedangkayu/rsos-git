<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_kinerja extends Model
{
	protected $table = 'data_kinerja';
	protected $fillable = [
	'id_karyawan',
	'id_penilai',
	'session'
	];

	public function log(){
		return $this->hasMany('App\Models\data_kinerja_log','id_kinerja');
	}
}
