<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
if(empty($records)){
	die('Invalid request');
}
$fields = array(
		'banner_title'=>'Title',
		'banner_subtitle'=>'Sub Title',
		'banner_text'=>'Text',
		'banner_id'=>'Image',
		'banner_display_order'=>'Display Order',
		'banner_active'=>'Status',
		
		);

?>
<table width='100%' class='table table-responsive'>
	<?php 
	
	foreach($fields as $key=>$value){
			switch($key){
				
				case 'banner_active':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo Info::getStatusByKey($records['banner_active']);?></td>
					</tr>
					<?php
					break;
				
				case 'banner_id':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><img src="<?php echo FatUtility::generateUrl('image','banner',array($records['banner_id'],300,300),CONF_BASE_DIR)?>"></td>
					</tr>
					<?php
					break;
				
					
				default:
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo html_entity_decode($records[$key]);?></td>
					</tr>
					<?php
			}
			?>
			
			<?php 
		
	}	
	?>

</table>