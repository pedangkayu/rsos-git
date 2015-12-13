<?php
	$dirplugins = "libraries/chosen-master/";
?>
<link href="<?php echo $dirplugins; ?>select2.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?php echo $dirplugins; ?>select2-bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php echo $dirplugins; ?>jquery-1.9.1.min.js"></script>
<script src="<?php echo $dirplugins; ?>select2.js"></script>
<script src="<?php echo $dirplugins; ?>select2.min.js"></script>

      
<script type="text/javascript">
$(document).ready(function() {	
	$('.chosen-select').select2({
        placeholder: "Pilih",
        allowClear: false            
	});
});
</script>