<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Category Name</strong></td>
		<td><?php echo $records['faqcat_name']?></td>
	</tr>
	
	<tr>
		<td><strong>Display Order</strong></td>
		<td><?php echo $records['faqcat_display_order']?></td>
	</tr>
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['faqcat_active'])?></td>
	</tr>
</table>

