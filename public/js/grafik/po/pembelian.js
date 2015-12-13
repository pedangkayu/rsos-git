$(function(){

	close_sidebar();

    grafik_pembelian = function(){

        var $tahun = $('[name="tahun"]').val();
        $('.highcharts-container').css('opacity', .3);
        $.getJSON(_base_url + '/grafikpo/datapembelian', {tahun : $tahun}, function(json){

            onDataCancel();
            var $tipe = $('[name="tipe"]:checked').val();
            /* Grafik Jumlah transaksi PO */
            $('#container').highcharts({
                chart: {
                    type: $tipe,
                    zoomType: 'x'
                },
                credits :{
                    enabled : false
                },
                title: {
                    text: 'Grafik Jumlah Transaksi Purchase Order (PO)'
                },
                subtitle: {
                    text: 'Priode Tahun ' + $tahun
                },
                xAxis: {
                    categories: json.po.kategori,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Transaksi'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b> total {point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Transaksi PO',
                    data: json.po.data

                }]
            });
            
            /* Grafik pembelian barang & obat */
            $('#grafikitem').highcharts({
                chart: {
                    type: $tipe,
                    zoomType: 'x'
                },
                title: {
                    text: 'Grafik Pembelian Item Barang & Obat'
                },
                credits :{
                    enabled : false
                },
                subtitle: {
                    text: 'Priode Tahun ' + $tahun
                },
                xAxis: {
                    categories: json.obat.kategori,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah item Barang / Obat'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>total {point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Obat',
                    data: json.obat.data

                }, {
                    name: 'Barang',
                    data: json.barang.data

                }]
            });
            

            $('.highcharts-container').css('opacity', 1);
        });
        
    }

    grafik_pembelian();

	$('.cari').click(function(){
        grafik_pembelian();
    });
});