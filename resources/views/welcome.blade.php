@extends('Master.Template')

@section('meta')
	<!-- BEGIN PAGE LEVEL JS -->
	<script src="{{ asset('/plugins/raphael/raphael-min.js') }}"></script>
	<script src="{{ asset('/plugins/pace/pace.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/jquery-slider/jquery.sidr.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/jquery-numberAnimate/jquery.animateNumbers.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('/plugins/jquery-ricksaw-chart/js/d3.v2.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-ricksaw-chart/js/rickshaw.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-morris-chart/js/morris.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-flot/jquery.flot.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-flot/jquery.flot.time.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-flot/jquery.flot.selection.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-flot/jquery.flot.animator.min.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-flot/jquery.flot.orderBars.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-sparkline/jquery-sparkline.js') }}"></script>
	<script src="{{ asset('/plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<script src="{{ asset('/js/charts.js') }}" type="text/javascript"></script>

	<link href="{{ asset('/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
	<link href="{{ asset('/plugins/jquery-slider/css/jquery.sidr.light.css') }}" rel="stylesheet" type="text/css" media="screen"/>
	<link rel="stylesheet" href="{{ asset('/plugins/jquery-ricksaw-chart/css/rickshaw.css') }}" type="text/css" media="screen">
	<link rel="stylesheet" href="{{ asset('/plugins/jquery-morris-chart/css/morris.css') }}" type="text/css" media="screen">
@endsection

@section('title')
	Dashboard 
@endsection

@section('content')
	<div class="row">
        <div class="col-md-5">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Flot <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
                <h3>Grafik Pendapatan<span class="semi-bold"> Onkologi</span></h3>
                <br>
                <div id="placeholder" class="demo-placeholder" style="width:100%;height:250px;"></div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="mini-chart-wrapper">
                      <div class="chart-details-wrapper">
                        <div class="chartname"> New Orders </div>
                        <div class="chart-value"> 17,555 </div>
                      </div>
                      <div class="mini-chart">
                        <div id="mini-chart-orders"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mini-chart-wrapper">
                      <div class="chart-details-wrapper">
                        <div class="chartname"> My Balance </div>
                        <div class="chart-value"> $17,555 </div>
                      </div>
                      <div class="mini-chart">
                        <div id="mini-chart-other" ></div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Sparkline <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
              <div class="row-fluid">
                <h3>Grafik Kunjungan <span class="semi-bold">Pasien</span></h3>
              </div>
            </div>
            <div class="tiles white no-margin"> <br>
              <br>
              <br>
              <span id="mysparkline"></span> </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Sparkline <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
                <h3>Grafik Rawat Inap <span class="semi-bold"> Pasien</span></h3>
				         
            </div>
            <div class="tiles white no-margin"><span id="spark-2"></span></div>
          </div>
        </div>
        <div class="col-md-5 ">
          <div class="tiles white no-margin">
            <div class="tiles-body">
              <div class="controller"> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
              <div class="tiles-title"> SERVER LOAD </div>
              <div class="heading text-black "> 250 GB </div>
              <div class="progress  progress-small no-radius progress-success">
                <div class="bar animate-progress-bar" data-percentage="25%" ></div>
              </div>
              <div class="description"> <span class="mini-description"><span class="text-black">250GB</span> of <span class="text-black">1,024GB</span> used</span> </div>
            </div>
          </div>
          <div class="tiles white no-margin">
            <div id="updatingChart"> </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
	   <div class="col-md-12">
        <div class="grid simple">
          <div class="grid-title no-border">
            <h4>Morris <span class="semi-bold">Charts</span></h4>
            <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
          </div>
          <div class="grid-body no-border">
            <div class="row">
              <div class="col-md-6">
                <h4>Grafik <span class="semi-bold">Stok Gudang Logistik</span></h4>
 
                <div id="line-example"> </div>
              </div>
              <div class="col-md-6">
                <h4>Grafik Transaksi <span class="semi-bold"> Purchase Order</span></h4>
                <div id="area-example"> </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
	  <div class="row">
		<div class="col-md-6">
			<div class="grid simple">
			  <div class="grid-title no-border">
				<h4>Bar <span class="semi-bold">Charts</span></h4>
				<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
			  </div>
			  <div class="grid-body no-border">					
				<h4>Grafik Logistik <span class="semi-bold"> Stok Obat</span></h4>
				<br>
				<div id="placeholder-bar-chart" style="height:250px"></div>					
			 </div>
		   </div>
		</div>
		<div class="col-md-6">
			<div class="grid simple">
			  <div class="grid-title no-border">
				<h4>Bar <span class="semi-bold">Charts</span></h4>
				<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
			  </div>
			  <div class="grid-body no-border">
				<div class="row-fluid">	
				<h4>Grafik Logistik Bar <span class="semi-bold"> Stok Barang (Non Obat)</span></h4>
				<br>				
				<div id="stacked-ordered-chart" style="height:250px"></div>

				</div>
			 </div>
		   </div>			
		</div>		
	  </div>
      <div class="row">
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Morris <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
                <h4>Grafik Penjualan<span class="semi-bold">  Apotik</span></h4>
                <div id="donut-example" style="height:200px;"> </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Pie <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
				 <h4>Grafik <span class="semi-bold"> Usia Pasien</span></h4>
				<br>
				<div id="sparkline-pie" class="col-md-12"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Morris <span class="semi-bold">Charts</span></h4>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            </div>
            <div class="grid-body no-border">
                <h4>Grafik Jenis <span class="semi-bold"> Kelamin Pasien</span></h4>
				<br>
			   <div class="pull-left">
			      <p>Pria</p>
                  <div id="ram-usage" class="easy-pie-custom" data-percent="85"><span class="easy-pie-percent">85</span></div>
                </div>
                <div class="pull-right">
				  <p>Wanita</p>
                  <div id="disk-usage" class="easy-pie-custom" data-percent="73"><span class="easy-pie-percent">73</span></div>
                </div>
				<div class="clearfix"></div>
            </div>
          </div>
        </div>
      </div>
    

@endsection