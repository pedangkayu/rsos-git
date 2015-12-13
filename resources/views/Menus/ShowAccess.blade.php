@extends('Master.layout')

@section('meta')
	<link rel="stylesheet" type="text/css" href="{{ asset('/lib/treeview_check/jquery-checktree.css') }}" />
	<script type="text/javascript" src="{{ asset('/lib/treeview_check/jquery-checktree.js') }}"></script>

	<style>
	    ul#tree{}
	    ul#tree li{padding: 5px;}
	</style>
@stop

@section('content')
	
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
			  	<div class="panel-heading">
			  		<h4 class="panel-title">Level</h4>

	                <div class="panel-options">
	                    <a href="#" data-rel="collapse"><i class="fa fa-fw fa-minus"></i></a>
	                </div>
			  	</div>
			  	<div class="panel-body">
			    	<div class="list-group">
			    		@foreach($level as $lev)
			    			<a href="{{ url('/menu/access/' . $lev->id_level) }}" class="list-group-item {{ $id ==  $lev->id_level ? 'active' : ''}}">
			    				{{ $lev->nm_level }}
			    			</a>
			    		@endforeach
					</div>
			  	</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel panel-default">
			  	<div class="panel-heading">
			  		<h4 class="panel-title"><i class="fa fa-unlock-alt"></i> Access Menu {{ $id > 0 ? 'for ' . App\Model\Level::find($id)->nm_level : '' }}</h4>

	                <div class="panel-options">
	                    <a href="#" data-rel="collapse"><i class="fa fa-fw fa-minus"></i></a>
	                </div>
			  	</div>

			  	
			  	<div class="panel-body">
			  		<form method="post" action="{{ url('/menu/saveaccessmenu') }}">
			  			<input type="hidden" name="_token" value="{{ csrf_token() }}">
				    	{!! Menu::MenuAkses($id) !!}

				    	@if($id > 0 && Auth::user()->permission == 3)

				    		<hr />
				    		<button class="btn btn-flat btn-primary">save Changes</button>
				    		<input type="hidden" value="{{ $id }}" id="id_level" name="id_level" />
				    	@endif
			    	</form>
			  	</div>
			  	

			</div>
		</div>
	</div>

	
@stop

@section('footer')
	<script type="text/javascript">
		// $(function(){
			$('ul#tree').checktree();
		// });
	</script>
@stop