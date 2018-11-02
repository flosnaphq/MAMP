<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 


$commission_str = '<table class="commission-chart table table--fixed table--bordered table--responsive info-table "><thead>	<tr><th>'. Info::t_lang('LISTING_PRICE').'</th>				<th>'. Info::t_lang('SITE_FEE').'</th>	</tr></thead><tbody>';
				 foreach($commission_chart as $comm){ 
					$max_amount = ($comm['max_amount'] <= 0)?'>':$comm['max_amount'];
					$commission_str .='<tr><td data-label="'.Info::t_lang('LISTING_PRICE').'">'.$comm['min_amount'].'   <span>'. Info::t_lang('-').'</span> '. Currency::displayPrice($max_amount).'</td><td data-label="'.Info::t_lang('SITE_FEE').'">'.Currency::displayPrice($comm['commission_rate']).'%</td></tr>';
				 }
				$commission_str .='</tbody></table>';
				
$payout_terms['block_content'] = str_replace('{commission_chart}',$commission_str, $payout_terms['block_content']);
?>
<?php if(!empty($payout_terms)) echo html_entity_decode($payout_terms['block_content'])?>