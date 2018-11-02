<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
$fields = array(
		'packageopt_price'=>'Price',
		'packageopt_days'=>'Days',
		'packageopt_active'=>'Active',
		);

foreach($records as $data){
	foreach($fields as $key=>$value){
		$tr = $tbl->appendElement('tr');
		switch($key){
			case 'packageopt_active':
				$tr->appendElement('td','','Active',true);
				$tr->appendElement('td','',Info::getStatusByKey($data[$key]),true);
				break;
			default:
				$tr = $tbl->appendElement('tr');
				$tr->appendElement('td','',$value,true);
				$tr->appendElement('td','',html_entity_decode($data[$key]),true);
		}
		
		
	}
}
echo $tbl->getHtml();

