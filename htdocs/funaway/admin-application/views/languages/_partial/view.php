<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>


<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Template Name</strong></td>
		<td><?php echo $records['tpl_name']?></td>
	</tr>
	<tr>
		<td><strong>Subject</strong></td>
		<td><?php echo $records['tpl_subject']?></td>
	</tr>
	<tr>
		<td><strong>Body</strong></td>
		<td><?php echo $records['tpl_body']?></td>
	</tr>
	
</table>