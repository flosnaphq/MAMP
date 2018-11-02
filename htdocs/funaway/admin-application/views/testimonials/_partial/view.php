<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td colspan="2"><img src="<?php echo FatUtility::generateUrl('image','testimonial',array($records[Testimonial::DB_TBL_PREFIX.'id'],200,200),'/')?>"></td>
	</tr>
	<tr>
		<td><strong>Name</strong></td>
		<td><?php echo $records[Testimonial::DB_TBL_PREFIX.'name']?></td>
	</tr>
	
	<tr>
		<td><strong>Content</strong></td>
		<td><?php echo $records[Testimonial::DB_TBL_PREFIX.'content']?></td>
	</tr>
	
	<tr>
		<td><strong>Status</strong></td>
		<td><?php echo Info::getStatusByKey($records[Testimonial::DB_TBL_PREFIX.'status'])?></td>
	</tr>
</table>