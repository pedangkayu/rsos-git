@extends('Master.Template')

@section('meta')
<link href="{{ asset ('/plugins/bootstrap-select2/select2.css')}}" rel="stylesheet" type="text/css" media="screen"/>
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#satuan_max").select2(); 
		$("#satuan_min").select2(); 
	});

</script>

@endsection

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Data Konversi Satuan</h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a> 
				<a href="javascript:;" class="reload"></a>
			</div>
		</div>
		<div class="grid-body no-border">
			<div class="row">
			<div class="col-sm-7">
					<form action="{{ url('konversi/update') }}" method="post">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" value="{{ $data->id }}">
						<div class="form-group">
							<label>Satuan Maksimal</label>
							<select id="satuan_max" class="col-sm-12" name="satuan_max" required>
								<option value="">-Pilih-</option>
								@foreach($satuan as $datas)
								<option value="{{ $datas->id_satuan }}" {{ $data->id_satuan_max == $datas->id_satuan ? 'selected' : '' }}> {{ $datas->nm_satuan }} </option>
								@endforeach
							</select>
						</div>
						<br>
						<div class="form-group">
							<label>Satuan Minimal</label>
							<select id="satuan_min" class="col-sm-12" name="satuan_min" required>
								<option value="">-Pilih-</option>
								@foreach($satuan as $datas)
								<option value="{{ $datas->id_satuan }}" {{ $data->id_satuan_min == $datas->id_satuan ? 'selected' : '' }}> {{ $datas->nm_satuan }} </option>
								@endforeach
							</select>
						</div>
						<br>
						<div class="form-group">
							<label>Qty</label>
							<input name="qty" class="form-control" type="text" required value="{{ $data->qty }}" />
						</div>
						<div class="form-group">
							<button class="btn btn-primary" type="submit">Update</button>
							<a href="{{ url('konversi') }}"><button class="btn btn-danger" type="button">Kembali</button></a>
						</div>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>
@endsection