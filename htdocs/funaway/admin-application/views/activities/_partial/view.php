<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Title</strong></td>
		<td><?php echo $records['cms_name']?></td>
	</tr>
	<tr>
		<td><strong>Sub heading</strong></td>
		<td><?php echo $records['cms_sub_heading']?></td>
	</tr>
	<tr>
		<td><strong>Content</strong></td>
		<td><?php echo html_entity_decode($records['cms_content'])?></td>
	</tr>
	<tr>
		<td><strong>Display Order</strong></td>
		<td><?php echo $records['cms_display_order']?></td>
	</tr>
	
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['cms_active'])?></td>
	</tr>
</table>