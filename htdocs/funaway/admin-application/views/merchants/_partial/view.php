<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$fields = array(
		'photo'=>'Photo',
		'user_name'=>'Name',
		'user_email'=>'Email',
		'udetails_phone'=>'Phone Number',
		'udetails_zip'=>'Zipcode/Pincode',
		//'user_is_merchant'=>'User Type',
		'user_registered'=>'Reg. Date',
		'udetails_sex'=>'Gender',
		'address'=>'Address',
		'udetails_dob'=>'DOB',
		'user_active'=>'Status',
		'user_verified'=>'Verified',
		'user_confirmed'=>'Confirm',
		'udetails_longitude'=>'Longitude',
		'udetails_latitude'=>'Latitude',
		
		);


?>
<table width='100%' class='table table-responsive'>
	<?php 
	foreach($fields as $key=>$value){
		switch($key){
			case 'photo';
				?>
				<tr>
					<td><?php echo $value; ?></td>
					<td><img src="<?php echo FatUtility::generateUrl('users', 'photo',array($records['user_id'],100,100,rand(1111,99999)))?>"></td>
				</tr>
				<?php
				break;
			case 'user_is_merchant':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getUserTypeByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'user_confirmed':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getUserConfirmByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'user_active':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getSearchUserStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'user_verified':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getEmailStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'user_registered':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo FatDate::format($records[$key],true)?></td>
				</tr>
				<?php
				break;
			case 'udetails_sex':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php echo Info::getSexValue($records[$key])?></td>
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