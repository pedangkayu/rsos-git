<?php
	
	namespace App\Classes\Karyawan;

	use App\User;
	use App\Models\data_karyawan;
	use App\Models\data_level;
	use App\Models\data_akses_gudang;
	use App\Models\data_gudang_user;

	class Karyawan {
		
		/*Menampilkan data karyawan Me::data()*/
		public function data(){
			return data_karyawan::find(\Auth::user()->id_karyawan);
		}

		public function fullName(){
			$user = data_karyawan::find(\Auth::user()->id_karyawan);
			return $user->nm_depan . ' ' . $user->nm_belakang;
		}

		/*Mengambil data level user Me::level()*/
		public function level(){
			$levels = data_level::whereId_user(\Auth::user()->id_user)
				->select('id_level_user AS level')
				->get();
			$_level = [];
			foreach($levels as $level){
				$_level[] = $level->level;
			}
			return $_level;
		}

		/* Mengupdate Status Online ditambah 15 Menit Me::setOnline() */
		public function setOnline(){
			$time = time() + 900;
			if(\Auth::check())
				User::find(\Auth::user()->id_user)->update([
					'time_online' => $time
				]);
		}
		/*Menampilkan data departement*/
		public function departemen(){
			return data_karyawan::leftJoin('data_departemen', 'data_departemen.id_departemen','=','data_karyawan.id_departemen')
				->where('data_karyawan.id_karyawan', \Auth::user()->id_karyawan)
				->select('data_departemen.nm_departemen')
				->first()
				->nm_departemen;
		}

		public function accessGudang(){
			$user = \Auth::user();
			if($user->permission < 3){
				$gudang = data_akses_gudang::whereId_user($user->id_user);
				if($gudang->count() > 0){
					$res = $gudang->first();
					return [$res->tipe];
				}else{
					return [];
				}

			}else{
				return [1,2];
			}
		}

		public function statusGudang(){
			$user = \Auth::user();
			if($user->permission < 3){
				$gudang = data_akses_gudang::whereId_user($user->id_user);
				if($gudang->count() > 0){
					$res = $gudang->first();
					return $res->tipe; /* Gudang atau Obat */
				}else{
					return 0 /* Tidak memiliki akses */;
				}

			}else{
				return 3 /* Admin */;
			}
		}

		public function subgudang(){
			$gudang = data_gudang_user::join('ref_gudang', 'ref_gudang.id_gudang', '=', 'data_gudang_user.id_gudang')
				->where('id_user', \Auth::user()->id_user)
				->select('data_gudang_user.id_gudang', 'ref_gudang.nm_gudang')
				->first();

			$res = [];
			if($gudang == null){
				$res['access'] = false;
				$res['id_gudang'] = 0;
				$res['nm_gudang'] = '';
			}else{
				$res['access'] = true;
				$res['id_gudang'] = $gudang->id_gudang;
				$res['nm_gudang'] = $gudang->nm_gudang;
			}

			return (object) $res;
		}
	}