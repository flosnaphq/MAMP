<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
if(empty($records)){
	die('Invalid request');
}
$fields = array(
		'ad_title'=>'Name',
		'ad_place_id'=>'Place',
		'ad_created'=>'Created Date',
		'ad_starting_date'=>'Starting Date',
		'ad_ending_date'=>'Ending Date',
		'ad_display_order'=>'Display Order',
		'ad_active'=>'Status',
		'ad_link'=>'Link',
		'afile_id'=>'Image',
		);

?>
<table width='100%' class='table table-responsive'>
	<?php 
	
	foreach($fields as $key=>$value){
			switch($key){
				case 'ad_created':
				case 'ad_starting_date':
				case 'ad_ending_date':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo FatDate::format($records[$key],true);?></td>
					</tr>
					<?php
					break;
				case 'ad_active':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo Info::getAdStatusByKey($records['ad_active']);?></td>
					</tr>
					<?php
					break;
				case 'ad_link':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><a href="<?php $records['ad_link']?>"><?php echo $records['ad_link']?></a></td>
					</tr>
					<?php
					break;
				case 'afile_id':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><img src="<?php echo FatUtility::generateUrl('banners','image',array($records['afile_id'],300,300))?>"></td>
					</tr>
					<?php
					break;
				case 'ad_place_id':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo Info::getAdPlaceByKey($records[$key]);?></td>
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