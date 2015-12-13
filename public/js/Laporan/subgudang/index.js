$(function(){

	getitem = function(){
		var $id_gudang = $('[name="gudang"]').val();
		var $tipe = $('[name="tipe"]:checked').val();
		$('.items').html('<div class="form-group">Memuat...</div>');
		$.getJSON(_base_url + '/lapsubgudang/informasi', {

			id_gudang 	: $id_gudang,
			tipe 		: $tipe

		}, function(json){
			$('.items').html('\
				<div class="form-group barang">\
        			<label for="id_barang">Item</label>\
        			<select class="select item" style="width:100%;" name="barang" id="id_barang" required>\
        				<option value="">Loading...</option>\
        			</select>\
        		</div>\
			');

			$('.item').html(json.items);
			$('.select').select2();

		});

	}

	getitem();

	$('[name="tipe"]').click(function(){
		getitem();
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
    });

});