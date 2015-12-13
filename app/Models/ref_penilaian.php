<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_penilaian extends Model
{
	protected $table = 'ref_penilaian';
	protected $primaryKey = 'id_penilaian';
	protected $fillable = [
	'section',
	'penilaian',
	'definisi'
	];

	public function uraian(){
		return $this->hasMany('App\Models\ref_penilaian_uraian','id_penilaian');
	}
}
