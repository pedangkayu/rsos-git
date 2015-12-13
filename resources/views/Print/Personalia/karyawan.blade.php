@extends('Master.Print')
@section('meta')
	<style type="text/css">
		.paspoto{
			float: right;
			margin-bottom: 10px;
		}
	</style>
@endsection
@section('content')
<div>
<img src="{{ asset('/img/avatars/md/' . $karyawan->foto) }}" class="paspoto" />
	<table>
		<tr>
			<td>Nama</td>
			<td>: {{ $karyawan->nm_depan }} {{ $karyawan->nm_belakang }}</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>: {{ $jabatan->nm_jabatan }}</td>
		</tr>
		<tr>
			<td>Telp / HP</td>
			<td>: {{ $karyawan->telp }} / {{ $karyawan->hp }} </td>
		</tr>
		<tr>
			<td>Email</td>
			<td>: {{ $karyawan->email }}</td>
		</tr>
		<tr>
			<td>Alamat</td>
			<td>: {{ $karyawan->alamat }}</td>
		</tr>
	</table>
</div>
<div>
	<h3>Keluarga</h3>
	<table class="table table-bordered" cellspacing="0">
		<tr>
			<th>No</th>
			<th>Nama</th>
			<th>Hubungan</th>
			<th>Jenis Kelamin</th>
			<th>Tempat Lahir</th>
			<th>Pendidikan</th>
			<th>Pekerjaan</th>
		</tr>
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
	</table>
</div>
<div>
	<h2>Status Karyawan</h2>
	<table class="table table-bordered" cellspacing="0">
		<tr>
			<th>No</th>
			<th>Tanggal</th>
			<th>Nomor SK</th>
			<th>Status</th>
		</tr>
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
		</table>
	</div>
	<div>
		<h2>Catatan</h2>
		<table class="table table-bordered" cellspacing="0">
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
	@endsection