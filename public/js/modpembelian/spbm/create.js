$(function(){
	
	$('.tgl').datepicker({
		format : 'yyyy-mm-dd'
	});

	$('[data-exp="exp"]').datepicker({
		format : 'yyyy-mm-dd'
	});
	
	$('[name="sj"]').focus();

	$('[type="number"]').change(function(){
		var qty = $(this).val();
		var max = $(this).data('max');
		if(qty > max)
			$(this).val(max);
		else if(qty < 0)
			$(this).val(0);
	});

	addbonus = function(id){
		$('.btn-' + id).button('loading');
		$.post(_base_url + '/gr/addbonus', { id : id }, function(json){
			$('.bonus').append(json.content);
			$('.btn-' + id).button('reset');
			$('.btn-' + id).addClass('hide');

			$('[data-exp="bonus"]').datepicker({
				format : 'yyyy-mm-dd'
			});

			countbonus(function(res){
				if(res > 0)
					$('tr.no-bonus').addClass('hide');
				else
					$('tr.no-bonus').removeClass('hide');
			});

		}, 'json');

	}

	rmbonus = function(id){
		$('.bonus-' + id).remove();
		$('.btn-' + id).removeClass('hide');
		countbonus(function(res){
			if(res > 0)
				$('tr.no-bonus').addClass('hide');
			else
				$('tr.no-bonus').removeClass('hide');
		});
	}

	countbonus = function(res){
		var count = [];
		$('.item-bonus').each(function(i){
			count[i] = i;
		});
		return res(count.length);	
	}

	$('form').submit(function(){
		$('.btn-kembali').remove();
		swal('', 'Proses ini membutuhkan beberapa waktu...');
	});

});