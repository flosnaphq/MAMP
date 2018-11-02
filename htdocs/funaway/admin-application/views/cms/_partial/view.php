<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Title</strong></td>
		<td><?php echo $records['cms_name']?></td>
	</tr>
	<tr>
		<td><strong>Slug</strong></td>
		<td><?php echo $records['cms_slug']?></td>
	</tr>
	<tr>
		<td><strong>Sub heading</strong></td>
		<td><?php echo $records['cms_sub_heading']?></td>
	</tr>
	<tr>
		<td><strong>Show Banner</strong></td>
		<td><?php echo Info::getIsValue($records['cms_show_banner']);?></td>
	</tr>
	<?php if($records['cms_show_banner'] == 1){ ?>
	<tr>
		<td><strong>Banner</strong></td>
		<td><img src=<?php echo FatUtility::generateUrl('image','cms-image',array($records['cms_id'],200,200),CONF_BASE_DIR)?>></td>
	</tr>
	<?php } ?>
	<tr>
		<td><strong>Positions</strong></td>
		<?php if(!empty($positions)) { ?>
		<td><?php echo implode(', ',@$positions['positions_name'])?></td>
		<?php } ?>
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
		<td><strong>Page Type</strong></td>
		<td><?php echo Info::getCmsTypeByKey($records['cms_type'])?></td>
	</tr>
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['cms_active'])?></td>
	</tr>
</table>