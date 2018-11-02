<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$lang_fields = array(
		'discount_description'=>'Description'
		);
$fields = array(
		'discoupon_code'=>'Code',
		'lang_fields'=>'lang_fields',
		'discoupon_type'=>'Coupon',
		'discoupon_discount_type'=>'Discount Type',
		'discoupon_limit'=>'Coupon limit',
		'discoupon_discount'=>'Discount',
		'discoupon_min_order'=>'Minimum Order',
		'discoupon_max_discount'=>'Max Discount',
		'discoupon_weekday_specific'=>'Weekday Specific',
		'discoupon_valid_from'=>'Valid From',
		'discoupon_valid_upto'=>'Valid Upto',
		'discoupon_active'=>'Status',
		
	);


?>
<table width='100%' class='table table-responsive'>
	<?php 
	foreach($fields as $key=>$value){
		switch($key){
			case 'lang_fields';
				foreach($lang_fields as $lang_field_name=>$lang_field_caption){
					foreach($languages as $language){
						?>
						<tr>
							<td><?php echo $lang_field_caption.'['.$language['language_name'].']'; ?></td>
							<td><?php if(isset($records[$lang_field_name][$language['language_id']])) echo html_entity_decode($records[$lang_field_name][$language['language_id']]);?></td>
						</tr>
						<?php
					}
				}
				break;
			case 'discoupon_active':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo Info::getStatusByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'discoupon_discount_type':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo Info::getCouponDiscountTypeByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'discoupon_weekday_specific':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo Info::getYesNoByKey($records[$key])?></td>
				</tr>
				<?php
				break;
		
			case 'discoupon_type':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo Info::getCouponTypeByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'discoupon_use':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo Info::getCouponUseTypeByKey($records[$key])?></td>
				</tr>
				<?php
				break;
			case 'discoupon_valid_from':
			case 'discoupon_valid_upto':
				?>
				<tr>
					<td><?php echo $value?></td>
					<td><?php if(isset($records[$key])) echo FatDate::format($records[$key],true)?></td>
				</tr>
				<?php
				break;
			default:
			?>
			<tr>
				<td><?php echo $value; ?></td>
				<td><?php if(isset($records[$key])) echo html_entity_decode($records[$key])?></td>
			</tr>
			<?php 
		}
	}
	?>
</table>