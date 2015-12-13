@extends('Master.Template')

@section('meta')
	<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/treeview_check/jquery-checktree.css') }}" />
	<script type="text/javascript" src="{{ asset('/plugins/treeview_check/jquery-checktree.js') }}"></script>

	<style>
	    ul#tree{}
	    ul#tree li{padding: 5px;}
	</style>
@stop

@section('content')
	
	<div class="">
		<div class="row">
			<div class="col-md-6">
				<div class="grid simple">
				  	<div class="grid-body no-border">
				  		<h3>Daftar <span class="semi-bold">Levels</span></h3>
				    	<div class="list-group">
				    		@foreach($level as $lev)
				    			<a href="{{ url('/menu/access/' . $lev->id_level_user) }}" class="list-group-item {{ $id ==  $lev->id_level_user ? 'active' : ''}}">
				    				{{ $lev->nm_level }}
				    			</a>
				    		@endforeach
						</div>
				  	</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="grid simple">
				  	
				  	<div class="grid-body no-border">
				  		<h3><i class="fa fa-unlock-alt"></i> Access Menu <span class="semi-bold">{{ $id > 0 ? 'for ' . App\Models\data_level_user::find($id)->nm_level : '' }}</span></h3>
				  		<hr />
				  		<form method="post" action="{{ url('/menu/saveaccessmenu') }}">
				  			<input type="hidden" name="_token" value="{{ csrf_token() }}">
					    	{!! Menu::MenuAkses($id) !!}
				    		@if($id > 0)
				    		<hr />
				    		<button class="btn btn-flat btn-primary">save Changes</button>
				    		<input type="hidden" value="{{ $id }}" id="id" name="id_level" />
					    	@endif
				    	</form>
				  	</div>
				  	

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