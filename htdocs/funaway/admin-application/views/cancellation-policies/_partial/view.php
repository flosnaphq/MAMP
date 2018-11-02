<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Name</strong></td>
		<td><?php echo $records[CancellationPolicy::DB_TBL_PREFIX.'name']?></td>
	</tr>
	<tr>
		<td><strong>User Type</strong></td>
		<td><?php echo Info::getUserTypeByKey($records[CancellationPolicy::DB_TBL_PREFIX.'user_type'])?></td>
	</tr>
	<tr>
		<td><strong>Content</strong></td>
		<td><?php echo html_entity_decode($records[CancellationPolicy::DB_TBL_PREFIX.'content'])?></td>
	</tr>
	<tr>
		<td><strong>Display Order</strong></td>
		<td><?php echo $records[CancellationPolicy::DB_TBL_PREFIX.'display_order']?></td>
	</tr>
	<tr>
		<td><strong>Day(s)</strong></td>
		<td><?php echo $records[CancellationPolicy::DB_TBL_PREFIX.'days']?></td>
	</tr>
	
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records[CancellationPolicy::DB_TBL_PREFIX.'active'])?></td>
	</tr>
</table>