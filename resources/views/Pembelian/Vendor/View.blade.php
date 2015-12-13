@extends('Master.Template')

@section('meta')
	<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/rating/bootstrap-rating.css') }}">
	<script type="text/javascript" src="{{ asset('/plugins/rating/bootstrap-rating.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/modpembelian/vendor/ratings.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$.getJSON(_base_url + '/vendor/ratings', {id : {{ $vendor->id_vendor }} }, function(json){
				$('.rating').html(json.content);

				$('[data-rel="rating"]').rating();
				$('.btn-rating').removeClass('hide');
			});
		});
	</script>
	<style type="text/css">
		.rating-symbol{
			color: orange;
			/*font-size: 12pt;*/
		}
		.rating .rating-symbol{
			font-size: 12pt;	
		}
	</style>
@endsection

@section('title')
	Kode. {{ $vendor->kode }}
@endsection

@section('content')
	<div class="row">
		<div class="col-sm-9">
			
			<ul class="nav nav-tabs" id="tab-01">
	            <li class="active"><a href="{{ url('/vendor/review/' . $vendor->id_vendor) }}">Detail</a></li>
	            <li><a href="{{ url('/vendor/po/' . $vendor->id_vendor) }}">Purcase Order</a></li>
	            <li><a href="{{ url('/vendor/retur/' . $vendor->id_vendor) }}">Retur Barang</a></li>
	        </ul>

	        <div class="tab-content">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<div class="detail">
							<h3>{{ $vendor->nm_vendor }}</h3>

							<table style="width:100%;" cellpadding="5">
								<tr>
									<td class="semi-bold" width="20%">Pemilik</td>
									<td width="80%"> : {{ $vendor->pemilik }}</td>
								</tr>

								<tr>
									<td class="semi-bold" width="20%">Telpon</td>
									<td width="80%"> : {{ $vendor->telpon }}</td>
								</tr>

								<tr>
									<td class="semi-bold" width="20%">Fax</td>
									<td width="80%"> : {{ $vendor->fax }}</td>
								</tr>

								<tr>
									<td class="semi-bold" width="20%">Email</td>
									<td width="80%"> : {{ $vendor->email }}</td>
								</tr>

								<tr>
									<td class="semi-bold" width="20%">Website</td>
									<td width="80%"> : {{ $vendor->website }}</td>
								</tr>

							</table>
							<br />
							<div class="well well-sm"><em>{{ $vendor->alamat }}</em></div>

							<div class="text-right">
								<a href="{{ url('/vendor') }}" class="btn btn-primary">Kembali</a>
							</div>

						</div>

						<div class="ratings hide"></div>
						<div class="ratings-pagin text-right hide"></div>
						
					</div>
				</div>
			</div>

		</div>

		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title no-border"></div>
				<div class="grid-body no-border">
					<address>
						<strong>Dibuat Oleh</strong>
						<p>{{ $vendor->nm_depan }} {{ $vendor->nm_belakang }}</p>
						<strong>Tanggal</strong>
						<p>
							{{ Format::indoDate($vendor->created_at) }}<br />
							<small class="text-muted">{{ Format::hari($vendor->created_at) }}, {{ Format::jam($vendor->created_at) }}</small>
						</p>
					</address>
				</div>
			</div>

			<div class="grid simple">
				<div class="grid-title no-border">
					<h4>Ratings</h4>
				</div>
				<div class="grid-body no-border">
					<div class="rating">
						Memuat...
					</div>
					<br />
					<button class="btn btn-primary btn-block btn-rating hide" data-id="{{ $vendor->id_vendor }}">Detail</button>
				</div>
			</div>

		</div>
	</div>
@endsection
	