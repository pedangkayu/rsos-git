@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/modpembelian/po/master.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('.tgl').datepicker({
				format : 'yyyy-mm-dd'
			});
			$('.btn-tanggal').click(function(){
				$('#tanggal').val('');
			});
			$('.btn-deadline').click(function(){
				$('#deadline').val('');
			});
		});
	</script>
	<style type="text/css">
		td > .link{
			display: none;
		}
		table.daftar-po tr:hover td .link{
			display: block;
		}
	</style>
@endsection

@section('title')
	Pembelian
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>{{ $items->total() }} pembelian <strong>ditemukan</strong></h4>
				</div>
				<div class="grid-body no-border">
					
					<div class="table-responsive">
						<table class="table table-striped daftar-po">
							<thead>
								<tr>
									<th width="7%">No.</th>
									<th width="20%">No PO</th>
									<th width="40%">Penyedia</th>
									<th width="23%">Deadline</th>
									<th width="10%">Status</th>
								</tr>
							</thead>

							<tbody class="content-po">
								<?php $no = 1; ?>
								@forelse($items as $item)
								<?php $css = strtotime($item->deadline) > strtotime(date('Y-m-d')) ? '' : 'text-danger semi-bold'; ?>
								<tr title="Dibuat oleh : {{ $item->nm_depan }} {{ $item->nm_belakang }}" class="itempo-{{ $item->id_po }}">
									<td>{{ $no }}</td>
									<td>
										{{ $item->no_po }}
										<div class="link">
											<small>
												[
													<a href="{{ url('/po/print/' . $item->id_po) }}" target="_balnk">Print</a>
													@if($item->id_sph == 0 && $item->status < 2)
													| <a href="{{ url('/po/edit/' . $item->id_po) }}">Edit</a>
													| <a href="javascript:;" onclick="delpo({{ $item->id_po }});" class="text-danger">Hapus</a>
													@elseif($item->status != 5 && $item->id_sph > 0)
													| <a href="{{ url('/sph/editsphsystem/' . $item->id_sph) }}" target="_balnk">Edit SPH</a>
													@endif
												]
											</small>
										</div>	
									</td>
									<td>
										{{ $item->nm_vendor }}
										<div><small class="text-muted">{{ $item->telpon }}</small></div>
									</td>
									<td>
										<span class="{{ $css }}">{{ Format::indoDate($item->deadline) }}</span>
										<div><small class="text-muted">{{ strtotime($item->deadline) > time() ? Format::selisih_hari($item->deadline, date('Y-m-d')) . ' hari dari sekarang' : '' }}</small></div>
									</td>
									<td>{{ $status[$item->status] }}</td>
								</tr>
								<?php $no++; ?>
								@empty
									<tr>
				    					<td colspan="5">Tidak ditemukan</td>
				    				</tr>
								@endforelse
							</tbody>
						</table>

						<div class="text-right pagin">
							{!! $items->render() !!}
						</div>
					</div>

				</div>
			</div>

		</div>
		<div class="col-sm-3">
			
			<!-- @if(Auth::user()->permission > 1)
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<a href="{{ url('/po/select') }}" class="btn btn-primary btn-block">Buat PO</a>
				</div>
			</div>
			@endif -->

			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					
					<div class="form-group">
						<label for="no_po">No. PO</label>
						<input type="text" id="no_po" name="no_po" class="form-control">
					</div>

					<div class="form-group">
						<label for="vendor">Nama Penyedia</label>
						<input type="text" id="vendor" name="vendor" class="form-control">
					</div>

					<div class="form-group">
						<div class="checkbox check-info">
							<input type="checkbox" name="titipan" id="titipan">
							<label for="titipan">Barang Titipan</label>
						</div>
					</div>

					<div class="form-group">
						<label for="tanggal">Tanggal Buat</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="tanggal" id="tanggal" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-tanggal" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="deadline">Deadline</label>
						<div class="input-group">
					      <input type="text" class="form-control tgl" name="deadline" id="deadline" readonly="readonly">
					      <span class="input-group-btn">
					        <button class="btn btn-default btn-deadline" type="button"><i class="fa fa-trash"></i></button>
					      </span>
					    </div><!-- /input-group -->
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select class="form-control" name="status" id="status">
							<option value="0">Semua</option>
							<option value="1" selected="selected">Baru</option>
							<option value="2">Proses</option>
							<option value="3">Selesai</option>
							<option value="5">Delete System</option>
						</select>
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
						<button class="btn btn-primary btn-block cari"><i class="fa fa-search"></i> Cari</button>
					</div>

				</div>
			</div>

		</div>
	</div>
@endsection