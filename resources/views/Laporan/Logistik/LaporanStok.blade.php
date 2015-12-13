@extends('Master.Template')

@section('meta')
<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/multiselect/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/multiselect/dataTables.tableTools.css') }}">
<script type="text/javascript" src="{{ asset('/plugins/multiselect/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/multiselect/dataTables.tableTools.js') }}"></script>
<script type="text/javascript">
	$(function(){
		$('#tab-01 a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		$('#selecallklasifikasi').click(function(){
			var status = $(this).prop('checked');
			$(':input[data-tipe="klasifikasi"]').prop('checked', status);
		});

		$('#selectkategori').click(function(){
			var status = $(this).prop('checked');
			$(':input[data-tipe="kategori"]').prop('checked', status);
		});

	});
</script>
<style type="text/css">
	.list-label{
		list-style-type: none;
		margin: 0;
		padding: 0;
	}
</style>
@endsection

@section('title')
	Pencarian Laporan Stok
@endsection

@section('content')


	<div class="row">
		<!-- left -->
		<div class="col-sm-12">

			<ul class="nav nav-tabs" id="tab-01">
				<li class="active"><a href="#tab1hellowWorld">Klasifikasi</a></li>
				<li><a href="#tab1FollowUs">Kategori</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="tab1hellowWorld">
					<form action="{{ url('/reportlogistik/laporanstokview') }}" method="get">
						<input type="hidden" name="tipe" value="klasifikasi">
						<div class="row">
							<div class="col-sm-9">
								<table id="klasifikasi" class="table">
									<thead>
										<tr>
											<th>
												<div class="checkbox check-primary">
													<input type="checkbox" name="selecallklasifikasi" id="selecallklasifikasi">
													<label for="selecallklasifikasi"></label>
												</div>
											</th>
											<th width="10%">No.</th>
											<th width="15">Kode</th>
											<th width="75%">Klasifikasi</th>
										</tr>
									</thead>
									
									<tbody>
										<?php $no = 1; ?>
										@foreach($klasifikasis as $klasifikasi)
										<tr>
											<td>
												<div class="checkbox check-primary">
													<input data-tipe="klasifikasi" id="select{{ $klasifikasi->id_klasifikasi }}" type="checkbox" name="id_klasifikasi[]" value="{{ $klasifikasi->id_klasifikasi }}">
													<label for="select{{ $klasifikasi->id_klasifikasi }}"></label>
												</div>
											</td>
											<td>{{ $no }}</td>
											<td>{{ $klasifikasi->kode }}</td>
											<td>
												{{ $klasifikasi->nm_klasifikasi }}
											</td>
										</tr>
										<?php $no++; ?>
										@endforeach
									</tbody>
								</table>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<strong>Tipe Item</strong>
									<select name="jenis" class="form-control">
										<option value="">Semua</option>
										<option value="1">Obat</option>
										<option value="2">Barang</option>
									</select>
								</div>

								<strong>Label Kolom</strong>
								<ul class="list-label">
									<li>
										<div class="checkbox check-primary">
											<input id="kode" type="checkbox" name="kode" value="kode" checked="checked" disabled="disabled">
											<label for="kode">Kode</label>
										</div>
									</li>
									<li>
										<div class="checkbox check-primary">
											<input id="nama" type="checkbox" name="nama" value="nama" checked="checked" disabled="disabled">
											<label for="nama">Nama</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="kat" type="checkbox" name="kat" value="kat">
											<label for="kat">Kategori</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="jenis_barang" type="checkbox" name="jenis_barang" value="jenis_barang">
											<label for="jenis_barang">Jenis Barang</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="klasi" type="checkbox" name="klasifikasi" value="klasifikasi">
											<label for="klasi">Klasifikasi</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="stok" type="checkbox" name="stok" value="stok" checked="checked" disabled="disabled">
											<label for="stok">Sisa Stok</label>
										</div>
									</li>
									<li>
										<div class="checkbox check-primary">
											<input id="satuan" type="checkbox" checked="checked" name="satuan" value="satuan">
											<label for="satuan">Satuan</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="harga_satuan" type="checkbox" name="harga_satuan" value="harga_satuan" checked="checked" disabled="disabled">
											<label for="harga_satuan">Harga Satuan</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="total" type="checkbox" name="total" value="total" checked="checked" disabled="disabled">
											<label for="total">Total</label>
										</div>
									</li>
								</ul>

								<div class="form-group">
									<button class="btn btn-primary" type="submit">Proses Laporan</button>
								</div>
							</div>
						</div>

						
					</form>
				</div>

				<div class="tab-pane" id="tab1FollowUs">
					<form action="{{ url('/reportlogistik/laporanstokview') }}" method="get">
						<input type="hidden" name="tipe" value="kategori">
						<div class="row">
							<div class="col-sm-9">
								<table id="kategori" class="table">
									<thead>
										<tr>
											<th>
												<div class="checkbox check-primary">
													<input type="checkbox" name="selectkategori" id="selectkategori">
													<label for="selectkategori"></label>
												</div>
											</th>
											<th width="10%">No.</th>
											<th width="15">Kode</th>
											<th width="75%">Kategori</th>
										</tr>
									</thead>
									
									<tbody>
										<?php $no = 1; ?>
										@foreach($kategoris as $kategori)
										<tr>
											<td>
												<div class="checkbox check-primary">
													<input data-tipe="kategori" id="selectkat{{ $kategori->id_kategori }}" type="checkbox" name="id_kategori[]" value="{{ $kategori->id_kategori }}">
													<label for="selectkat{{ $kategori->id_kategori }}"></label>
												</div>
											</td>
											<td>{{ $no }}</td>
											<td>{{ $kategori->alias }}</td>
											<td>
												{{ $kategori->nm_kategori }}
											</td>
										</tr>
										<?php $no++; ?>
										@endforeach
									</tbody>
								</table>
							</div>
							<div class="col-sm-3">

								<div class="form-group">
									<strong>Tipe Item</strong>
									<select name="jenis" class="form-control">
										<option value="">Semua</option>
										<option value="1">Obat</option>
										<option value="2">Barang</option>
									</select>
								</div>

								<strong>Label Kolom</strong>
								<ul class="list-label">
									<li>
										<div class="checkbox check-primary">
											<input id="kode" type="checkbox" name="kode" value="kode" checked="checked" disabled="disabled">
											<label for="kode">Kode</label>
										</div>
									</li>
									<li>
										<div class="checkbox check-primary">
											<input id="nama" type="checkbox" name="nama" value="nama" checked="checked" disabled="disabled">
											<label for="nama">Nama</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="kat2" type="checkbox" name="kat" value="kat">
											<label for="kat2">Kategori</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="jenis_barang2" type="checkbox" name="jenis_barang" value="jenis_barang">
											<label for="jenis_barang2">Jenis Barang</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="klasi2" type="checkbox" name="klasifikasi" value="klasifikasi">
											<label for="klasi2">Klasifikasi</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="stok" type="checkbox" name="stok" value="stok" checked="checked" disabled="disabled">
											<label for="stok">Sisa Stok</label>
										</div>
									</li>
									<li>
										<div class="checkbox check-primary">
											<input id="satuan2" type="checkbox" name="satuan" checked="checked" value="satuan">
											<label for="satuan2">Satuan</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="harga_satuan" type="checkbox" name="harga_satuan" value="harga_satuan" checked="checked" disabled="disabled">
											<label for="harga_satuan">Harga Satuan</label>
										</div>
									</li>

									<li>
										<div class="checkbox check-primary">
											<input id="total" type="checkbox" name="total" value="total" checked="checked" disabled="disabled">
											<label for="total">Total</label>
										</div>
									</li>
								</ul>

								<div class="form-group">
									<button class="btn btn-primary" type="submit">Proses Laporan</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

		</div>

	</div>

@endsection