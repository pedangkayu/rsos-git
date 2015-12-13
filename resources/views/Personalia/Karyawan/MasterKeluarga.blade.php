@extends('Master.Template')

@section('meta')
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('/js/personalia/karyawan.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function () {
    $('#date_start').datepicker();
    $('#date_end').datepicker();
    $('#tgl_lahir').datepicker();
});
</script>
@endsection

@section('title')
Tambah 
@endsection

@section('content')
<div class="col-md-12">
	<ul class="nav nav-tabs">
		<li>
		@if($id == 0)
		<a href="{{ url('/karyawan/add') }}">Data Karyawan</a>
		@else
		<a href="{{ url('/karyawan/update/'.$id) }}">Data Karyawan</a>
		@endif
		</li>
		<li  class="active"><a href="{{ url('/karyawan/keluarga') }}">Data Keluarga</a></li>
		<li>
		@if($id == 0)
		<a href="#">Photo</a>
		@else
		<a href="{{ url('/karyawan/photo/'.$id) }}">Photo</a>
		@endif
		</li>
	</ul>
	<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	<div class="tab-content">
		<div class="tab-pane active" id="data_karyawan">
			<form action="{{ url('karyawan/keluarga') }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="id_karyawan" value="<?php if(count($id) > 0){ echo $id; }else{ echo ""; }  ?>">
				<div class="row column-seperation">
					<div class="col-md-6">

						<div class="form-group">
							<div class="form-label">Nama Depan</div>
							<span class="help">e.g. John</span>
							<div class="control">
								<input type="text" required name="nm_depan" id="nm_depan" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Nama Belakang</div>
							<span class="help">e.g. Doe</span>
							<div class="control">
								<input type="text" name="nm_belakang" id="nm_belakang" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Hubungan</div>
							<span class="help">e.g. Istri</span>
							<div class="control">
								<input type="text" class="form-control" id="hubungan" name="hubungan">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Jenis Kelamin</div>
							<div class="radio">
								<input id="male" type="radio" name="gender" value="male" checked="checked">
								<label for="male">Male</label>
								<input id="female" type="radio" name="gender" value="female">
								<label for="female">Female</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-label">Tempat Lahir</div>
							<span class="help">e.g. Bandung</span>
							<div class="control">
								<input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Tanggal Lahir</div>
							<div class="control">
								<div class="input-append success date col-md-10 col-lg-6 no-padding">
									<input type="text" name="tgl_lahir" id="tgl_lahir" value="{{date('m/d/Y')}}" class="form-control" data-provide="datepicker" value="">
									<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
								</div>
							</div>
						</div>
						<br><br>
						<div class="form-group">
							<div class="form-label">Pendidikan</div>

							<div class="control">
								<input type="text" class="form-control" name="pendidikan" id="pendidikan">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Pekerjaan</div>

							<div class="control">
								<input type="text" class="form-control" name="pekerjaan" id="pekerjaan">
							</div>
						</div>
						<div class="form-group">
							<div class="control">
								<button class="btn btn-primary" type="submit"> Simpan</button>
								<button class="btn btn-default" type="button" id="tambah-keluarga"> Tambah Keluarga</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Table Keluarga Karyawan -->
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Master  <span class="semi-bold">Keluarga</span></h4>
			<div class="tools">	<a href="javascript:;" class="collapse"></a>
				<a href="#grid-config" data-toggle="modal" class="config"></a>
				<a href="javascript:;" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="grid-body no-border">
			
			<table class="table no-more-tables">
				<thead>
					<tr>
						<th style="width:9%">Nama</th>
						<th style="width:22%">Hubungan</th>
						<th style="width:6%">Pendidikan</th>
						<th style="width:10%">Pekerjaan</th>
					</tr>
				</thead>
				<tbody>
					@if(count($keluarga) > 0)
					@foreach($keluarga as $row)
					<tr>
						<td><a href="{{ url('karyawan/updatekeluarga?id='.$row->id) }}">{{ $row->nm_depan }} {{ $row->nm_belakang }}</a></td>
						<td>{{ $row->hubungan }}</td>
						<td>{{ $row->pendidikan }}</td>
						<td>{{ $row->pekerjaan }}</td>
					</tr>
					@endforeach
					@else
					<tr>
						<td colspan="4"><i>Tidak ada Data Keluarga</i></td>	
					</tr>
					
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection