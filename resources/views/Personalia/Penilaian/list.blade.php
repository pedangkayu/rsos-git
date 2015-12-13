@extends('Master.Template')

@section('title')
List Penilaian
@endsection

@section('content')
	<div class="row">
		<div class="grid simple">
			<div class="grid-title no-border">
				<h4>#{{ $heads->id }}</h4>
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
						<td>{{ $heads->nm_depan }} {{ $heads->nm_belakang }}</td>
					</tr>
					<tr>
						<th>Departemen</th>
						<td>{{ $heads->nm_departemen }}</td>
					</tr>
					<tr>
						<th>Jabatan</th>
						<td>{{ $heads->nm_jabatan }}</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="grid simple ">
			<div class="grid-title no-border">
				<h4>Penilaian </h4>
				<div class="tools">	<a href="javascript:;" class="collapse"></a>
					<a href="#grid-config" data-toggle="modal" class="config"></a>
					<a href="javascript:;" class="reload"></a>
					<a href="javascript:;" class="remove"></a>
				</div>
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
						@foreach($datas as $data)
						<tr>
							<td>{{ $data->penilaian }}</td>
							<td>{{ $data->uraian }}</td>
							<td>
								@if($data->score == 1)
								Sangat Baik
								@elseif($data->score == 2)
								Baik
								@elseif($data->score == 3)
								Cukup
								@elseif($data->score == 4)
								Kurang
								@endif
							</td>
							<td>
								@if($data->score == 1)
								8
								@elseif($data->score == 2)
								7
								@elseif($data->score == 3)
								6
								@elseif($data->score == 4)
								5
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<div class="row">
					<div class="col-sm-6">
					<a href="{{ url('penilaian') }}"><button type="button" class="btn btn-info col-sm-12">Kembali</button></a>
					</div>
					<div class="col-sm-6">
						<a href="#"><button type="button" class="btn btn-primary col-sm-12">Cetak</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection