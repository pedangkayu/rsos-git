@extends('Master.Template')

@section('csstop')
<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('plugins/jquery-sparkline/jquery-sparkline.js') }}"></script>

<script src="{{ asset('/plugins/jquery-flot/jquery.flot.js') }}"></script>
<script src="{{ asset('/plugins/jquery-flot/jquery.flot.time.min.js') }}"></script>
<script src="{{ asset('/plugins/jquery-flot/jquery.flot.selection.min.js') }}"></script>
<script src="{{ asset('/plugins/jquery-flot/jquery.flot.animator.min.js') }}"></script>
<script src="{{ asset('/plugins/jquery-flot/jquery.flot.orderBars.js') }}"></script>
<script src="{{ asset('/plugins/raphael/raphael-min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/plugins/jquery-morris-chart/css/morris.css') }}" type="text/css" media="screen">
<script src="{{ asset('/plugins/jquery-morris-chart/js/morris.min.js') }}"></script>


<script type="text/javascript" src="{{ asset('/js/Laporan/Logistik/index.js') }}"></script>
<script type="text/javascript">
	$(function(){
		$('#tab-01 a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

			// date pic
			var checkin = $('#dpd1').datepicker({
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
				if (ev.date.valueOf() > checkout.date.valueOf()) {
					var newDate = new Date(ev.date)
					newDate.setDate(newDate.getDate() + 1);
					checkout.setValue(newDate);
				}
				checkin.hide();
				$('#dpd2')[0].focus();
			}).data('datepicker');

			var checkout = $('#dpd2').datepicker({
				onRender: function(date) {
					return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
				},
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
				checkout.hide();
			}).data('datepicker');

			// date pic /////////////////////////////////////////
			var checkinb = $('#dpd3').datepicker({
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
				if (ev.date.valueOf() > checkoutb.date.valueOf()) {
					var newDate = new Date(ev.date)
					newDate.setDate(newDate.getDate() + 1);
					checkoutb.setValue(newDate);
				}
				checkinb.hide();
				$('#dpd4')[0].focus();
			}).data('datepicker');

			var checkoutb = $('#dpd4').datepicker({
				onRender: function(date) {
					return date.valueOf() <= checkinb.date.valueOf() ? 'disabled' : '';
				},
				format : 'yyyy-mm-dd'
			}).on('changeDate', function(ev) {
				checkoutb.hide();
			}).data('datepicker');

		});
</script>
@endsection

@section('title')
Laporan Logistik
@endsection

@section('content')

<div class="row"> <!-- row -->

	<div class="col-md-4"> <!-- col -->

		<ul class="nav nav-tabs" id="tab-01">
			<li class="active"><a href="#kso">Kartu Stok Obat</a></li> <!-- Kartu Stok Obat -->
			<li><a href="#ksb">Kartu Stok Barang</a></li> <!-- Kartu Stok Barang -->
		</ul>

		<div class="tab-content">

			<div class="tab-pane active" id="kso"> <!-- Kartu Stok -->

				<form method="get" action="{{ url('/reportlogistik/ks') }}">
					<input type="hidden" value="{{ csrf_token() }}" name="_token">

					<div class="row">
						<div class="col-sm-12"> <!-- col -->
							<h4><span class="summary-total">0</span> Transaksi bulan ini.</h4>

	            				<!-- <div class="form-group">
	            					<label>Cari berdasarkan</label>
		            				<div class="radio">
		            					<input type="radio" name="tipe" class="tipe-src" value="1" id="rbarang" checked>
		            					<label for="rbarang">Barang</label>

		            					<input type="radio" name="tipe" class="tipe-src" value="2" id="rkategori">
		            					<label for="rkategori">Kategori</label>

		            					<input type="radio" name="tipe" class="tipe-src" value="3" id="rklasifikasi">
		            					<label for="rklasifikasi">Klasifikasi</label>
		            				</div>
	            				</div>
	            			-->

	            			<div class="form-group barang">
	            				<label for="id_barang">Obat</label>
	            				<select class="select item-obat" style="width:100%;" name="barang" id="id_barang">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group kategori hide">
	            				<label for="id_kategori">Kategori</label>
	            				<select class="select" style="width:100%;" name="kategori" id="id_kategori">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group klasifikasi hide">
	            				<label for="id_klasifikasi">Klasifikasi</label>
	            				<select class="select" style="width:100%;" name="klasifikasi" id="id_klasifikasi">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group">
	            				<label>Waktu</label>
	            				<div class="radio">
	            					<input type="radio" name="waktu" class="waktu-src" value="1" id="bln" checked>
	            					<label for="bln">Per Bulan</label>

	            					<input type="radio" name="waktu" class="waktu-src" value="2" id="tgl">
	            					<label for="tgl">Per Tanggal</label>

	            				</div>
	            			</div>

	            			<div class="perbulan">
	            				<div class="form-group">
	            					<label for="bulan">Bulan</label>
	            					<select class="select" style="width:100%;" name="bulan" id="bulan">
	            						<option value="1" {{ date('m') == 1 ? 'selected' : '' }}>Januari</option>
	            						<option value="2" {{ date('m') == 2 ? 'selected' : '' }}>Februari</option>
	            						<option value="3" {{ date('m') == 3 ? 'selected' : '' }}>Maret</option>
	            						<option value="4" {{ date('m') == 4 ? 'selected' : '' }}>April</option>
	            						<option value="5" {{ date('m') == 5 ? 'selected' : '' }}>Mei</option>
	            						<option value="6" {{ date('m') == 6 ? 'selected' : '' }}>Juni</option>
	            						<option value="7" {{ date('m') == 7 ? 'selected' : '' }}>Juli</option>
	            						<option value="8" {{ date('m') == 8 ? 'selected' : '' }}>Agustus</option>
	            						<option value="9" {{ date('m') == 9 ? 'selected' : '' }}>September</option>
	            						<option value="10" {{ date('m') == 10 ? 'selected' : '' }}>Oktober</option>
	            						<option value="11" {{ date('m') == 11 ? 'selected' : '' }}>Nopenber</option>
	            						<option value="12" {{ date('m') == 12 ? 'selected' : '' }}>Desember</option>
	            					</select>
	            				</div>	 


	            				<div class="form-group">
	            					<label for="tahun">Tahun</label>
	            					<select class="select text-right" style="width:100%;" name="tahun" id="tahun">
	            						@for($i = 2000; $i <= date('Y'); $i++)
	            						<option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
	            						@endfor
	            					</select>
	            				</div>
	            			</div>

	            			<div class="pertanggal hide">
	            				<div class="form-group">
	            					<label>Tentukan tanggal</label>
	            					<div class="input-group">
	            						<input type="text" name="dari" class="form-control" readonly="readonly" value="{{ date('Y-m-d') }}" id="dpd1">
	            						<span class="input-group-addon">s/d</span>
	            						<input type="text" name="sampai" class="form-control" readonly="readonly" value="{{ date('Y-m-d', strtotime('+30 day', time())) }}" id="dpd2">
	            					</div>
	            					<small class="text-muted">Hari tidak boleh lebih dari 30 hari</small>
	            				</div>
	            			</div>

	            			<div class="form-group">
	            				<button class="btn btn-primary btn-block" type="submit">Proses</button>
	            			</div>

	            		</div> <!-- end col -->


	            	</div>

	            </form>
	        </div>

	        <div class="tab-pane" id="ksb"> <!-- Kartu Stok -->

	        	<form method="get" action="{{ url('/reportlogistik/ks') }}">
	        		<input type="hidden" value="{{ csrf_token() }}" name="_token">

	        		<div class="row">
	        			<div class="col-sm-12"> <!-- col -->
	        				<h4><span class="summary-total">0</span> Transaksi bulan ini.</h4>

	            				<!-- <div class="form-group">
	            					<label>Cari berdasarkan</label>
		            				<div class="radio">
		            					<input type="radio" name="tipe" class="tipe-src" value="1" id="rbarang" checked>
		            					<label for="rbarang">Barang</label>

		            					<input type="radio" name="tipe" class="tipe-src" value="2" id="rkategori">
		            					<label for="rkategori">Kategori</label>

		            					<input type="radio" name="tipe" class="tipe-src" value="3" id="rklasifikasi">
		            					<label for="rklasifikasi">Klasifikasi</label>
		            				</div>
	            				</div>
	            			-->

	            			<div class="form-group barang">
	            				<label for="id_barang">Barang</label>
	            				<select class="select item-barang" style="width:100%;" name="barang" id="id_barang">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group kategori hide">
	            				<label for="id_kategori">Kategori</label>
	            				<select class="select" style="width:100%;" name="kategori" id="id_kategori">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group klasifikasi hide">
	            				<label for="id_klasifikasi">Klasifikasi</label>
	            				<select class="select" style="width:100%;" name="klasifikasi" id="id_klasifikasi">
	            					<option value="">Loading...</option>
	            				</select>
	            			</div>

	            			<div class="form-group">
	            				<label>Waktu</label>
	            				<div class="radio">
	            					<input type="radio" name="waktu" class="waktu-srcb" value="1" id="blnb" checked>
	            					<label for="blnb">Per Bulan</label>

	            					<input type="radio" name="waktu" class="waktu-srcb" value="2" id="tglb">
	            					<label for="tglb">Per Tanggal</label>

	            				</div>
	            			</div>

	            			<div class="perbulanb">
	            				<div class="form-group">
	            					<label for="bulan">Bulan</label>
	            					<select class="select" style="width:100%;" name="bulan" id="bulan">
	            						@for($i=1;$i<13;$i++)
	            						<option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ Format::nama_bulan($i) }}</option>
	            						@endfor
	            					</select>
	            				</div>	 


	            				<div class="form-group">
	            					<label for="tahun">Tahun</label>
	            					<select class="select text-right" style="width:100%;" name="tahun" id="tahun">
	            						@for($i = 2000; $i <= date('Y'); $i++)
	            						<option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
	            						@endfor
	            					</select>
	            				</div>
	            			</div>

	            			<div class="pertanggalb hide">
	            				<div class="form-group">
	            					<label>Tentukan tanggal</label>
	            					<div class="input-group">
	            						<input type="text" name="dari" class="form-control" readonly="readonly" value="{{ date('Y-m-d') }}" id="dpd3">
	            						<span class="input-group-addon">s/d</span>
	            						<input type="text" name="sampai" class="form-control" readonly="readonly" value="{{ date('Y-m-d', strtotime('+30 day', time())) }}" id="dpd4">
	            					</div>
	            					<small class="text-muted">Hari tidak boleh lebih dari 30 hari</small>
	            				</div>
	            			</div>

	            			<div class="form-group">
	            				<button class="btn btn-primary btn-block" type="submit">Proses</button>
	            			</div>

	            		</div> <!-- end col -->


	            	</div>

	            </form>
	        </div>

	    </div>
	    
	    <div class="list-group">
	    	<a href="{{ url('/reportlogistik/laporanstok') }}" class="list-group-item"> Laporan Stok</a>
	    </div>

	    <div class="list-group">
	    	<a href="{{ url('/reportlogistik/laporanbelanjabarang') }}" class="list-group-item"> Rekap Belanja Barang dan Obat</a>
	    	<!-- <a href="{{ url('/reportlogistik/rekapbelanja') }}" class="list-group-item"> Rekap Belanja</a> -->
	    	<!-- <a href="{{ url('/reportlogistik/rekapatk') }}" class="list-group-item"> Rekap Belanja ATK</a> -->
	    	<a href="{{ url('/reportlogistik/rekapdistributor') }}" class="list-group-item"> Rekap Belanja Distributor</a>
	    	<a href="{{ url('/reportlogistik/rekapprodusen') }}" class="list-group-item"> Rekap Belanja Produsen</a>
	    </div>

	    <div class="list-group">
	    	<a href="{{ url('/reportlogistik/lpb') }}" class="list-group-item"> Laporan perpindahan barang</a>
	    </div>


	</div> <!-- end col -->

	<div class="col-md-8"> <!-- cols 1 -->

		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>Grafik <strong>Statistik</strong></h4>
			</div>
			<div class="grid-body no-border">

				<center>Berdasarkan transaksi pembelian obat bulan {{Format::nama_bulan(date('m')) }}</center>

				<div class="tiles white no-margin">
					<br />
					<div id="graphobat" style="height: 250px;">Memuat...</div>
				</div>

				<br />


			</div>
		</div>

		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>Grafik <strong>Statistik</strong></h4>
			</div>
			<div class="grid-body no-border">

				<center>Berdasarkan transaksi pembelian barang bulan {{Format::nama_bulan(date('m')) }}</center>

				<div class="tiles white no-margin">
					<br />
					<div id="graphbarang" style="height: 250px;">Memuat...</div>
				</div>

				<br />


			</div>
		</div>

	</div> <!-- end col 1 -->

</div> <!-- end row -->

@endsection
