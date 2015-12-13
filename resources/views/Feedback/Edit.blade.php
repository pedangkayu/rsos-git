@extends('Master.Template')

@section('meta')
	<script type="text/javascript">
		$(function(){
			$('[name="ask"]').focus();
		});
	</script>	
@endsection

@section('content')
	
	<div class="row">
		<!-- left -->
		<div class="col-sm-9">
			
			<div class="grid simple">
				<div class="grid-body no-border">
					
					<form method="post" action="{{ url('/feedback/edit') }}" enctype="multipart/form-data">
		    			<input type="hidden" name="_token" value="{{ csrf_token() }}">
		    			<input type="hidden" name="id" value="{{ $feed->id_feedback }}">
						<input type="hidden" name="file" value="{{ $feed->file }}">
		      			<div class="modal-header">
		        			<h4 class="modal-title" id="myModalLabel">Edit Feedback</h4>
		      			</div>
				    	<div class="modal-body">
				        	
				    		<div class="grid simple">
								<div class="grid-title no-border"></div>
								<div class="grid-body no-border">
									@if($feed->parent_id == 0)
									<div class="form-group">
				    					<label for="feed_title">Title *</label>
				    					<input type="text" name="feed_title" id="feed_title" value="{{ $feed->title }}" class="form-control" required>
				    				</div>
				    				@endif

				    				<div class="form-group">
				    					<label for="feed_ask">Deskripsi *</label>
				    					<textarea name="feed_ask" id="feed_ask" rows="7" class="form-control" required>{{ $feed->ask }}</textarea>
				    				</div>

				    				@if($feed->parent_id == 0)
				    				<div class="form-group">
				    					<label for="feed_link">Link Module</label>
				    					<input type="text" name="feed_link" id="feed_link" value="{{ $feed->link }}" class="form-control">
				    				</div>
				    				@endif
				    				<div class="form-group">
				    					<label for="feed_file">
				    						<span class="btn btn-white"><i class="fa fa-paperclip"></i> Lampiran</span> <span class="feed_file"></span>
				    					</label>
				    					<input type="file" accept="image/*" name="feed_file" class="sr-only" id="feed_file">
				    				</div>
								</div>
							</div>

				    	</div>
			      		<div class="modal-footer">
			        		<a href="{{ url('/feedback') }}" class="btn pull-left btn-white">Kembali</a>
			        		<button type="submit" class="btn btn-primary">Perbaharui</button>
			      		</div>
		     		</form>

				</div>
			</div>

		</div>

		<!-- right -->
		<div class="col-sm-3">
			
			<div class="grid simple">
				<div class="grid-title text-right no-border"></div>
				<div class="grid-body no-border">
					
					<address>
						<strong>Tanggal</strong>
						<p>
							{{ Format::indoDate($feed->create_at) }}<br />
							<small class="text-muted">{{ Format::hari($feed->create_at) }}, {{ Format::jam($feed->create_at) }}</small>
						</p>
					</address>
						
				</div>
			</div>

		</div>
		
	</div>

@endsection