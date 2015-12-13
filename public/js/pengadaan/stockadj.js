$(function(){
	/*Pagination*/
	getItems = function(page){
		
		var $kode 	= $('[name="kode"]').val();
		var $item 	= $('[name="nm_barang"]').val();
		var $kat 	= $('[name="id_kategori"]').val();
		var $tipe 	= $('[name="jenis"]').val();
		var $limit 	= $('[name="limit"]').val();

		$('.content-barang').css('opacity', .3);
		$('body').css('cursor', 'wait');

		$.ajax({
			type 	: 'GET',
			url 	: _base_url + '/stockadj/allitems',
			data 	: {
				page 	: page,
				kode 	: $kode,
				item 	: $item,
				kat 	: $kat,
				tipe 	: $tipe,
				limit 	: $limit
			},
			cache 	: false,
			dataType : 'json',
			success : function(res){
				$('.content-barang').html(res.data);
				$('.pagins').html(res.pagin);
				$('.content-barang').css('opacity', 1);
				$('body').css('cursor', 'default');

				onDataCancel();

				$('div.pagins > ul.pagination > li > a').click(function(e){
					e.preventDefault();
					var $link 	= $(this).attr('href');
					var $split 	= $link.split('?page=');
					var $page 	= $split[1];
					getItems($page);
				});
			}
		});

	}
	
	$('div.pagins > ul.pagination > li > a').click(function(e){
		e.preventDefault();
		var $link 	= $(this).attr('href');
		var $split 	= $link.split('?page=');
		var $page 	= $split[1];
		getItems($page);
	});
	/*End Pagination*/

	/*Advance Searching*/
	$('.Searching').click(function(){
		getItems(1);
	});
	$('[name="id_kategori"]').change(function(){
		getItems(1);
	});
	$('[name="jenis"]').change(function(){
		getItems(1);
	});
	$('[name="limit"]').change(function(){
		getItems(1);
	});
	$('[name="kode"]').keyup(function(e){
		if(e.keyCode == 13 || $(this).val().length == 0){
			getItems(1);
		}
	});
	$('[name="nm_barang"]').keyup(function(e){
		if(e.keyCode == 13 || $(this).val().length == 0){
			getItems(1);
		}
	});
	/*End Advance Searching*/

	/*Tambah Item Barang*/
	add = function(id){
		$('.item_' + id).css('opacity', .3);
		$('body').css('cursor', 'wait');
		$.ajax({
			type : 'POST',
			url : _base_url + '/stockadj/additem',
			data : {
				id : id
			},
			cache : false,
			dataType : 'json',
			success : function(res){
				$('.item_' + res.id).remove();
				itemSelected();
				$('body').css('cursor', 'default');
			}
		});
	}

	itemSelected = function(){
		$('.item-selected').css('opacity', .3);
		var $tipe 	= $('[name="jenis"]').val();
		$.getJSON(_base_url + '/stockadj/itemselected', {tipe : $tipe}, function(res){
			$('.item-selected').html(res.data);
			$('.loading-item-selected').addClass('hide');
			$('.total').html(res.count);
			$('.item-selected').css('opacity', 1);

			if(res.count > 0){
				$('.cart').removeClass('hide');
			}else{
				$('.cart').addClass('hide');
			}

			$('.hover-item').hover(function(){
				$(this).find('.oneitem').toggle();
			});
		});
	}

	/*Delete All Item*/
	$('.dellAll').click(function(){
		$(this).button('loading');
		$.getJSON(_base_url + '/stockadj/dellall', {}, function(ses){
			itemSelected();
			getItems(1);
			$('.dellAll').button('reset');
		});
	});
	/*Menghapu sitem terpilih stu per satu*/
	trashme = function(id){
		$('.me_' + id).css('opacity', .3);
		$('body').css('cursor', 'wait');
		$('.me_' + id).find('.oneitem').remove();
		$.post(_base_url + '/stockadj/trashme', {id : id}, function(res){
			if(res.result == true){
				$('.me_' + id).remove();
				getItems(1);
			}
		}, 'json');
			
	}
	itemSelected();

	detailprq = function(id){
		$('.modal-noprq').html('');
		$('.modal-contentprq').html('Memuat...');
		$('.btn-acc').html('');
		$.getJSON(_base_url + '/stockadj/detailprq', {
			id_prq : id
		}, function(json){
			$('.modal-noprq').html(json.no_prq);
			$('.modal-contentprq').html(json.content);
			$('.btn-acc').html(json.btn);
		});
	}

	accprq = function(id){
		$('.btn-accs').button('loading');
		$.post(_base_url + '/stockadj/accprq', {id : id}, function(json){
			console.log(json);
			$('.btn-accs').remove();
		}, 'json');
	}

	hapusprq = function(id){
		swal({
			title: "Anda yakin ?",   
			text: "Anda yakin ingin membatalkan pengajuan ini ?",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#ff0000",   
			confirmButtonText: "Yes, Delete it!",   
			closeOnConfirm: true 
		}, function(){
			
			$('.item-prq-' + id).css('opacity', .3);
			$.post(_base_url + '/stockadj/detailprq', {id : id}, function(json){
				$('.item-prq-' + id).fadeOut(function(){
					$(this).remove();
				});
			}, 'json');
			
		});
	}
	
});
