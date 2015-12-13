@extends('Master.Template')

@section('meta')
<link href="{{ asset ('/plugins/bootstrap-select2/select2.css')}}" rel="stylesheet" type="text/css" media="screen"/>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('/js/penilaian/penilaian.js') }}"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#id_karyawan").select2(); 
	});

</script>

@endsection

@section('title')
Admin Penilaian Kerja
@endsection

@section('content')
<div class="grid simple">
	<div class="grid-title no-border">
		<h4>Data Karyawan</h4>
		<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	</div>
	<form  action="{{ url('penilaian/detail') }}" method="get" onchange="chains(this)">
		<div class="grid-body no-border">
			<div class="row">
				<div class="col-sm-7">

					<div class="form-group">
						<label class="form-label">Nama Pegawai</label>
						<div class="controls">
							<select class="col-sm-12" name="id_karyawan" id="id_karyawan">
								<option value="">-Pilih-</option>
								@foreach($data as $datas)
								<option value="{{ $datas->id_karyawan }}"> {{ $datas->nm_depan }} {{ $datas->nm_belakang }} </option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label">Departement</label>
						<div class="controls">
							<div class="controls">
							<select class="col-sm-12" name="departemen" id="departemen" class="form-control">
								<option value="">-Pilih-</option>
								@foreach($departemens as $departemen)
								<option value="{{ $departemen->id_departemen }}"> {{ $departemen->nm_departemen }}</option>
								@endforeach
							</select>
						</div>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label">Jabatan</label>
						<div class="controls">
							<select class="col-sm-12" name="jabatan" id="jabatan" class="form-control">
								<option value="">-Pilih-</option>
								@foreach($jabatans as $jabatan)
								<option value="{{ $jabatan->id }}"> {{ $jabatan->nm_jabatan }}</option>
								@endforeach
							</select>
						</div>
					</div>

				</div>
			</div>
			<button type="submit" class="btn btn-primary">Proses</button>
		</div>
	</form>
</div>
@endsection