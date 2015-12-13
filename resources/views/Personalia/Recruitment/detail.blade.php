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
									<td>Tanggal Mulai</td>
									<td>:</td>
									<td>{{ Format::indoDate(date('y-m-d',strtotime($data->date_open))) }}</td>
								</tr>
								<tr>
									<td>Tanggal Berakhir</td>
									<td>:</td>
									<td>{{ Format::indoDate(date('y-m-d',strtotime($data->date_close))) }}</td>
								</tr>
								<tr>
									<td>Syarat</td>
									<td>:</td>
									<td>{{ $data->syarat }}</td>
								</tr>
								<tr>
									<td>Estimasi Gaji</td>
									<td>:</td>
									<td>{{ $data->estimasi_gaji }}</td>
								</tr>
								<tr>
									<td>Job Desc</td>
									<td>:</td>
									<td><?php echo htmlspecialchars_decode(stripslashes($data->jobdesk)); ?></td>
								</tr>
								<tr>
									<td>Catatan</td>
									<td>:</td>
									<td>{{ $data->catatan }}</td>
								</tr>
							</table>
						</p>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection