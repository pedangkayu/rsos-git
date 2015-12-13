@extends('Master.Template')

@section('meta')
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/js/akunting/coa/coa.js') }}" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/plugins/dragdrop/drugdrop.css') }}">
<script type="text/javascript" src="{{ asset('/plugins/dragdrop/jquery.nestable.js') }}"></script>
@stop

@section('title')
Chart Of Account 
@endsection

@section('content')

<div class="col-md-12">
  <div class="grid simple">
    <div class="grid-title no-border">
      <h4>Add Account Group</h4>
      <div class="tools">
        <a href="javascript:;" class="collapse"></a> 
        <a href="javascript:;" class="reload"></a>
      </div>
    </div>

    <div class="grid-body no-border">
      <div class="row">
        <form action="{{ url('/coa/add') }}" method="post" role="form">
          <div class="col-sm-10">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if((Session::get('sess')))
            <div class="alert alert-block alert-info">
              <button data-dismiss="alert" class="close" type="button">&times;</button>
              {{ Session::get('sess') }}
            </div>
            @endif

            <div class="form-group">
              <label for="title">Grup Account *</label>
				  <div>
					<input type="text" value="{{ empty($menu->nm_coa) ? '' : $menu->nm_coa }}" class="form-control" id="nm_coa" name="nm_coa" required="required" />
				  </div>
            </div>

            <div class="form-group">
              <label for="title">Kode Account *</label>
				  <div>
					<input type="text" value="{{ empty($menu->no_coa) ? '' : $menu->no_coa }}" class="form-control" id="no_coa" name="no_coa" required="required" />
				  </div>
            </div>

            <div class="form-group">
              <label for="parent">Parent Grup</label>
              <div>
			  <!-- bagian ini tampilkan yang ada di tabel ref_coa saja, bertingkat sesuai dgn grup parent masing2 -->
                <select class="form-control" id="parent" name="idparent">
                  <option value="0">None</option>
                  @foreach($parent as $menus)
                  <option value="{{ $menus->id_coa }}" {{ empty($menu) ? '' : $menus->id_coa == $menu->parent_id ? 'selected="selected"' : '' }}>- {{ $menus->nm_coa }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="parent">Type Coa</label>
			  <!-- bagian ini masih dibutuhkan ato tidak, tergantung RSOS -->
              <div>
                <select class="form-control" id="type_coa" name="type_coa">
                  <option value="">-Pilih-</option>
                  <option value="1">Debit</option>
                  <option value="2">Kredit</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="note">Note</label>
				  <div>
					<textarea value="{{ empty($menu->keterangan) ? '' : $menu->keterangan }}" type="text" class="form-control" id="note" name="keterangan" /></textarea>
				  </div>
            </div>


            <div class="form-group">
              <label></label>

				  @if(Auth::user()->permission == 3)
					  <div>
						<button type="submit" class="btn btn-flat btn-primary">{{ !empty($menu) ? 'Update' : 'Add' }} Account Grup</button>
						<a href="{{ url('coa') }}"><button type="button" class="btn btn-flat btn-default">Kembali</button></a>
						@if(!empty($menu))
						<input type="hidden" value="{{ $menu->id_coa }}" name="id_coa" />
						@endif
					  </div>
				  @endif
            </div>

          </div><!-- EOF -->

        </form>
      </div>

    </div>
  </div>
</div>
@stop
