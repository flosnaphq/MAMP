<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$arrFlds = array(
		'n'=>'Sr.',
		'user_name'=>'Name',
		'credential_username'=>'Email',
		'user_phone'=>'Phone',
		'user_regdate'=>'Registered On',
		'credential_verified'=>'Verified',
);

if ($canVerify || $canEdit) {
	$arrFlds['action'] = 'Action';
}

$tbl = new HtmlElement('table', array('border'=>'1'));
$tr = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
	$tr->appendElement('th', array(), $val);
}

$n = $i = ($pageNumber - 1) * $pageSize + 1;
foreach ($data as $row) {
	$row['n'] = $n;
	$tr = $tbl->appendElement('tr');
	if ( 0 == $row['credential_active']) {
		$tr->addValueToAttribute('class', 'inactive');
	}
	foreach ($arrFlds as $fld=>$caption) {
		switch ($fld) {
			case 'user_regdate':
				$tr->appendElement('td', array(), FatDate::format($row[$fld], true, true, FatApp::getConfig('CONF_TIMEZONE', FatUtility::VAR_STRING, date_default_timezone_get())));
				break;
			case 'credential_verified':
				$tr->appendElement('td', array(), (($row[$fld] == 1)?'Yes':'No'));
				break;
			case 'action':
				$td = $tr->appendElement('td');
				if ($canVerify) {
					if ($row['credential_verified'] == 0) {
						$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'verifyUser(' . $row['user_id'] . ', 1)'), 'Verify');
					}
					else {
						$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'verifyUser(' . $row['user_id'] . ', 0)'), 'Unverify');
					}
					$td->appendElement ( 'plaintext', array (), ' ' );
				}
				
				if ( $canEdit ) {
					if ($row['credential_active'] == 0) {
						$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'activateUser(' . $row['user_id'] . ', 1)'), 'Activate');
					}
					else {
						$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'activateUser(' . $row['user_id'] . ', 0)'), 'Deactivate');
					}
				}
				break;
			default:
				$tr->appendElement('td', array(), $row[$fld]);
				break;
		}
	}
	
	$n++;
}

if (count($data) == 0) {
	$tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arrFlds)), 'No Records Found');
}

echo $tbl->getHtml();

echo FatUtility::createHiddenFormFromData ( $postedData, array (
		'name' => 'frmUserSearchPaging' 
) ); // We need the form always to reload listing
if ($pageCount > 1) {
	echo FatUtility::getPageString(' <a href="javascript:void(0);" onclick="showUserSearchPage(xxpagexx);">xxpagexx</a>', 
			$pageCount, $pageNumber, $lnkcurrent = ' xxpagexx', ' ... ', 1, 
			' <a href="javascript:void(0);" onclick="showUserSearchPage(xxpagexx);">First</a>', 
			' <a href="javascript:void(0);" onclick="showUserSearchPage(xxpagexx);">Last</a>', 
			' <a href="javascript:void(0);" onclick="showUserSearchPage(xxpagexx);">Pre</a>', 
			' <a href="javascript:void(0);" onclick="showUserSearchPage(xxpagexx);">Next</a>');
}