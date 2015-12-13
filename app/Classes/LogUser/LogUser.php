<?php
	
	namespace App\Classes\LogUser;

	use App\Models\data_aktivitas;

	class LogUser{

		public function create($text){
			return data_aktivitas::create([
				'id_karyawan' => \Me::data()->id_karyawan,
				'keterangan' => $text
			]);
		}

	}