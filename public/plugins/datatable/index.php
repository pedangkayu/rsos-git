<?php
/**
 * @author Same
 * @copyright 2014
 */
    $dirplugins = "libraries/datatable/"

?>
<link rel="stylesheet" type="text/css" href="<?php echo $dirplugins; ?>dataTable.bootstrap.css" />
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>media/js/jquery.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>media/js/jquery.dataTables.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>TableTools/js/dataTables.tableTools.js" ></script>

<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>boostrap.3.0.0.min.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>media/js/datatable.bootstrap.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>fnFakeRowspan.js" ></script>


<script>
var dt = jQuery.noConflict();
dt(document).ready(function() {

	dt('.bDataTable').dataTable( {

        "sPaginationType": "bootstrap",
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],

	} );
	dt('.bDataTableNoSort').dataTable( {

        "sPaginationType": "bootstrap",
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        "bSort": false

	} );
} );
</script>