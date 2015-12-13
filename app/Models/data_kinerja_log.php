<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_kinerja_log extends Model
{
    protected $table = 'data_kinerja_log';
	protected $fillable = [
	'id_kinerja',
	'id_penilaian',
	'id_penilaian_uraian',
	'score',
	'point',
	];
}
