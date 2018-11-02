<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
if(empty($records)){
	die('Invalid request');
}
$fields = array(
		'adrequest_name'=>'Name',
		'adrequest_email'=>'Email',
		'adrequest_phone'=>'Phone',
		'adrequest_message'=>'Message',
		'adrequest_date'=>'Date',
		'adrequest_active'=>'Status',
		);
$status = array(0=>'Inactive',1=>'Active');


?>
<table width='100%' class='table table-responsive'>
	<?php 
	
	foreach($fields as $key=>$value){
			switch($key){
				case 'adrequest_date':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo FatDate::format($records['adrequest_date'],true);?></td>
					</tr>
					<?php
					break;
				case 'adrequest_active':
					?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo $status[$records['adrequest_active']];?></td>
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