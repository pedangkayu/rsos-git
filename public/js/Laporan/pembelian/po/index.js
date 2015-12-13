$(function(){

	getlaporan = function(page){
		
		var $waktu = $('[name="waktu"]:checked').val();
		var $bulan = $('[name="bulan"]').val();
		var $tahun = $('[name="tahun"]').val();

		var $dari 	= $('[name="dari"]').val();
		var $sampai = $('[name="sampai"]').val();

		var $tipe = $('[name="tipe"]').val();
		var $limit = $('[name="limit"]').val();
		
		$('.item-po').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$('.btn-proses').button('loading');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/laporanpo/laporan',
			data 	: {
				page 	: page,
				waktu	: $waktu,
				bulan	: $bulan,
				tahun	: $tahun,
				dari	: $dari,
				sampai	: $sampai,
				tipe	: $tipe,
				limit	: $limit
			},
			cache 		: false,
			dataType 	: 'json',
			success 	: function(json){
				$('.item-po').html(json.content);
				$('.pagin').html(json.pagin);
				$('.total').html(json.total);
				$('.item-po').css('opacity', 1);
				$('body').css('cursor', 'default');

				$('div.pagin > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					getlaporan($page);
				});

				onDataCancel();
				$('[data-toggle="tooltip"]').tooltip();
				$('.btn-proses').button('reset');

			}
		});

	}

	$('.btn-proses').click(function(){
		getlaporan(1);
	});

});