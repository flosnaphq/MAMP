<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Name</strong></td>
		<td><?php echo $records['block_name']?></td>
	</tr>
	<tr>
		<td><strong>Title</strong></td>
		<td><?php echo $records['block_title']?></td>
	</tr>
	<tr>
		<td><strong>Content</strong></td>
		<td><?php echo html_entity_decode($records['block_content'])?></td>
	</tr>
	
	
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['block_active'])?></td>
	</tr>
</table>