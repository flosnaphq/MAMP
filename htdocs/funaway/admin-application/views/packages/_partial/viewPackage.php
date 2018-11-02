<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
$fields = array(
		'package_name'=>'Package Name',
		'package_sub_title'=>'Sub Title',
		'package_description'=>'Description',
		);

foreach($fields as $field=>$value){
	foreach($records[$field]  as $lang_id=>$field_data){
		$tr = $tbl->appendElement('tr');
		$tr->appendElement('td','',$value.' ['.$languages[$lang_id]['language_name'].']',true);
		$tr->appendElement('td','',html_entity_decode($field_data),true);
	}
}


if(isset($records['package_active'])){
	$tr = $tbl->appendElement('tr');
	$tr->appendElement('td','','Active',true);
	$tr->appendElement('td','',Info::getStatusByKey($records['package_active']),true);
}
echo $tbl->getHtml();

