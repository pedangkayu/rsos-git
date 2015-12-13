@extends('Master.Template')

@section('title')
Data Recruitment
@endsection

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title">
			<h4>Data Recruitment #{{ $data->id }} <b></b></h4>
			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
		</div>
		<div class="grid-body">
			<div class="scroller" data-height="auto">
				<div class="row">
					<div class="col-sm-8">
						<h3>{{ $data->posisi }}</h3>
						<p>
							<table class="table table-stripe">
								<tr>
									<td>Tanggal Daftar</td>
									<td>:</td>
									<td>{{ Format::indoDate(date('y-m-d',strtotime($data->created_at))) }}</td>
								</tr>
								<tr>
									<td>Nama</td>
									<td>:</td>
									<td>{{ $data->nm_depan }} {{ $data->nm_belakang }}</td>
								</tr>
								<tr>
									<td>Email</td>
									<td>:</td>
									<td>{{ $data->email }}</td>
								</tr>
								<tr>
									<td>Gender</td>
									<td>:</td>
									<td>@if($data->sex == 1) Laki - Laki @else Perempuan @endif</td>
								</tr>
								<tr>
									<td>Hanphone</td>
									<td>:</td>
									<td>{{ $data->mobile }}</td>
								</tr>
								<tr>
									<td>Tempat Lahir</td>
									<td>:</td>
									<td>{{ $data->tempat_lahir }}</td>
								</tr>
								<tr>
									<td>Tanggal Lahir</td>
									<td>:</td>
									<td>{{ Format::indoDate(date('y-m-d',strtotime($data->tgl_lahir))) }}</td>
								</tr>
								<tr>
									<td>Agama</td>
									<td>:</td>
									<td>{{ $data->nm_agama }}</td>
								</tr>
								<tr>
									<td>Pendidikan</td>
									<td>:</td>
									<td>{{ $data->pendidikan }}</td>
								</tr>
							</table>
						</p>
						<table class="table">
							<thead>
								
								<th>Company Name</th>
								<th>Title</th>
								<th>Location</th>
								<th>Periode</th>
							</thead>
							<tbody>
								@if(count($detail) > 0)
								@foreach($detail as $row)
								<td>{{ $row->company_name }}</td>
								<td>{{ $row->title }}</td>
								<td>{{ $row->location }}</td>
								<td>{{ Format::indoDate(date('y-m-d',strtotime($row->date_start))) }} s/d {{ Format::indoDate(date('y-m-d',strtotime($row->date_end))) }}</td>
								@endforeach
								@else
								<tr>
									<td colspan="4"><i>Tidak Memiliki Portfolio</i></td>
								</tr>
								@endif
							</tbody>
						</table>
						<a href="{{ url('employment/')}}"><button class="btn btn-info"> Kembali </button></a>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection