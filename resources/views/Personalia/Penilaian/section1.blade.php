@extends('Master.Template')
@section('meta')
<link href="{{ asset ('/plugins/bootstrap-select2/select2.css')}}" rel="stylesheet" type="text/css" media="screen"/>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#id_karyawan").select2(); 
	});

</script>

@endsection
@section('content')
<form action="{{ url('penilaian/section2') }}" method="post">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="">
		<ul class="wizard-steps">
			<li class="active" data-target="#step1"> <a href="#tab1" data-toggle="tab"> <span class="step">1</span> <span class="title">Sikap Kerja</span> </a> </li>
			<li data-target="#step2" class=""> <a href="#tab2" data-toggle="tab"> <span class="step">2</span> <span class="title">Prestasi Kerja</span> </a> </li>
			<li data-target="#step3" class=""> <a href="#tab3" data-toggle="tab"> <span class="step">3</span> <span class="title">Manajerial skill</span> </a> </li>
			<li data-target="#step4" class=""> <a href="#tab4" data-toggle="tab"> <span class="step">4</span> <span class="title">Finish <br>
			</span> </a> </li>
		</ul>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<div class="content">
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Data Kinerja</h4>
					<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
				</div>
				<div class="grid-body no-border">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="50%">Item Penilaian</th>
								<th>Kategori</th>
								<th width="12%">Nilai</th>
								<th>Skor</th>
							</tr>
						</thead>

						<tbody>
							<?php 
							$def = '';
							$no = 1;
							?>
							@foreach($head as $key => $row)
							@if($def != $row->penilaian)
							<tr>
								<td colspan="4"><h3>{{ $row->penilaian }}</h3>
								<input type="hidden" name="id_penilaian" value="{{ $row->id_penilaian }}">
								</td>
							</tr>
							<tr>
								<td colspan="4"><i><b>Definisi:</b> {{ $row->definisi }}</i></td>
							</tr>
							@endif
							@foreach($row->uraian as $val)
							<tr>
								<td>{{ $val->uraian }}</td>
								<td>
									@if($val->score == 1)
									Sangat Baik
									@elseif($val->score == 2)
									Baik
									@elseif($val->score == 3)
									Cukup
									@elseif($val->score == 4)
									Kurang
									@endif
								</td>
								<td>
									<input type="hidden" name="data_kuisioner[{{$key}}]['id_penilaian']" value="{{ $row->id_penilaian }}">
									<input type="hidden" name="data_kuisioner[{{$key}}]['id_penilaian_uraian']" value="{{ $val->id }}">
									<input type="radio"  name="data_kuisioner[{{$key}}]['score']" value="{{ $val->score }}">
									<!-- <input type="hidden" name="no[]" value="{{ $row->id_penilaian }}">
									<input type="hidden" name="point[]" value="{{ $row->point }}"> -->
								</td>
								<td>
									{{ $val->score }}
								</td>
							</tr>
							@endforeach
							@endforeach
						</tbody>
					</table>

					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary col-sm-12"> Lanjut </button>	
					</div>
				</div>
			</div>
		</div>
	</div>				
</div>
</div>
</form>
@endsection