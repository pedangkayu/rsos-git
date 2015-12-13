@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/modpembelian/spbm/create.js') }}"></script>
@endsection

@section('title')
	Good Receive
@endsection

@section('content')
	
	<form method="post" action="{{ url('/gr/creategr') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id_po" value="{{ $po->id_po }}">
		<div class="row">
			<div class="col-sm-8">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<div class="label label-important pull-right">{{ $status[$po->status] }}</div>
						<h4>Purchase Order No. <b>{{ $po->no_po }}</b></h4>
						<h4>Supplier : <b>{{ $po->nm_vendor }}</b></h4>
					</div>

					<div class="grid-body no-border">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="40%">Nama Barang</th>
										<th width="20%" class="text-right">Qty</th>
										<th width="40%" class="text-right">Realisasi</th>
									</tr>
								</thead>

								<tbody>
									@foreach($items as $item)
										@if($item->status > 2)
											<?php $qty = ( Format::convertSatuan($item->id_item, $item->id_satuan, $item->id_satuan_default) * $item->req_qty ); ?>
											<tr>
												<td title="{{ $item->nm_barang }}">
													{{ $item->nm_barang }}
													<div class="text-muted"><small>{{ $item->kode }}</small></div>
												</td>
												<td class="text-right">
													{{ number_format($item->req_qty,0,',','.') }} {{ $item->nm_satuan }}
													<div class="text-muted">
														<small>{{ number_format( $qty ,0,',','.') }} {{ $item->satuan_default }}</small>
													</div>
												</td>
												<td class="text-right">
													<i class="fa fa-check"></i>
												</td>
											</tr>
										@else
											<?php $qty = ( Format::convertSatuan($item->id_item, $item->id_satuan, $item->id_satuan_default) * $item->qty ); ?>
											<tr>
												<td title="{{ $item->nm_barang }}">
													{{ $item->nm_barang }}
													<div class="text-muted"><small>{{ $item->kode }}</small></div>
													<br />
													
													<input type="hidden" name="bonus[]" value="0">
													<input type="hidden" name="id_po_item[]" value="{{ $item->id_po_item }}">
													<input type="hidden" name="id_barang[]" value="{{ $item->id_item }}">
													<input type="hidden" name="barang_sesuai[]" value="1">
													<input type="hidden" name="req_qty[]" value="{{ $item->qty }}">

													<input type="hidden" name="id_satuan[]" value="{{ $item->id_satuan }}">
													<input type="hidden" name="id_satuan_default[]" value="{{ $item->id_satuan_default }}">
													<input type="hidden" name="status[]" value="1">

													<div class="row">
														<div class="col-sm-9">
															@if($item->tipe_barang == 1)
																<div class="form-group">
																	<label>* Tanggal Kadaluarsa</label>
																	<input type="text" data-exp="exp" name="tgl_exp[]" class="form-control" readonly="readonly" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
																</div>
															@else
																<input type="hidden" name="tgl_exp[]" value="">
															@endif
														</div>

														<div class="col-sm-3">
															@if($po->titipan == 0)
															<div class="form-group">
																<label>&nbsp;</label>
																<button class="btn btn-white btn-{{ $item->id_po_item }}" type="button" data-toggle="tooltip" data-placement="bottom" onclick="addbonus({{ $item->id_po_item }});" title="Jadikan Bonus" data-loading-text="<i class='fa fa-cog fa-spin'></i>"><i class="fa fa-plus"></i> bonus</button>
															</div>
															@endif
														</div>
													</div>
													
												</td>
												<td class="text-right">
													{{ number_format($item->qty,0,',','.') }} {{ $item->nm_satuan }}
													<div class="text-muted">
														<small>{{ number_format( $qty ,0,',','.') }} {{ $item->satuan_default }}</small>
													</div>
												</td>
												<td>
													<div class="form-group">
														<div class="input-group transparent">
														 	<span class="input-group-addon"></span>
														  	<input type="number"  name="qty_lg[]" value="{{ $item->qty }}" data-max="{{ $item->qty }}" class="text-right form-control" required />
														  	<span class="input-group-addon">
																<small>{{ $item->nm_satuan }}</small> &nbsp;&nbsp;
														  	</span>
														</div>
													</div>
													<div class="form-group">
														<input type="text" name="merek[]" class="form-control" value="{{ $item->nm_barang }}" placeholder="* Merk.." required>
														<input type="text" name="kets[]" class="form-control" placeholder="Keterangan..">
													</div>
												</td>
											</tr>
										@endif
									@endforeach
								</tbody>

							</table>
						</div>

					</div>
				</div>

				@if($po->titipan == 0)
				<!-- BONUS -->
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Stok <strong>lebih / Bonus</strong></h4>
					</div>

					<div class="grid-body no-border">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="30%">Nama Barang</th>
										<th width="30%">Tgl. Kadaluarsa</th>
										<th width="30%" class="text-right">Qty</th>
										<th width="10%"></th>
									</tr>
								</thead>

								<tbody class="bonus">
									<tr class="no-bonus">
										<td colspan="4">Tidak ditemukan</td>
									</tr>
								</tbody>
							 </table>
						</div>
					</div>
				</div>
				<!-- END BONUS -->
				@endif
			</div>

			<div class="col-sm-4">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						{!! $po->titipan > 0 ? '<h4>Barang <strong>Titipan</strong></h4>' : '' !!}
					</div>

					<div class="grid-body no-border">
						
						@if($po->titipan > 0)
						<input type="hidden" name="titipan" value="{{ $po->titipan }}">
						@else
						<input type="hidden" name="titipan" value="0">
						@endif

						<div class="form-group">
							<label for="sj">No. Invoice</label>
							<input type="text" name="sj" id="sj" class="form-control">
							<small class="text-muted">Nomor Invoice dari Supplier {{ $po->nm_vendor }}</small>
						</div>

						<div class="form-group">
							<label for="nm_pengirim">Nama Pengirim *</label>
							<input type="text" name="nm_pengirim" id="nm_pengirim" class="form-control" required>
						</div>

						<div class="form-group">
							<label for="tgl_terima">Tanggal Terima * </label>
						    <input type="text" class="form-control tgl" name="tgl_terima" id="tgl_terima" value="{{ date('Y-m-d') }}" readonly="readonly">
						</div>

						<div class="form-group">
							<label for="tgl_periksa">Tanggal Periksa *</label>
							<input type="text" class="form-control tgl" name="tgl_periksa" value="{{ date('Y-m-d') }}" id="tgl_periksa" readonly="readonly">
						</div>

						<div class="form-group">
							<label for="pengiriman">Jenis Pengiriman *</label>
							<select name="pengiriman" id="pengiriman" class="form-control">
								<option value="1">Oleh Supplier</option>
								<option value="2">Oleh Ekspedisi</option>
								<option value="2">Oleh Onkologi</option>
							</select>
						</div>

						<div class="form-group">
							<label for="pemeriksa1">Pemeriksa *</label>
							<input type="text" name="pemeriksa1" id="pemeriksa1" class="form-control" required>
						</div>

						<div class="form-group">
							<label for="pemeriksa2">Pengawas</label>
							<input type="text" name="pemeriksa2" id="pemeriksa2" class="form-control">
							<small class="text-muted">Pengawas bisa Security atau Orang ke 3</small>
						</div>

						<div class="form-group">
							<label for="keterangan">Keterangan</label>
							<textarea class="form-control" name="keterangan" id="keterangan" rows="4"></textarea>
						</div>

						<div class="form-group text-danger">
							Tanda (*) wajib diisi!
						</div>

						<div class="form-group">
							<label for="agree"> 
								<input type="checkbox" name="agree" id="agree" required> <small>Saya yakin untuk memproses <i>Good Receive</i> ini</small>
							</label>
						</div>

						<div class="form-group">
							@if(Auth::user()->permission > 1)
							<button type="submit" class="btn btn-block btn-primary">Proses</button>
							@endif
							<a href="{{ url('/gr/po') }}" class="btn btn-block btn-primary btn-kembali">Kembali</a>
						</div>

					</div>
				</div>

			</div>
		</div>
	</form>

@endsection