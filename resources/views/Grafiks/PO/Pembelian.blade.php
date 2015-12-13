@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/plugins/Highcharts-4.1.9/js/highcharts.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/grafik/po/pembelian.js') }}"></script>
@endsection

@section('title')
	Grafik Pembelian
@endsection

@section('content')
	
	<div class="row">
		<div class="col-lg-3 col-md-4">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label>Tentukan Tahun</label>
						<select name="tahun" class="form-control">
							@for($i=2005;$i <= date('Y');$i++)
								<option value="{{ $i }}" {{ $i == date('Y') ? 'selected="selected"' : ''}}>{{ $i }}</option>
							@endfor
						</select>
					</div>

					<div class="form-group">
						<label>Tipe Grafik</label>
						<div class="radio radio-primay">
							<input type="radio" name="tipe" value="column" id="bar" checked="checked">
							<label for="bar">Grafik Batang</label>

							<input type="radio" name="tipe" value="area" id="area">
							<label for="area">Grafik Area</label>
						</div>
					</div>

					<div class="form-group">
						<button class="btn btn-primary btn-block cari" data-loading-text="loading...">Cari</button>
					</div>

				</div>
			</div>
			
		</div>

		<div class="col-lg-9 col-md-8">
			<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto">Memuat...</div>
			<hr />
			<div id="grafikitem" style="min-width: 310px; height: 400px; margin: 0 auto">Memuat...</div>
		</div>
	</div>

@endsection