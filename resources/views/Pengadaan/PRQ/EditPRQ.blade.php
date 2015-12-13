@extends('Master.Template')

@section('csstop')
	<link href="{{ asset('/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
@endsection

@section('meta')
	<script src="{{ asset('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('/js/pengadaan/prq.js') }}"></script>
	<script type="text/javascript">
		$(function(){
			$('#hlp').tooltip({
				container: 'body',
				placement : 'left',
				trigger : 'focus',
				title : 'Semua Barang yang bertipe Obat akan di mutasikan ke gudang yang terdaftar pada list gudang di bawah ini.'
			});

			$('[name="deadline"]').datepicker({
					format: "yyyy-mm-dd",
					autoclose: true,
					todayHighlight: true
		   });

			$('[type="number"]').change(function(){
				var n = $(this).val();
				if(n < 0)
					$(this).val(0);
			});

			$.getJSON(_base_url + '/pmbumum/satuans', <?php echo $param; ?>, function(json){
				for(var i=0; i < json.ids.length; i++){
					if(json.result[json.ids[i]] == true){
						$('.satuan-item' + json.ids[i] ).html(json.content[json.ids[i]]);
					}else{
						$('.satuan-item' + json.ids[i] ).html('<div class="text-center" title="Satuan belum di seting silahkan hubungi Logistik"><i class="fa fa-times text-danger"></i> no set</div>');
						$('.input-' + json.ids[i]).attr('disabled', 'disabled').removeAttr('required').removeAttr('name');
					}
				}

			});

			$.getJSON(_base_url + '/sph/vendors', { idselect : {{ $prq->titipan }} }, function(json){
				$('[name="vendor"]').html(json.content);
				$('[name="vendor"]').select2();
			});

			$('[name="titipan"]').click(function(){
				var status = $(this).prop('checked');
				if(status == true)
					$('.suplier-titipan').show();
				else
					$('.suplier-titipan').hide();
			});

		});
	</script>

	<style type="text/css">
		.suplier-titipan{
			display: none;
		}
	</style>
@endsection

@section('title')
	No. {{ $prq->no_prq }}
@endsection

@section('content')
	<form method="post" action="{{ url('/prq/edit') }}" id="prosesPRQ">
		<input type="hidden" value="{{ csrf_token() }}" name="_token">
		<input type="hidden" value="{{ $prq->id_prq }}" name="id_prq">

		<div class="row">
			<div class="col-sm-9">
				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						<p>
							<a class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-plus"></i> Tambah</a>
							<h4>{{ count($items) }} Barang  <strong>terpilih</strong></h4>
						</p>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="30%">Nama</th>
										<th width="15%" class="text-right">Sisa</th>
										<th width="15%" class="text-right">REQ Qty</th>
										<th width="16%">Satuan</th>
										<th width="24%">Keterangan</th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr class="item-{{ $item->id_barang }}">
											<td>
												<a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="{{ $item->nm_barang }}">{{ Format::substr($item->nm_barang,15) }}</a><br />
												<small class="text-muted">
													{{ $item->kode }}
													<input type="hidden" value="{{ $item->id_barang }}" name="id_barang[]">
												</small>
											</td>
											<td class="text-right">{{ number_format(($item->in - $item->out), 0,',','.') }} {{ $item->nm_satuan }}</td>
											<td>
												<input type="number" name="qty[]" class="form-control text-right" value="{{ $item->qty }}" required>
											</td>
											<td class="satuan-item{{ $item->id_barang }}">
												Memuat...
											</td>
											<td>
												<input type="text" maxlength="50" name="kets[]" class="form-control" value="{{ $item->keterangan }}">
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<small class="text-muted">* Mengisi Qty degnan angka 0 sama dengan membatalkan item barang tsb!</small>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-3">
				
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>&nbsp;</h4>
						<div class="tools">
			          		<a href="javascript:;" class="collapse"></a> 
			          	</div>
					</div>
					<div class="grid-body no-border">
						<address>
							<strong>Oleh</strong>
							<p>{{ $prq->nm_depan }} {{ $prq->nm_belakang }}</p>
							<strong>Tanggal</strong>
							<p>{{ Format::indoDate($prq->created_at) }}</p>
							<strong>Disetujui Oleh</strong>
							<p>{{ $prq->acc_depan }} {{ $prq->acc_belakang }}</p>
						</address>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">

						<div class="form-group">
							<div class="checkbox check-info">
								<input type="checkbox" name="titipan" id="titipan" {{ $prq->titipan > 0 ? 'checked' : '' }}>
								<label for="titipan">Barang Titipan</label>
							</div>
						</div>	

						<div class="form-group suplier-titipan" {!! $prq->titipan > 0 ? 'style="display:block;"' : '' !!}>
							<label for="penyedia">Penyedia</label>
							<select style="width:100%;" name="vendor" id="penyedia" required>
								<option value="">Loading...</option>
							</select>
							<small>Pilih Supplier</small>
						</div>

						<div class="form-group">
							<label for="deadline">Deadline</label>
			                <input type="text" id="deadline" class="form-control" name="deadline" value="{{ $prq->target }}" readonly="readonly">
						</div>

						<div class="form-group">
							<textarea class="form-control" name="ket" placeholder="Tambah Keterangan..." rows="4">{{ $prq->keterangan }}</textarea>
						</div>
					</div>
				</div>

				<div class="grid simple">
					<div class="grid-title no-border"></div>
					<div class="grid-body no-border">
						@if(Auth::user()->permission > 1)
						<button type="button" class="btn btn-primary btn-block btn-createprq">Simpan Perubahan</button>
						@endif
						<a href="{{ url('/prq') }}" class="btn btn-block btn-primary">Batal</a>
					</div>
				</div>

			</div>

		</div>
	</form>

@endsection

@section('footer')
	
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="myModalLabel">Menambahkan Items</h4>
	      </div>
	      <div class="modal-body">
	        
	      	<div class="grid simple">
	      		<div class="grid-title no-border"></div>
	      		<div class="grid-body no-border">
	      			<div class="row">
	      				<div class="col-sm-6">
	      					<p><img src="{{ asset('/img/addtots.png') }}" class="img-responsive img-thumbnail"></p>
	      				</div>
	      				<div class="col-sm-6">
	      					<ol>
	      						<li>Pilih beberapa Item barang</li>
	      						<li>Klik tombol tambahkan seperti di samping</li>
	      						<li>Masukan No. {{ $prq->no_prq }} pada form pencarian</li>
	      					</ol>
	      					<p>
	      						Jika mengerti klik tombol di bawah ini untuk melanjutkan.
	      					</p>
	      				</div>
	      			</div>
	      		</div>
	      	</div>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
	        <a href="{{ url('/prq/select/' .$tipe) }}" class="btn btn-primary">Ya, Mengerti!</a>
	      </div>
	    </div>
	  </div>
	</div>

@endsection
