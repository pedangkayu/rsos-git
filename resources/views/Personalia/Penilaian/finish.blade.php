@extends('Master.Template')

@section('content')

<div>
	<ul class="wizard-steps">
		<li class="" data-target="#step1"> <a href="#tab1" data-toggle="tab"> <span class="step">1</span> <span class="title">Sikap Kerja</span> </a> </li>
		<li data-target="#step2" class=""> <a href="#tab2" data-toggle="tab"> <span class="step">2</span> <span class="title">Prestasi Kerja</span> </a> </li>
		<li data-target="#step3" class=""> <a href="#tab3" data-toggle="tab"> <span class="step">3</span> <span class="title">Manajerial skill</span> </a> </li>
		<li data-target="#step4" class="active"> <a href="#tab4" data-toggle="tab"> <span class="step">4</span> <span class="title">Finish <br>
		</span> </a> </li>
	</ul>
	<div class="clearfix"></div>
</div>

<div class="row">
	<div class="content">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4><b>Terimakasih Atas Penilaian Kinerja</b></h4>
				<div class="tools">	<a href="javascript:;" class="collapse"></a>
					<a href="#grid-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
			</div>
			<div class="grid-body no-border">
				<table class="table no-more-tables">
					<tr>
						<th>Nama</th>
						<td>{{ $head->nm_depan }} {{ $head->nm_belakang }}</td>
					</tr>
					<tr>
						<th>Departemen</th>
						<td>{{ $head->nm_departemen }}</td>
					</tr>
					<tr>
						<th>Jabatan</th>
						<td>{{ $head->nm_jabatan }}</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Data Kinerja</h4>
						<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
					</div>
					<div class="grid-body no-border">
						<table class="table table-striped table-flip-scroll cf">
							<thead>
								<tr>
									<th width="20%">Penilaian</th>
									<th width="60%">Uraian</th>
									<th>Score</th>
									<th>Point</th>
								</tr>	
							</thead>

							<tbody>
								<?php 
								$def = '';
								$no = 1;
								?>
								@if(count($items) > 0)
								@foreach($items as $item)
								<tr>
									<td>{{ $item->penilaian }}</td>
									<td>{{ $item->uraian }}</td>
									<td>
										@if($item->score == 1)
										Sangat Baik
										@elseif($item->score == 2)
										Baik
										@elseif($item->score == 3)
										Cukup
										@elseif($item->score == 4)
										Kurang
										@endif
									</td>
									<td>
										@if($item->score == 1)
										8
										@elseif($item->score == 2)
										7
										@elseif($item->score == 3)
										6
										@elseif($item->score == 4)
										5
										@endif
									</td>
								</tr>
								@endforeach
								@else
								<tr>
									<td colspan="3"><i>Tidak ada data Penilaian</i></td>
								</tr>
								@endif

							</tbody>
						</table>
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<a href="{{ url('penilaian') }}"><button type="button" class="btn btn-info col-sm-12"> Kembali </button>	</a>	
								</div>
								<div class="col-sm-6">
									<a href="#"><button type="button" class="btn btn-primary col-sm-12"> Cetak </button>	</a>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-7">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Catatan</h4>
						<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
					</div>
					<div class="grid-body no-border">
					<div class="form-group">
						<textarea class="form-control" name="catatan"></textarea>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	


@endsection
