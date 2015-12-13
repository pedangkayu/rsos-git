@extends('Master.frontend')
@section('meta')
<script type="text/javascript" src="{{ asset('/js/employment/employment.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function () {
    $('#tempat_lahir').datepicker();
    $('#tgl_lahir').datepicker();
});
</script>
@endsection

@section('content')
<form action="{{ url('employment\create') }}" method="post">
	<div class="grid simple">
		<div class="grid-body no-border">
			<h3>Data Employment</h3>
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" required name="id_recruitment" value="{{ $id_recruitment }}">
			<div class="row column-seperation">
				<div class="col-md-6">
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
							<input type="text" name="nm_belakang" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<div class="form-label">Handphone</div>
						<span class="help">e.g. 0822312321</span>
						<div class="control">
							<input type="text" required class="form-control" maxlength="12" name="telp">
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
						<div class="form-label">Pendidikan</div>

						<div class="control">
							<input type="text" required class="form-control" name="pendidikan">
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
							<textarea class="form-control" required name="alamat"></textarea>
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
						<div class="form-label">File</div>
						<span class="help">e.g. Fila harus type .rar</span>
						<input type="file" name="file" class="form-control">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="grid simple">
		<div class="grid-body no-border">
			<h3>Portfolios</h3>
			<p>

			</p>
			<div class="detail-items"></div>

			<div class="text-right">
				<button type="button" class="btn btn-default add-detail"><i class="fa fa-plus"></i> Tambah Portfolio</button>
			</div>

			<div class="form-group">
				<div class="control">
					<button class="btn btn-primary" type="submit"> Simpan</button>	
				</div>
			</div>
		</div>

	</div>
</form>


@endsection
