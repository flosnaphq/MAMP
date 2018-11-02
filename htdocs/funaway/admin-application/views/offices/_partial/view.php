<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<!--<tr>
		<td><strong>Image</strong></td>
		<td><img src="<?php //echo FatUtility::generateUrl('image','office',array($records['office_id'], 200,200,rand(100,1000)),'/')?>"></td>
	</tr>-->
	<tr>
		<td><strong>Country</strong></td>
		<td><?php echo $records['office_country']?></td>
	</tr>
	<tr>
		<td><strong>Address</strong></td>
		<td><?php echo nl2br($records['office_address'])?></td>
	</tr>
	
	
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['office_active'])?></td>
	</tr>
</table>