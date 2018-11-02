<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$fields = array(
		'notificationlang_text'=>Info::t_lang('Text'),
		
		
		);


?>
<table width='100%' class='table table-responsive'>
	<tr>
		<td><?php echo Info::t_lang('Template Name');?></td>
		<td><?php echo $records['tpl_name']?></td>
	</tr>
	<?php 
	foreach($fields as $field=>$value){
		foreach($languages as $language){
			?>
			<tr>
				<td><?php echo $value.'['.Info::t_lang($language['language_name']).']';?></td>
				<td><?php echo $records[$field][$language['language_id']]?></td>
			</tr>
			<?php
		}
	}
	?>
</table>