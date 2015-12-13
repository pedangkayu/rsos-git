@extends('Master.Template')

@section('title')
Logistik
@endsection

@section('content')
<div class="col-md-12">
	<div class="grid simple">
		<div class="grid-title">
			<h4>Data Personal <b>{{ $karyawan->nm_depan }}</b></h4>
			<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
		</div>
		<div class="grid-body">
			<div class="scroller" data-height="auto">
				<div class="row">
					<div class="col-sm-3">
					@if($karyawan->foto == null)
						<img src="http://placehold.it/200x200" />
					@else
						<img src="{{ asset('/img/avatars/md/' . $karyawan->foto) }}" />
					@endif
					</div>
					<div class="col-sm-8">
						<h1>{{ $karyawan->nm_depan }} {{ $karyawan->nm_belakang }}</h1>
						<p> <font size="4"> 
						@if(count($jabatan) > 0)
						{{ $jabatan->nm_jabatan }}
						@else
						<i>Anda belum memasukan <b>Jabatan</b></i>
						@endif
						</font> 
					</p>
					<hr/>
					<h3 class="text-success">Informasi</h3>
					<p>
						{{ $karyawan->alamat }}<br>
						{{ $karyawan->telp }} / {{ $karyawan->hp }} 
					</p>
					<p>
						{{ $karyawan->email }}	
					</p>
					<p>	</p>
				</div>
			</div>
			<div class="row">

				<div class="col-sm-12">
					<h2>Keluarga</h2>
					<table class="table table-bordered no-more-tables">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Hubungan</th>
								<th>Jenis Kelamin</th>
								<th>Tempat Lahir</th>
								<th>Pendidikan</th>
								<th>Pekerjaan</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1; ?>
							@if(count($keluarga) > 0)
							@foreach($keluarga as $datas)
							<tr>
								<td>{{ $no }}</td>
								<td>{{ $datas->nm_depan }}</td>
								<td>{{ $datas->hubungan }}</td>
								<td>@if($datas->sex === 1) Laki-Laki @else Perempuan @endif</td>
								<td>{{ $datas->tempat_lahir }}</td>
								<td>{{ $datas->pendidikan }}</td>
								<td>{{ $datas->pekerjaan }}</td>
							</tr>
							<?php $no++; ?>
							@endforeach
							@else
							<tr>
								<td colspan="7"><i> Tidak Ada Data Keluarga </i></td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>

			<div class="row">

				<div class="col-sm-12">
					<h2>Status Karyawan</h2>
					<table class="table table-bordered no-more-tables">
						<thead>
							<tr>
								<th>No</th>
								<th>Tanggal</th>
								<th>Nomor SK</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1;  ?>
							@if(count($status_karyawan) > 0)
							@foreach($status_karyawan as $datas)
							<tr>
								<td>{{ $no }}</td>
								<td>{{ Format::indoDate(date('y-m-d',strtotime($datas->created_at))) }}</td>
								<td>{{ $datas->surat_keputusan }}</td>
								<td>{{ $datas->nm_status }}</td>
								<?php $no++; ?>
								@endforeach
								@else
								<tr>
									<td colspan="7"><i> Tidak Ada Data Status </i></td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>

				<div class="row">

					<div class="col-sm-12">
						<h2>Catatan</h2>
						<table class="table table-bordered no-more-tables">
							<thead>
								<tr>
									<th>No</th>
									<th>Tanggal</th>
									<th>Catatan</th>
								</tr>
							</thead>
							<tbody>
							@if(count($catatan) > 0)
							<?php $no =1; ?>
							@foreach($catatan as $catatan)
								<tr>
									<td width="10%"><?php echo $no; ?></td>
									<td width="20%">{{ Format::indoDate(date('y-m-d',strtotime($catatan->created_at))) }}</td>
									<td>{{ $catatan->keterangan }}</td>
								</tr>
								<?php $no++; ?>
							@endforeach
							@else
								<tr>
									<td colspan="7"><i> Tidak Ada Data Catatan </i></td>
								</tr>
							@endif
							</tbody>
						</table>
					</div>
				</div>

				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<a href="{{ url('karyawan') }}"><button type="button" class="btn btn-info col-sm-12">Kembali</button></a>
						</div>
						<div class="col-sm-6">
							<a href="{{ url('karyawan/print/'.$karyawan->id_karyawan) }}"><button type="button" class="btn btn-primary col-sm-12">Cetak</button></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection