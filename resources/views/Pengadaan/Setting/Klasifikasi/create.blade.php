@extends('Master.Template')

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Data Klasifikasi</h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a> 
				<a href="javascript:;" class="reload"></a>
			</div>
		</div>
		<div class="grid-body no-border">
			<div class="row">
			<div class="col-sm-7">
					<form action="{{ url('klasifikasi/create') }}" method="post">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="form-group">
							<label>Kode</label>
							<input type="text" name="kode" class="form-control" >
						</div>
						<div class="form-group">
							<label>Nama Klasifikasi</label>
							<input type="text" name="nama" class="form-control" >
						</div>
						<div class="form-group">
							<button class="btn btn-primary" type="submit">Simpan</button>
							<a href="{{ url('klasifikasi') }}"><button class="btn btn-danger" type="button">Kembali</button></a>
						</div>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>
@endsection