@extends('Master.frontend')
@section('meta')
<script type="text/javascript" src="{{ asset('/js/employment/employment.js') }}"></script>
@endsection
@section('content')
<table class="table table-bordered">
	<thead>
		<tr>
			<th>No</th>
			<th>Posisi</th>
			<th>Berlaku</th>
			<th>Estimasi Gaji</th>
			<th>Syarat</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 1; ?>
		@foreach($items as $item)
		<tr>
			<td><?php echo $no; ?></td>
			<td><a href="{{ url('employment/create?id='.base64_encode($item->id)) }}">{{ $item->posisi }}</a></td>
			<td>{{ $item->date_open }} s/d {{ $item->date_close }}</td>
			<td>Rp. {{ number_format($item->estimasi_gaji) }}</td>
			<td><?php 
				$string = $item->syarat;
				if(strlen($string) > 50){
					echo substr($string,0,50).'....';
				}else{
					echo $string;
				}
				?></td>
				<td><button class="btn btn-primary" data-toggle="modal" data-target="#myModal" onclick="detail({{ $item->id }})"> Detail </button></td>
			</tr>
			<?php $no++; ?>
			@endforeach
		</tbody>
	</table>

	<div class="text-right pagins">
		{!! str_replace('/?', '?', $items->render()) !!}
	</div>	
	<!-- Modal -->
	<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<br>
					<i class="icon-credit-card icon-7x"></i>
					<h3 id="myModalLabel" class="semi-bold posisi"></h3>
				</div>
				<div class="modal-body detail">
					<i class="fa fa-circle-o-notch fa-spin"></i> Memuat...
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<span class="link"></span>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	@endsection
