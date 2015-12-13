@extends('Master.Template')

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Data Satuan</h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a> 
				<a href="javascript:;" class="reload"></a>
			</div>
		</div>
		<div class="grid-body no-border">
			<div class="row">
			<div class="col-sm-7">
					<form action="{{ url('satuan/update') }}" method="post">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" value="{{ $data->id_satuan }}">
						<div class="form-group">
							<label>Nama Satuan</label>
							<input type="text" name="nama" required class="form-control" value="{{ $data->nm_satuan }}" >
						</div>
						<div class="form-group">
							<label>Satuan</label>
							<input type="text" name="satuan" class="form-control" required value="{{ $data->satuan }}" >
						</div>
						<div class="form-group">
							<button class="btn btn-primary" type="submit">Simpan</button>
							<a href="{{ url('satuan') }}"><button class="btn btn-danger" type="button">Kembali</button></a>
						</div>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>
@endsection