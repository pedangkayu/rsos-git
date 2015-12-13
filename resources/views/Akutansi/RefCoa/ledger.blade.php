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
      <h4>Add Account Ledger</h4>
      <div class="tools">
        <a href="javascript:;" class="collapse"></a> 
        <a href="javascript:;" class="reload"></a>
      </div>
    </div>
    <div class="grid-body no-border">
      <div class="row">
        <form action="{{ url('/coa/ledger') }}" method="post" role="form">

          <div class="col-sm-10">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if((Session::get('sess')))
            <div class="alert alert-block alert-info">
              <button data-dismiss="alert" class="close" type="button">&times;</button>
              {{ Session::get('sess') }}
            </div>
            @endif


            <div class="form-group">
              <label for="title">Account Ledger *</label>
              <div>
                <input type="text" value="{{ empty($menu->nm_coa_ledger) ? '' : $menu->nm_coa_ledger }}" class="form-control" id="nm_coa" name="nm_coa_ledger" required="required" />
              </div>
            </div>

            <div class="form-group">
              <label for="title">Kode Account *</label>
              <div>
                <input type="text" value="{{ empty($menu->nm_coa_ledger) ? '' : $menu->nm_coa_ledger }}" class="form-control" id="no_coa" name="no_coa_ledger" required="required" />
              </div>
            </div>

            <div class="form-group">
              <label for="parent">Parent Grup</label>
			  <!-- bagian ini tampilkan yang ada di tabel ref_coa saja, bertingkat sesuai dgn grup parent masing2 -->

              <div>
                <select class="form-control" id="parent" name="idparent">
                  <option value="0">None</option>
                  @foreach($parent as $menus)
                  <option value="{{ $menus->id_coa }}" {{ empty($menu) ? '' : $menus->id_coa == $menu->parent_id ? 'selected="selected"' : '' }}>- {{ $menus->nm_coa }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group">
            <label for="title">Opening Balance *</label>
              <div class="row">
                <div class="col-sm-2">
                  <select class="form-control col-sm-3" name="status_balance">
                    <option value="Dr">Dr</option>
                    <option value="Cr">Cr</option>
                  </select>
                </div>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="balance" id="balance">
				  <i>Note : Assets / Expenses always have Dr balance and Liabilities / Incomes always have Cr balance.</i>
                </div>
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

              <button type="submit" class="btn btn-flat btn-primary">Add Account Ledger</button>
              <a href="{{ url('coa') }}"><button type="button" class="btn btn-flat btn-default">Cancel</button></a>
            </div>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>

@stop
