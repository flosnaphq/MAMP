<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<table width='100%' class='table table-responsive'>
	<tr>
		<td><strong>Question</strong></td>
		<td><?php echo html_entity_decode($records['faq_question'])?></td>
	</tr>
	<tr>
		<td><strong>Answer</strong></td>
		<td><?php echo html_entity_decode($records['faq_answer'])?></td>
	</tr>
	<tr>
		<td><strong>Display Order</strong></td>
		<td><?php echo $records['faq_display_order']?></td>
	</tr>
	<tr>
		<td><strong>Active</strong></td>
		<td><?php echo Info::getStatusByKey($records['faq_active'])?></td>
	</tr>
</table>

