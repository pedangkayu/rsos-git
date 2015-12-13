@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection
@section('meta')
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#id_karyawan").select2(); 
	});

</script>

@endsection
@section('title')
Status Aktif
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="tabbable tabs-left">
		<ul class="nav nav-tabs">
				<li class="active"><a href="#">Status Aktif</a></li>
				<li><a href="{{ url('status_karyawan/tidakaktif') }}">Status Tidak Aktif</a></li>
				<li><a href="{{ url('status_karyawan/kehadiran') }}">Kehadiran</a></li>
				<li><a href="{{ url('status_karyawan/cuti') }}">Cuti</a></li>
				<li><a href="{{ url('status_karyawan/keterlambatan') }}">Keterlambatan Pegawai</a></li>
				<li><a href="{{ url('status_karyawan/meninggalkan') }}">Meninggalkan Pekerjaan</a></li>
				<li><a href="{{ url('status_karyawan/skk') }}">Dokumen SKK</a></li>
				<li><a href="{{ url('status_karyawan/pegawaitidakaktif') }}">Pegawai Tidak Aktif</a></li>
			</ul>
			<div class="tab-content">

				<div class="tab-pane active" id="status_aktif">
					<div class="row">
						<div class="grid simple">
							<div class="grid-title no-border">
								<h4>Update Status Karyawan yang Aktif</h4>
								<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
							</div>
							<div class="grid-body no-border"> <br>
								<div class="row">
									<form action="{{ url('status_karyawan/statusaktif') }}" method="post">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<div class="col-md-8 col-sm-8 col-xs-8">
											<div class="form-group">
												<label class="form-label">Nama Pegawai</label>

												<div class="controls">
													<select class="col-sm-12" name="id_karyawan" id="id_karyawan">
														<option value="">-Pilih-</option>
														@foreach($data as $datas)
														<option value="{{ $datas->id_karyawan }}" {{ $id == $datas->id_karyawan ? 'selected' : '' }}> {{ $datas->nm_depan }} {{ $datas->nm_belakang }} </option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="form-label">Nomer SK</label>
												<div class="control">
													<input type="text" name="surat_keputusan" class="form-control" >
												</div>
											</div>
											<div class="form-group">
												<div class="form-label">Tanggal</div>
												<div class="control">
													<div class="input-append success date col-md-10 col-lg-6 no-padding">
														<input type="text" name="datetime_in" class="form-control" data-provide="datepicker" value="{{ date('m/d/Y') }}">
														<span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
													</div>
												</div>
											</div>
											<br><br>
											<div class="form-group">
												<label class="form-label">Status</label>

												<div class="controls">
													<select class="form-input" name="id_status">
														<option value="">-Pilih-</option>
														@foreach($status_aktif as $data)
														<option value="{{ $data->id }}">{{ $data->nm_status }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="form-label">Keterangan</label>
												<textarea class="form-control" name="keterangan"></textarea>
											</div>
											<div class="form-group">
												<button class="btn btn-primary">Simpan</button>
											</div>
										</div>
									</form>
								</div>
								<div class="col-sm-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>No</th>
												<th>Nama</th>
												<th>Nomer SK</th>
												<th>Keterangan</th>
											</tr>
										</thead>
										<tbody>
										<?php $no=1; ?>
											@foreach($aktif as $data)
											<tr>
												<td>{{ $no }}</td>
												<td>{{ $data->nm_depan }} {{ $data->nm_belakang }}</td>
												<td> {{ $data->surat_keputusan }} </td>
												<td> {{ $data->keterangan }} </td>
											</tr>
											<?php $no++;  ?>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection