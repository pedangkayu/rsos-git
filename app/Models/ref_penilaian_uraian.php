<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_penilaian_uraian extends Model
{
	protected $table = 'ref_penilaian_uraian';
	protected $fillable = [
	'id_penilaian',
	'uraian',
	'point'
	];
}
