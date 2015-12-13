@extends('Master.Template')

@section('meta')
	<script src="{{ asset('/plugins/raphael/raphael-min.js') }}"></script>
	<link rel="stylesheet" href="{{ asset('/plugins/jquery-morris-chart/css/morris.css') }}" type="text/css" media="screen">
	<script src="{{ asset('/plugins/jquery-morris-chart/js/morris.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/grafik/po/index.js') }}"></script>
	<style type="text/css">
		table.tbl-menus > tbody > tr > td > a{
			font-weight: bold;
			font-size: 14px;
			color: #606060;
			line-height: 70px;
		}
	</style>
@endsection

@section('title')
	Grafiks Purchase Order (PO)
@endsection

@section('content')
	<div class="row">
		<!-- left -->
		<div class="col-sm-7">
			
			<ul class="nav nav-pills">
			  	<li class="active"><a href="{{ url('/grafikpo') }}">Dashboard</a></li>
			  	<li><a href="{{ url('/grafikpo/pembelian') }}">Grafik Pembelian</a></li>
			  	<!-- <li><a href="#">Supplier</a></li> -->
			</ul>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<h4>Grafik Top <span class="vtop">0</span> Supplier</h4>
					<small>Berdasarkan jumlah transaksi terbanyak per Tahun {{  date('Y') }}</small>
					<div id="vendor-chart" style="height: 250px;">Memuat...</div>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<h4>Tabel Top <span class="vtop">0</span> Supplier</h4>
					<small>Berdasarkan jumlah transaksi terbanyak per Tahun {{  date('Y') }}</small>

					<table class="table table-striped" style="margin-top:10px;">
						<thead>
							<tr>
								<th width="20%" class="text-middle">Kode</th>
								<th width="70%" class="text-middle">Supplier</th>
								<th width="10%" class="text-right">Total Transaksi</th>
							</tr>
						</thead>
						<tbody class="tbl-vendors">
							<tr>
								<td colspan="3">Memuat...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-5">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<h3>Obat 5 Teratas</h3>
					<small>Berdasarkan jumlah transaksi per Tahun {{  date('Y') }}</small>
					<div id="obat-chart" style="height: 250px;">Memuat...</div>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<h3>Barang 5 Teratas</h3>
					<small>Berdasarkan jumlah transaksi per Tahun {{  date('Y') }}</small>
					<div id="barang-chart" style="height: 250px;">Memuat...</div>
				</div>
			</div>

		</div>
		
	</div>

@endsection