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

<script type="text/javascript" src="{{ asset('/js/Laporan/subgudang/index.js') }}"></script>
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

		});
</script>
@endsection

@section('title')
Laporan Sub Gudang
@endsection

@section('content')

<div class="row"> <!-- row -->

	<div class="col-md-4"> <!-- col -->

		<div class="tab-content">

			<div class="tab-pane active" id="kso"> <!-- Kartu Stok -->

				<form method="get" action="{{ url('/lapsubgudang/ks') }}">
					<input type="hidden" value="{{ csrf_token() }}" name="_token">

					<div class="row">
						<div class="col-sm-12"> <!-- col -->

							@if(!$me->access && \Auth::user()->permission > 2)
							<div class="form-group gudang">
								<label for="gudang">Gudang</label>
								<select style="width:100%;" name="gudang" id="gudang" onchange="getitem();">
									@foreach($gudangs as $gudang)
									<option value="{{ $gudang->id_gudang }}">{{ $gudang->nm_gudang }}</option>
									@endforeach
								</select>
							</div>
							@else
							<input type="hidden" value="{{ $me->id_gudang }}" name="gudang">
							@endif

							<div class="form-group">
								<div class="radio">
									<input type="radio" name="tipe" value="1" id="tipe-obat" checked="checked">
									<label for="tipe-obat">Obat</label>

									<input type="radio" name="tipe" value="2" id="tipe-barang">
									<label for="tipe-barang">Barang</label>
								</div>
							</div>

							<div class="items">
								<div class="form-group">Memuat...</div>
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

		</div>

	</div> <!-- end col -->

	<div class="col-sm-8"> <!-- cols 1 -->

		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>Grafik <strong>Statistik</strong></h4>
			</div>
			<div class="grid-body no-border">

			<center>Berdasarkan Transaksi Gudang Obat Bulan {{Format::nama_bulan(date('m')) }}</center>

				<div class="tiles white no-margin">
					<br />
					<div id="graphobat" style="height: 250px;">Memuat...</div>
				</div>

				<br />


			</div>
		</div>

	</div> <!-- end col 1 -->

</div> <!-- end row -->

@endsection
