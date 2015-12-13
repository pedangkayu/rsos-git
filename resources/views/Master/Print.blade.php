<!DOCTYPE html>
<html>
<head>
	<title>Document Rs Onkologi</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/Print/print.css') }}">
	<script type="text/javascript" src="{{ asset('/js/print.js') }}"></script>
	<style>
		.page-break {
		    page-break-after: always;
		}
	</style>
	@yield('meta')
</head>
<body>
	<header>
		<img src="{{ asset('/img/onkologi.png') }}">
		<div class="alamat">
			Araya Galaxy Bumi Permai Blok A-2 No.7 <br />(Jl. Arif Rahman Hakim) Surabaya 60111<br />
			Telp. +62-31-5914855 Fax. +62-31-5914860<br />
			www.rsonkologi.com
		</div>
	</header>
	<br />
	<section class="container">
		@yield('content')
	</section>
	<footer class="text-center btn-print">
		<hr />
		<button type="button" onclick="window.print();">Print Dokumen</button>
		<button type="button" onclick="window.close();">Keluar</button>
	</footer>
</body>
</html>