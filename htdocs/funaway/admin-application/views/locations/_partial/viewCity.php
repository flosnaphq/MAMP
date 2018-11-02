<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
$fields = array(
		'city_name'=>'City Name',
		);
$status =array(0=>'Inactive',1=>'Active',2=>'Deleted');
foreach($fields as $field=>$value){
	foreach($records[$field]  as $lang_id=>$field_data){
		$tr = $tbl->appendElement('tr');
		$tr->appendElement('td','',$value.' ['.$languages[$lang_id]['language_name'].']',true);
		$tr->appendElement('td','',html_entity_decode($field_data),true);
	}
}

if(isset($records['city_active'])){
	$tr = $tbl->appendElement('tr');
	$tr->appendElement('td','','Active',true);
	$tr->appendElement('td','',Info::getLocationStatusByKey($records['city_active']),true);
}
echo $tbl->getHtml();

