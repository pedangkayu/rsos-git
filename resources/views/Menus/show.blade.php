@extends('Master.Template')

@section('meta')
	<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/dragdrop/drugdrop.css') }}">
	<script type="text/javascript" src="{{ asset('/plugins/dragdrop/jquery.nestable.js') }}"></script>
@stop

@section('content')
	
	<div class="row">
		<div class="col-md-6">
			
				@if(empty($menu))
					<form class="form-horizontal form-bordered" action="{{ url('/menu/add') }}" method="post" role="form">
				@else
					<form class="form-horizontal form-bordered" action="{{ url('/menu/update') }}" method="post" role="form">
				@endif

				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="grid simple">
				  	<div class="grid-title no-border">
				  		<h4 class="panel-title">{{ !empty($menu) ? 'Update' : 'Add' }} Menu</h4>
				  	</div>
				  	<div class="grid-body no-border">

				  	@if((Session::get('sess')))
				  		<div class="alert alert-block alert-info">
	                        <button data-dismiss="alert" class="close" type="button">&times;</button>
	                        {{ Session::get('sess') }}
	                    </div>
				  	@endif

				  		<div class="form-group">
		                    <label for="title" class="control-label col-sm-3">Title *</label>

		                    <div class="controls col-sm-9">
		                        <input type="text" value="{{ empty($menu->title) ? '' : $menu->title }}" class="form-control" id="title" name="title" required="required" />
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label for="slug" class="control-label col-sm-3">Slug</label>

		                    <div class="controls col-sm-9">
			                    <div class="input-group">
		                            <span class="input-group-addon">{{ url() }}/</span>
		                             <input type="text" value="{{ empty($menu->slug) ? '' : $menu->slug }}" placeholder="controller/method" class="form-control" id="slug" name="slug" />
	                        	</div>
	                        </div>
		                </div>

		                <div class="form-group">
		                    <label for="icon" class="control-label col-sm-3">Icon</label>

		                    <div class="controls col-sm-9">
		                        <input type="text" value="{{ empty($menu->class) ? '' : $menu->class }}" class="form-control" placeholder="fa " id="icon" name="class" />
		                        <small class="text-muted">* fa fa-fw fa-caret-right</small>
		                        <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank" class="pull-right">Check icon here!</a>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label for="id" class="control-label col-sm-3">#Id</label>

		                    <div class="controls col-sm-9">
		                        <input type="text" value="{{ empty($menu->id) ? '' : $menu->id }}" class="form-control" id="id" name="id" />
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label for="parent" class="control-label col-sm-3">Parent</label>

		                    <div class="controls col-sm-9">
		                        <select class="form-control" id="parent" name="idparent">
		                        	<option value="0">None</option>
		                        	@foreach($parent as $menus)
		                        		<option value="{{ $menus->id_menu }}" {{ empty($menu) ? '' : $menus->id_menu == $menu->parent_id ? 'selected="selected"' : '' }}>- {{ $menus->title }}</option>
		                        	@endforeach
		                        </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-sm-3">Status</label>

		                    <div class="controls col-sm-9">

		                     	<div class="controls">
				                    <div class="radio radio-success">
		                                <input type="radio" id="yes" value="1" name="status" class="icheck minimal" {{ !empty($menu) && $menu->status == 1 ? 'checked="checked"' : empty($menu) ? 'checked="checked"' : ''  }}>
		                                <label for="yes">Enable</label>
		                            </div>

		                            <div class="radio radio-success">
		                                <input id="no" type="radio" name="status" value="0" class="icheck minimal" {{ !empty($menu) && $menu->status == 0 ? 'checked="checked"' : '' }}>
		                                <label for="no">Disable</label>
		                            </div>
		                            
			                        
			                    </div>

		                    </div>
		                </div>

		                @if(!empty($menu))
		                 <div class="form-group">
		                    <label class="control-label col-sm-3">Delete</label>

		                    <div class="controls col-sm-9">
		                         <div class="checkbox check-success">
	                                <input name="del" id="del" type="checkbox" value="1">
	                                <label for="del">Yes</label>
	                            </div>
		                    </div>
		                </div>
		                @endif

		                 <div class="form-group">
		                    <label for="note" class="control-label col-sm-3">Note</label>

		                    <div class="controls col-sm-9">
		                        <input value="{{ empty($menu->ket) ? '' : $menu->ket }}" type="text" class="form-control" id="note" placeholder="Admin, Superadmin, User" name="ket" />
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-sm-3"></label>

		                    @if(Auth::user()->permission == 3)
		                    <div class="controls col-sm-9">
		                    	<button type="submit" class="btn btn-flat btn-primary">{{ !empty($menu) ? 'Update' : 'Add' }} Menu</button>
		                    	@if(!empty($menu))
		                    		<a href="{{ url('/menu/add') }}" class="btn btn-link pull-right"><strong>Cancel</strong></a>
		                    		<input type="hidden" value="{{ $menu->id_menu }}" name="id_menu" />
		                    	@endif
		                    </div>
		                    @endif
		                </div>

				  	</div>
				</div>
			</form>
		</div>
		<div class="col-md-6">
			
				<div class="panel panel-default">
				  	<div class="panel-heading">
				  		<h4 class="panel-title">Menu Position</h4>
				  	</div>
				  	<div class="panel-body">
				  		
				  		<div class="dd" id="nestable">
				  			{!! Menu::menuPosition() !!}
				  		</div>
				  	</div>
				</div>

				@if(Auth::user()->permission == 3)
				<div class="panel panel-default">
				  	<div class="panel-body">
				  		
				  		<div>
				  			<form method="post" action="{{ url('/menu/saveposition') }}">
					            <button type="submit" name="simpan" class="btn btn-flat btn-primary">Save Position</button>
					            <input type="hidden" value="" name="update" id="nestable-output">
					            <input type="hidden" name="_token" value="{{ csrf_token() }}">
					        </form>
				  		</div>

				  	</div>
				</div>
				@endif

		</div>
	</div>

@stop

@section('footer')
	
	<script type="text/javascript">
    	var updateOutput = function(e){

            var list   = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));			
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };
        
        $('#nestable').nestable({
        	group: 1
        })
        .on('change', updateOutput);
        updateOutput($('#nestable').data('output', $('#nestable-output')));
	</script>

@stop