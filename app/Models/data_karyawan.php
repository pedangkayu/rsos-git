<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_karyawan extends Model{
    
	protected $table = 'data_karyawan';
	protected $primaryKey = 'id_karyawan';
	protected $fillable = [
		'nm_depan',
		'nm_belakang',
		'telp',
		'email',
		'sex',
		'hp',
		'tempat_lahir',
		'tgl_lahir',
		'jabatan',
		'alamat',
		'agama',
		'pendidikan',
		'id_status',
		'NIK',
		'foto',
		'tgl_bergabung',
		'id_departemen',
	];

	public function scopeDetails($query){
		return $query->join('ref_jabatan','ref_jabatan.id','=','data_karyawan.jabatan')
		->join('ref_status_karyawan','ref_status_karyawan.id','=','data_karyawan.id_status')
		->where('tipe_status', 1);
	}

}
