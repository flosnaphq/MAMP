<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php

$status =array(0=>'Inactive',1=>'Active',2=>'Deleted');

?>
<table width="100%" class="table table-responsive">
	<tr>
		<td>Username</td>
		<td><?php echo $records['admin_username'];?></td>
	</tr>
	<tr>
		<td>Full name</td>
		<td><?php echo $records['admin_full_name'];?></td>
	</tr>
	<tr>
		<td>Email</td>
		<td><?php echo $records['admin_email'];?></td>
	</tr>
	<tr>
		<td>Palette</td>
		<td><?php echo $records['palette'];?></td>
	</tr>
	<tr>
		<td>Is Super Admin</td>
		<td><?php echo $records['admin_is_super_admin']==1?'yes':'No';?></td>
	</tr>
	<tr>
		<td>Active</td>
		<td><?php echo $status[$records['admin_active']];?></td>
	</tr>
</table>