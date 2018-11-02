<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>
<?php

$fields = array(
		'user_name'=>'Review By',
		'review_user_id'=>'Image',
		'activity_name'=>'Activity Name',
		'review_rating'=>'Rating',
		'review_content'=>'Content',
		'review_date'=>'Date',
		'reported' => 'Reported Inappropriate',
		'abreport_user_comment' => 'Host Comments',
		'abreport_comments' => 'Admin Comments',
		'abreport_taken_care' => 'Inappropriate Status',
		'review_active' => 'Status',
		
	);


?>
<table width='100%' class='table table-responsive'>
	<?php 
	foreach($fields as $key=>$value){
		switch($key){
			
			/* case 'review_rating';
				?>
				<tr>
					<td><?php echo $value; ?></td>
					<td><?php echo Info::rating($records[$key]); ?></td>
				</tr>
				<?php
				break; */
		
			
			case 'abreport_taken_care':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getAbuseReportStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'review_user_id':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td>
					<?php if($records['review_user_id']){ ?>
					<img src="<?php echo FatUtility::generateUrl('image','user',array($records['review_user_id'], 100,100),'/')?>"/>
					<?php }else{  ?>
						<img src="<?php echo FatUtility::generateUrl('image','user',array($records['review_id'], 100,100,1),'/')?>"/>
					<?php } ?>
					</td>
				</tr>
				<?php
				break;
			case 'user_name':
				?>
				<tr>
					<td><?php echo $value?></td>
				<?php
				if($records['review_user_id']){
					?>
					<td><?php echo $records[$key]; ?></td>
					<?php
				}
				else{
					?>
					<td><?php echo $records['review_user_name']; ?></td>
					<?php
				}
				?>
				</tr>
				<?php
				break;
			case 'review_active':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getReviewStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'reported':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo !empty($records['abreport_id'])?'Yes':''?></td>
				</tr>
				<?php
				break;
			
			case 'abreport_user_comment':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo !empty($records['abreport_user_comment']) ? $records['abreport_user_comment'] : 'No Comments'?></td>
				</tr>
				<?php
				break;
			
			case 'abreport_comments':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo !empty($records['abreport_comments']) ? $records['abreport_comments'] : 'No Comments'?></td>
				</tr>
				<?php
				break;
			
			case 'review_date':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo FatDate::format($records[$key], true)?></td>
				</tr>
				<?php
				break;
			default:
			?>
			<tr>
				<td><?php echo $value; ?></td>
				<td><?php echo $records[$key]?></td>
			</tr>
			<?php 
		}
	}
	?>
</table>