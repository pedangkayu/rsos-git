@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/user/log.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[name="tanggal"]').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('[name="tanggal"]').val('');
			});
		});
	</script>
@endsection

@section('title')
	Logs
@endsection


@section('content')
	
	<div class="row">
		<dov class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} log <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="5%">No.</th>
									<th width="20%">Nama</th>
									<th width="20%">Waktu</th>
									<th width="55%" class="text-center">Aktivitas</th>
								</tr>
							</thead>

							<tbody class="content-logs">
								<?php $no = 1; ?>
								@forelse($items as $item)
									<tr>
										<td>{{ $no }}</td>
										<td>
											{{ $item->nm_depan }} {{ $item->nm_belakang }}
											<div><small class="text-muted">Dept. {{ $item->nm_departemen }}</small></div>
										</td>
										<td>
											{{ Format::indoDate($item->created_at) }}
											<div><small class="text-muted">{{ Format::hari($item->created_at) }}, {{ Format::jam($item->created_at) }}</small></div>
										</td>
										<td>
											<small>{{ $item->keterangan }}</small>
										</td>
									</tr>
									<?php $no++; ?>
								@empty
									<tr>
										<td colspan="4">Tidak ditemukan</td>
									<tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagin">
							{!! $items->render() !!}
						</div>
					</div>
				</div>
			</div>	

		</dov>
		<dov class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label for="karyawan">Nama Karyawan</label>
						<input type="text" id="karyawan" name="karyawan" class="form-control">
					</div>

					<div class="form-group">
						<label for="dept">Departemen</label>
						<select class="form-control" name="dept" id="dept">
							<option value="0">Pilih</option>
							@foreach($depts as $dept)
								<option value="{{ $dept->id_departemen }}">{{ $dept->nm_departemen }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label for="tanggal">Tanggal</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="limit">Limit / Page</label>
						<select class="form-control" name="limit" id="limit">
							<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>

					<div class="form-group">
						<button type="button" class="btn btn-primary btn-block cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>	

		</dov>
	</div>

@endsection