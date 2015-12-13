<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_karyawan_klrg extends Model
{
    protected $table = 'data_karyawan_klrg';
	protected $fillable = [
		'id_karyawan',
		'nm_depan',
		'nm_belakang',
		'telp',
		'hubungan',
		'sex',
		'tempat_lahir',
		'tgl_lahir',
		'pekerjaan',
		'pendidikan',
	];
}
