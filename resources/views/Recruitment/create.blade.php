@extends('Master.Template')

@section('meta')
<link href="{{ asset('/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css') }}" rel="stylesheet" type="text/css"/>
<script src="{{ asset('/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }} " type="text/javascript"></script>
<script src="{{ asset('/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		$('[name="job_desk"]').wysihtml5();
	});
</script>
@endsection

@section('title')
Create Recruitment
@endsection

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title no-border">
			<h4>Data Recruitment</h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a> 
				<a href="javascript:;" class="reload"></a>
			</div>
		</div>
		<div class="grid-body no-border">
			<form action="{{ url('recruitment\create') }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="row column-seperation">
					<div class="col-md-8 col-sm-8 col-xs-8">
						<div class="form-group">
							<div class="form-label">Posisi</div>
							<div class="control">
								<input type="text" required name="posisi" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Tanggal Mulai</div>
							<div class="control">
								<div class="input-append success date col-md-10 col-lg-6 no-padding">
									<input type="text" name="date_open" class="form-control" data-provide="datepicker" value="{{date('m/d/Y')}}">
									<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
								</div>
							</div>
						</div>
						<br><br>
						<div class="form-group">
							<div class="form-label">Tanggal Berakhir</div>
							<div class="control">
								<div class="input-append success date col-md-10 col-lg-6 no-padding">
									<input type="text" name="date_close" class="form-control" data-provide="datepicker" value="{{date('m/d/Y')}}">
									<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
								</div>
							</div>
						</div>
						<br><br>
						<div class="form-group">
							<div class="form-label">Estimasi Gaji</div>
							<div class="control">
								<input type="text" class="form-control" name="estimasi_gaji">
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Syarat</div>
							<div class="control">
								<textarea class="form-control" name="syarat"></textarea>	 
								
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Job Desk</div>
							<div class="control">
								<!-- <input type="text" class="form-control" name="job_desk"> -->
								<textarea id="some-textarea" name="job_desk" style="width:100%; height:300px;"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="form-label">Catatan</div>
							<div class="control">
								<textarea name="catatan" class="form-control"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="control">
								<button type="submit" class="btn btn-primary">Simpan</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection