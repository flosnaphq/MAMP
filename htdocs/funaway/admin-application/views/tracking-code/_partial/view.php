<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Name</strong></td>
		<td><?php echo $records[TrackingCode::DB_TBL_PREFIX.'name']?></td>
	</tr>
	<tr>
		<td><strong>Tracking Code</strong></td>
		<td><?php echo $records[TrackingCode::DB_TBL_PREFIX.'code']?></td>
	</tr>
	
	<tr>
		<td><strong>Status</strong></td>
		<td><?php echo Info::getStatusByKey($records[TrackingCode::DB_TBL_PREFIX.'status'])?></td>
	</tr>
</table>