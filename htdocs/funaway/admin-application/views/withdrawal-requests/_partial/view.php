<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$fields = array(
	
		'user_name'=>'Name',
		'user_email'=>'Email',
		'withdrawalrequest_amount'=>'Phone Number',
		'withdrawalrequest_comment'=>'Comment',
		'withdrawalrequest_admin_comment'=>'Admin Comment',
		'withdrawalrequest_datetime'=>'Request Time',
		'withdrawalrequest_status'=>'Status',
		
		
		);


?>
<table width='100%' class='table table-responsive'>
	<?php 
	foreach($fields as $key=>$value){
		switch($key){
			
			case 'user_name':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo $records['user_firstname'].' '.$records['user_lastname']?></td>
				</tr>
				<?php
				break;
			case 'withdrawalrequest_amount':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Currency::displayPrice($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'withdrawalrequest_datetime':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo FatDate::format($records[$key],true)?></td>
				</tr>
				<?php
				break;
			case 'withdrawalrequest_status':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getWithdrawalRequestStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			
			
			default:
			?>
			<tr>
				<td><?php echo $value; ?></td>
				<td><?php echo html_entity_decode($records[$key])?></td>
			</tr>
			<?php 
		}
	}
	?>
</table>