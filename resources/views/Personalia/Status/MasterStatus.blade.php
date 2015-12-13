@extends('Master.Template')

@section('meta')
<link href="{{ asset ('/plugins/bootstrap-select2/select2.css')}}" rel="stylesheet" type="text/css" media="screen"/>
<script src="{{ asset ('/js/tabs_accordian.js') }}" type="text/javascript"></script>
<script src="{{ asset ('/plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
  $(document).ready(function() { 
    $("#id_karyawan").select2(); 
  });
  $(document).ready(function() { 
    $("#tidak_aktif").select2(); 
  });
   $(document).ready(function() { 
    $("#kehadirans").select2(); 
  });

</script>

@endsection

@section('title')
Master Karyawan
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="tabbable tabs-left">
      <ul class="nav nav-tabs" id="tab-2">
        <li class="active"><a href="#status_aktif">Status Aktif</a></li>
        <li><a href="#status_tidak_aktif">Status Tidak Aktif</a></li>
        <li><a href="#kehadiran">Kehadiran</a></li>
        <li><a href="#cuti">Cuti</a></li>
        <li><a href="#keterlambatan">Keterlambatan Pegawai</a></li>
        <li><a href="#meninggalkan">Meninggalkan Pekerjaan</a></li>
        <li><a href="#skk">Dokumen SKK</a></li>
        <li><a href="#pegawai_aktif">Pegawai Tidak Aktif</a></li>
      </ul>
      <div class="tab-content">
        
        <div class="tab-pane active" id="status_aktif">
          <div class="row">
            @include('Personalia.status_aktif')
          </div>
        </div>
        <div class="tab-pane" id="status_tidak_aktif">
          <div class="row">
            @include('Personalia.status_tidakaktif')
          </div>
        </div>
        <div class="tab-pane" id="kehadiran">
          <div class="row">
           @include('Personalia.status_kehadiran')
          </div>
        </div>
        <div class="tab-pane" id="cuti">
          <div class="row">
           @include('Personalia.status_cuti')
          </div>
        </div>
        <div class="tab-pane" id="keterlambatan">
          <div class="row">
           @include('Personalia.keterlambatan')
          </div>
        </div>
        <div class="tab-pane" id="meninggalkan">
          <div class="row">
           @include('Personalia.status_meninggalkan')
          </div>
        </div>
        <div class="tab-pane" id="skk">
          <div class="row">
           @include('Personalia.status_skk')
          </div>
        </div>
        <div class="tab-pane" id="pegawai_aktif">
          <div class="row">
           @include('Personalia.status_pegawai_aktif')
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection