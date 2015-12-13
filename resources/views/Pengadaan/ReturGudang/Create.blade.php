@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			$('[type="number"]').change(function(){
				var val = $(this).val();
				var max = $(this).data('max');
				if(val < 1)
					$(this).val(1);
				else if(val > max)
					$(this).val(max);
			});
		});
	</script>	
@endsection

@section('title')
	Retur Permohonan Barang
@endsection

@section('content')
	
	<form method="post" action="">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="row">
			<!-- left -->
			<div class="col-sm-9">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>{{ count($items) }} ditemukan</h4>
					</div>
					<div class="grid-body no-border">
						
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="40%">Nama</th>
										<th width="30%" class="text-right text-middle">Req Qty</th>
										<th width="30%" class="text-right text-middle">Qty</th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr>
											<td>
												<a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="{{ $item->nm_barang }}">{{ Format::substr($item->nm_barang,25) }}</a><br />
												<small class="text-muted">{{ $item->kode }}</small>
												<input type="hidden" name="id_satuan[]" value="{{ $item->id_satuan }}">
												<input type="hidden" name="id_barang[]" value="{{ $item->id_barang }}">
											</td>
											<td class="text-right">{{ $item->qty }} {{ $item->nm_satuan }}</td>
											<td><input type="number" class="text-right form-control" name="qty[]" value="{{ $item->qty }}" data-max="{{ $item->qty }}"></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

					</div>
				</div>

			</div>

			<!-- right -->
			<div class="col-sm-3">
				
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						
						<address>
							<strong>No SKB</strong>
							<p>{{ $skb->no_skb }}</p>
							<strong>Tanggal Buat</strong>
							<p>
								{{ Format::indoDate($skb->created_at) }}<br />
								<small class="text-muted">{{ Format::hari($skb->created_at) }}, {{ Format::jam($skb->created_at) }}</small>
							</p>
						</address>

						<input type="hidden" name="tipe" value="{{ $skb->tipe }}">
						<input type="hidden" name="id_gudang" value="{{  $me->id_gudang }}">

					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						@if(!$me->access && $skb->tipe == 1)
						<p>Maaf Anda tidak memiliki akses untuk melakukan pengenbalian barang, <a class="btn btn-mini btn-white" href="{{ url('/subgudang/access') }}">Atur akses user</a></p>
						<br />

						<input type="hidden" name="access" value="false">
						@else
						<div class="form-group">
							<button type="submit" class="btn btn-block btn-primary">Proses</button>
							<input type="hidden" name="access" value="true">
						</div>
						@endif
						<a href="{{ url('/returgudang/skb') }}" class="btn btn-primary btn-block">Batal</a>
					</div>
				</div>

			</div>
			
		</div>
	</form>

@endsection