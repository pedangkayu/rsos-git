$(function(){

	$('[name="id_coa_ledger"]').select2();
	$('[name="perkiraan"]').select2();
	$('[name="id_payment_methode"]').select2();
	$('[name="tanggal"]').datepicker();


	setstatus = function(status){
		var id = $('[name="id_faktur"]').val();
		$('.header-status').css('opacity',.3);
		$.post(_base_url + '/fakturpembelian/status', {status : status , id : id}, function(json){
			$('.status-faktur').html(json.err);
			swal(json.status);
			$('.header-status').css('opacity',1);
		}, 'json');
	}

});