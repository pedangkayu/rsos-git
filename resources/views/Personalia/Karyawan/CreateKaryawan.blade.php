@extends('Master.Template')

@section('meta')
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function () {
    $('#tgl_bergabung').datepicker();
    $('#tgl_lahir').datepicker();
});
</script>
@endsection

@section('title')
Tambah Karyawan
@endsection

@section('content')
<div class="col-md-12">
	<ul class="nav nav-tabs">
		<li class="active"><a href="{{ url('/karyawan/add') }}">Data Karyawan</a></li>
		<li><a href="#">Data Keluarga</a></li>
	</ul>
	<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
	<div class="tab-content">
		<div class="tab-pane active" id="data_karyawan">
			<form action="{{ url('karyawan/add') }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="NIK" value="">
				<div class="row column-seperation">
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-label">NIK</div>
							<div class="control">
								<input type="text" required name="nik" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Nama Depan</div>
							<span class="help">e.g. John</span>
							<div class="control">
								<input type="text" required name="nm_depan" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Nama Belakang</div>
							<span class="help">e.g. Doe</span>
							<div class="control">
								<input type="text" required name="nm_belakang" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Telephon</div>
							<span class="help">e.g. 0226012321</span>
							<div class="control">
								<input type="text"  class="form-control" maxlength="12" name="telp">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Email</div>
							<span class="help">e.g. jhondoe@example.com</span>
							<div class="control">
								<input type="email" class="form-control" name="email">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Departemen</div>
							<div class="control">
								<select class="form-control" id="source" name="id_departemen">
									<option value="">-Pilih-</option>
									@foreach($departemen as $row)
									<option value="{{ $row->id_departemen }}"> {{$row->nm_departemen}} </option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Jabatan</div>
							<div class="control">
								<select class="form-control" id="source" name="jabatan">
									<option value="">-Pilih-</option>
									@foreach($ref_jabatan as $row)
									<option value="{{ $row->id }}"> {{$row->nm_jabatan}} </option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Tanggal Bergabung</div>
							<div class="control">
								<div class="input-append success date col-md-10 col-lg-6 no-padding">
									<input type="text" value="{{date('m/d/Y')}}" name="tgl_bergabung" id="tgl_bergabung" class="form-control" data-provide="datepicker">
									<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<div class="form-label">Tempat Lahir</div>
							<span class="help">e.g. Bandung</span>
							<div class="control">
								<input type="text" class="form-control" name="tempat_lahir">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Tanggal Lahir</div>
							<div class="control">
								<div class="input-append success date col-md-10 col-lg-6 no-padding">
									<input type="text" name="tgl_lahir" id="tgl_lahir" class="form-control" data-provide="datepicker" value="{{date('m/d/Y')}}">
									<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
								</div>
							</div>
						</div>
						<br><br>
						<div class="form-group">
							<div class="form-label">Jenis Kelamin</div>
							<div class="radio">
								<input id="male" type="radio" name="gender" value="1" checked="checked">
								<label for="male">Laki-Laki</label>
								<input id="female" type="radio" name="gender" value="2">
								<label for="female">Perempuan</label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Alamat</div>

							<div class="control">
								<input type="text" name="alamat" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Agama</div>

							<div class="control">
								<select name="agama" class="form-control">
									<option value="">-Pilih-</option>
									@foreach($ref_agama as $row)
									<option value="{{ $row->id }}"> {{$row->nm_agama}} </option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Pendidikan</div>

							<div class="control">
								<input type="text" class="form-control" name="pendidikan">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">HandPhone</div>

							<div class="control">
								<input type="text" class="form-control" name="hp">
							</div>
						</div>
						<div class="form-group">
							<div class="control">
								<button class="btn btn-primary" type="submit"> Simpan</button>	
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		
	</div>
</div>
@endsection