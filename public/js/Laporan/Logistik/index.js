$(function(){

	$.getJSON(_base_url + '/reportlogistik/informasi', {}, function(json){
		$('.item-barang').html(json.barang);
		$('.item-obat').html(json.obat);
		$('#id_kategori').html(json.kategori);
		$('#id_klasifikasi').html(json.klasifikasi);
		$('.select').select2();

		$('.summary-total').html(json.total);
	});

	$('.tipe-src').click(function(){
		var val = $(this).val();
		if(val == 1){
			$('.barang').removeClass('hide');
			$('.kategori').addClass('hide');
			$('.klasifikasi').addClass('hide');
		}else if(val == 2){
			$('.kategori').removeClass('hide');
			$('.barang').addClass('hide');
			$('.klasifikasi').addClass('hide');
		}else{
			$('.klasifikasi').removeClass('hide');
			$('.barang').addClass('hide');
			$('.kategori').addClass('hide');
		}
	});

	$('.waktu-src').click(function(){
		var val = $(this).val();
		if(val == 1){
			$('.pertanggal').addClass('hide');
			$('.perbulan').removeClass('hide');
		}else{
			$('.perbulan').addClass('hide');
			$('.pertanggal').removeClass('hide');
		}
	});

	$('.waktu-srcb').click(function(){
		var val = $(this).val();
		if(val == 1){
			$('.pertanggalb').addClass('hide');
			$('.perbulanb').removeClass('hide');
		}else{
			$('.perbulanb').addClass('hide');
			$('.pertanggalb').removeClass('hide');
		}
	});


	//////////////////////////////// DUMMY GRAFIK ///////////////////////
    $.getJSON(_base_url + '/reportlogistik/grafik', {}, function(json){

        $('#graphobat').html('');
        $('#graphbarang').html('');

        new Morris.Bar({
            // ID of the element in which to draw the chart.
            element: 'graphobat',
            // Chart data records -- each entry in this array corresponds to a point on
            // the chart.
            data: json.obat,
            // The name of the data record attribute that contains x-values.
            xkey: 'obat',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['value'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Obat'],

            barRatio: 0.4,
            xLabelAngle: 20,
            hideHover: 'auto'
        });
       
        new Morris.Bar({
            // ID of the element in which to draw the chart.
            element: 'graphbarang',
            // Chart data records -- each entry in this array corresponds to a point on
            // the chart.
            data: json.barang,
            // The name of the data record attribute that contains x-values.
            xkey: 'barang',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['value'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Barang'],

            barRatio: 0.4,
            xLabelAngle: 20,
            hideHover: 'auto'
        }); 
    });

});