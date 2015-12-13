<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_recruitment extends Model
{
    protected $table = 'data_recruitment';
	protected $fillable = [
		'posisi',
		'date_open',
		'date_close',
		'syarat',
		'jobdesk',
		'gambar',
		'estimasi_gaji',
		'catatan',
		'pageview',
		'status',
	];

}
