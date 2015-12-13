@extends('Master.Template')

@section('meta')
	<script type="text/javascript" src="{{ asset('/js/pengadaan/skb.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('[type="number"]').change(function(){
				var val = $(this).val();
				var max = $(this).data('max');
				var qty = $(this).data('qty');
				if(val > max){
					$(this).val(qty);
				}if(val < 0){
					$(this).val(qty);
				}
			});
		});
	</script>
@endsection

@section('title')
	Proses SPB
@endsection

@section('content')
	
	<form method="post" action="{{ url('/skb/process') }}" id="prosesSPB">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="id_spb" value="{{ $spb->id_spb }}">
		<input type="hidden" name="tipe" value="{{ $spb->tipe }}">

		<div class="row">
			<div class="col-sm-9">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">

						<div class="row">
							<div class="col-sm-6"><br />
								<h1><i>No. {{ $spb->no_spb }}</i></h1>
							</div>
							<div class="col-sm-6 text-right">
								<address>
									<strong>Tanggal</strong>
									<p>
										{{ Format::indoDate($spb->created_at) }}<br />
										<small class="text-muted">{{ Format::hari($spb->created_at) }}, {{ Format::jam($spb->created_at) }}</small>
									</p>
									<strong>Dept.</strong>
									<p>{{ $spb->nm_departemen }}</p>
								</address>
							</div>
						</div>

						<p>
							<textarea name="keterangan" class="form-control">{{ $spb->keterangan }} - {{ '@' . ucwords(Me::data()->nm_depan) . ucwords(Me::data()->nm_belakang) }} : ...</textarea>
							<small>* Anda bisa menambahkan keterangan di sini</small>
						</p>

						<div class="text-right">
							<a class="btn btn-default" href="{{ url('/skb/spb') }}"><i class="fa fa-arrow-circle-left"></i> Batal</a>
							@if(Auth::user()->permission > 1)
							<button class="btn btn-primary btn-prosesSPB" type="button"><i class="fa fa-cog"></i> Proses</button>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<address>
							<strong>Deadline</strong>
							<p>
								{{ Format::indoDate($spb->deadline) }}<br />
								<small class="text-muted">{{ Format::hari($spb->deadline) }}, {{ Format::jam($spb->deadline) }}</small>
							</p>
							<strong>Oleh</strong>
							<p>{{ $spb->nm_depan }} {{ $spb->nm_belakang }}</p>
							<strong>Acc</strong>
							<p>{{ $spb->acc_depan }} {{ $spb->acc_belakang }}</p>
						</address>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>{{ count($items) }} barang <strong>ditemukan</strong></h4>
					</div>
					<div class="grid-body no-border">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="15%">Kode</th>
										<th width="15%">Nama Barang</th>
										<th width="10%" class="text-right">Sisa</th>
										<th width="10%" class="text-right">Req Qty</th>
										<th width="10%" class="text-right">Realisasi</th>
										<th width="20%" class="text-right">Acc Qty</th>
										<th width="30%">Ket.</th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<?php 
											$conver = Format::convertSatuan($item->id_item, $item->id_satuan, $item->id_satuan_barang);
										?>
										<tr>
											<!-- kode -->
											<td>
												{{ $item->kode }}
												<input type="hidden" name="id_items[]" value="{{ $item->id_spb_item }}">
												<input type="hidden" name="id_barang[]" value="{{ $item->id_item }}">
												<input type="hidden" name="id_gudang[]" value="{{ $item->id_gudang }}">
												<input type="hidden" name="kets[]" value="{{ $item->keterangan }}">
												<input type="hidden" name="sisa[]" value="{{ ($item->in - $item->out) }}">

												<input type="hidden" name="id_satuan_barang[]" value="{{ $item->id_satuan_barang }}">
												<input type="hidden" name="id_satuan[]" value="{{ $item->id_satuan }}">

												<input type="hidden" name="qty_lg[]" value="{{ $item->qty_lg }}">
												<input type="hidden" name="qty_req[]" value="{{ $item->qty }}">
											</td>
											<!-- Nama Barang -->
											<td>{{ $item->nm_barang }}</td>
											<!-- Sisa -->
											<td class="text-right">{{ number_format($item->in - $item->out,0,',','.') }} {{ $item->satuan_barang }}</td>
											<!-- Req Qty -->
											<td class="text-right">{{ number_format($item->qty_lg,1,',','.') }} {{ $item->nm_satuan }}</td>
											<!-- Convert -->
											<td class="text-right">
												<a href="javascript:;" title="1 {{ $item->nm_satuan }} = {{ $conver }} {{ $item->satuan_barang }} x {{ $item->qty }} {{ $item->nm_satuan }}">
													{{ number_format($item->qty,0,',','.') }} {{ $item->satuan_barang }}
												</a>
											</td>
											<!-- Acc Qty -->
											<td>
												<div class="input-group transparent">
													<span class="input-group-addon">&nbsp;</span>
												  	<input
														type="number"
														name="qty[]"
														class="form-control text-right"
														required
														value="{{ $item->qty > ($item->in - $item->out) ? ((($item->in - $item->out) - $item->qty) + $item->qty) : $item->qty }}"
														data-max="{{ ($item->in - $item->out) }}"
														data-qty="{{ $item->qty > ($item->in - $item->out) ? ((($item->in - $item->out) - $item->qty) + $item->qty) : $item->qty }}"
													/>
												  	<span class="input-group-addon">{{ $item->satuan_barang }}&nbsp;&nbsp;</span>
												</div>
											</td>
											<!-- Ket. -->
											<td>{{ $item->keterangan }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>

@endsection