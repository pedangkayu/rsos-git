# **[Laravel 5.1](http://laravel.com/docs/5.0/) - RS Onkologi ** #

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)


Agar proses koding tetap Rapih mohon untuk membeca keterangan di bawah ini

* Pengaturan Framework terdapat pada file rsos/.env
* database framework pada file rsos/rsos_db.sql
* Untuk penambahan Script **CSS** silahkan untuk menambahkan pada file public/css/update.css
* Untuk penambahan Script **Javascript** silahkan untuk menambahkan pada file public/css/update.js
* Untuk pembuatan **Controllers/Events/Helpers/Jobs/Listeners/Views/ Dll** mohon untuk mengelompokannya sesuai Modul yang di buat ke dalam folder
* Untuk penamaan **Model** diusahakan sesuai nama tabel yang ada di **Database**, dan diusahakan model tidak ada yang double²
* Jika tidak diperlukan diharapkan untuk tidak melakukan update terhadap Composer kecuali ada penambahan Helper

**Penggunaan Helper**

Mengabil data user pada tabel Users berdasarkan session Login

```
#!php

Auth::user()
// Out array (field pada tabel users)
```

Mengambil data dari tabel data_karyawan berdasarkan session yang Login

```
#!php

Me::data()
// Out array (filed pada tabel data_karyawan)
```

Mengabil data Level berdasarkan session yang login (*setiap user dimungkinkan untuk meiliki lebih dari 1 levels*)

```
#!php

Me::level()
// Out array id_level_user dari tabel data_level_user
```
mengambil Format tanggal Indonesia

```
#!php

Format::indoDate(date('y-m-d'))
// Out 1 Januari 2015
```

Format Waktu 


```
#!php

Format::time_stamp(date('Y-m-d H:i:s'))
// Out 1 detik yang lalu
```

Mencari nama hari berdasarkan tanggal


```
#!php

Format::hari(date('Y-m-d'))
// Out Senin / Selasa/ dst
```

Mengetahui status online pada user


```
#!php

Format::online($id_user)
// Out Boolean|true/false
```

Merubah format tanggal ke dalam format Jam

```
#!php

Format::jam(date('Y-m-d H:i:s'))
// Out 07:00 AM
```

Penomoran

```
#!php

Format::code(1)
// Out 00001
```

Pemotongan Karakter panjang

```
#!php

Format::substr('Hello World', 7)
// Out Helo W...
```

Fungsi Log Aktivitas.
Simpan pada setiap Event Handler / Jobs Seperti pada saat, membuat data, update data, hapus data

```
#!php

Loguser::create('Masukan text yang menandakan aktivitas user di sini.')

```



### **Tutorial Plugin Javascript** ###

* [**Alert Notifikasi**](http://t4t5.github.io/sweetalert/)


### **Daftar Helper** ###

Kunjungi link di bawah ini untuk melihat cara penggunaan Helper yang terdaftar

* [**Intervention Image**](http://image.intervention.io/)
* [**Save Excel**](http://www.maatwebsite.nl/laravel-excel/docs)
* [**Qr Code**](https://github.com/SimpleSoftwareIO/simple-qrcode)
* [**PDF**](https://github.com/barryvdh/laravel-dompdf)

# **Akses Login** #

**Superadmin**

* Username : admin
* Password : admin

**Admin**

* Username : client
* Password : client


hexters@gmail.com
