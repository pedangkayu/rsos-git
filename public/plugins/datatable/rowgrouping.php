<?php
/**
 * @author Same
 * @copyright 2014
 */
    $dirplugins = "libraries/datatable/"

?>

<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>media/js/jquery.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>RowGrouping/js/jquery.dataTables.min.js" ></script>
<script type="text/javascript" charset="utf-8" src="<?php echo $dirplugins; ?>RowGrouping/js/jquery.dataTables.rowGrouping.js" ></script>
<script>
var dt = jQuery.noConflict();
dt(document).ready(function() {

	dt('.bDataTable2').dataTable({ "bLengthChange": false, "bPaginate": false})
						.rowGrouping({bExpandableGrouping: true});
			} );
} );
</script>