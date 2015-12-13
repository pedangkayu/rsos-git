$(function(){

	$.getJSON(_base_url + '/grafikpo/dashboard', {}, function(json){

		$('#obat-chart').html('');
		$('#barang-chart').html('');
		$('#vendor-chart').html('');
		
		/* -------------------- OBAT -----------------------*/
		new Morris.Bar({
			// ID of the element in which to draw the chart.
			element: 'obat-chart',
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

		/* -------------------- BARANG -----------------------*/
		new Morris.Bar({
			// ID of the element in which to draw the chart.
			element: 'barang-chart',
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

		/* -------------------- VENDORS -----------------------*/
		new Morris.Bar({
			// ID of the element in which to draw the chart.
			element: 'vendor-chart',
			// Chart data records -- each entry in this array corresponds to a point on
			// the chart.
			data: json.vendor,
			// The name of the data record attribute that contains x-values.
			xkey: 'vendor',
			// A list of names of data record attributes that contain y-values.
			ykeys: ['value'],
			// Labels for the ykeys -- will be displayed when you hover over the
			// chart.
			labels: ['Supplier'],

			barRatio: 0.4,
			xLabelAngle: 20,
			hideHover: 'auto'
		});

		$('.vtop').html(json.tablevendor.length);

		if(json.tablevendor.length > 0){
			var table = '';
			for(i=0;i<json.tablevendor.length;i++){
				table += '<tr>\
					<td>' + json.tablevendor[i].kode + '</td>\
					<td>\
						<div>' + json.tablevendor[i].nama + '</div>\
						<small class="text-muted">' + json.tablevendor[i].telpon  + '</small>\
						<small class="text-muted">' + json.tablevendor[i].alamat  + '</small><br />\
					</td>\
					<td class="text-right">' + json.tablevendor[i].total + '</td>\
				<tr>';
			}
			$('.tbl-vendors').html(table);
		}
		
	});

});