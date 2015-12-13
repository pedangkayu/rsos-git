$(function(){

	logharga = function(vendor, id_barang, page){
		var kode = $('.harga-kode');
		var head = $('.harga-head');
		var body = $('.harga-body');
		var pagin = $('.harga-pagin');

		kode.html('');
		head.html('');
		body.html('Memuat...');
		pagin.html('');

		$.getJSON(_base_url + '/sph/logharga', {

			vendor : vendor,
			id_barang : id_barang,
			page : page

		}, function(json){

			kode.html(json.kode);
			head.html(json.vendor);
			body.html(json.content);
			pagin.html(json.pagin);

			$('div.harga-pagin > ul.pagination > li > a').click(function(e){
				e.preventDefault();
				var $link 	= $(this).attr('href');
				var $split 	= $link.split('?page=');
				var $page 	= $split[1];
				logharga(vendor, id_barang, $page);
			});

		});
	}

});