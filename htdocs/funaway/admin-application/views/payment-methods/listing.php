<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');

$arrFlds = array(
    'n' => 'Sr.',
    'pmethod_name' => 'Name',
    'pmethod_active' => 'Status',
);

if ($canEdit) {
    $arrFlds['action'] = 'Action';
}

$tbl = new HtmlElement('table', array(
    'width' => '100%',
    'class' => 'table table-responsive',
    'id' => 'payment-methods-list'
        )
);
$tr = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $tr->appendElement('th', array(), $val);
}

$n = $i = ($pageNumber - 1) * $pageSize + 1;
foreach ($data as $row) {

    $row['n'] = $n;
    $tr = $tbl->appendElement('tr');
    if (0 == $row['pmethod_active']) {
        $tr->addValueToAttribute('class', 'inactive');
    }
    foreach ($arrFlds as $fld => $caption) {

        if ($row['pmethod_id'] == 5) { // Hide sage pay payment gateway 
            continue;
        }
        switch ($fld) {
            case 'pmethod_name':
                $tr->appendElement('td', array(), $row[$fld]);
                break;
            case 'pmethod_active':

                if ($canEdit) {
                    $td = $tr->appendElement('td');
                    $tdstatus = $td->appendElement(
                            'label', array(
                        'data-status' => intval($row['pmethod_active']),
                        'class' => "statustab" . (intval($row['pmethod_active']) == 1 ? '' : ' active '),
                        'title' => 'Change Status',
                        'onClick' => 'changePaymentMethodStatus(' . $row['pmethod_id'] . ', this); return false;'
                            )
                    );
                    $tdstatus->appendElement(
                            'span', array(
                        'data-on' => 'Inactive',
                        'data-off' => 'Active',
                        'class' => 'switch-labels',
                            )
                    );
                    $tdstatus->appendElement('span', array('class' => 'switch-handles'));
                } else {
                    $tr->appendElement('td', array(), (($row[$fld] == 1) ? 'Active' : 'Inactive'));
                }

                break;
            case 'action':
                $td = $tr->appendElement('td');

                if ($canEdit) {
                    $actionul = $td->appendElement('ul', array('class' => 'actions'));

                    $actionli = $actionul->appendElement('li', array());
                    $actionli->appendElement(
                            'a', array(
                        'href' => FatUtility::generateUrl('PaymentMethods', 'form', array($row['pmethod_id']))
                            ), '<i class="ion-edit icon"></i>', true);

                    $settingsli = $actionul->appendElement('li', array());
                    $settingsli->appendElement(
                            'a', array('title' => 'Settings', 'href' => FatUtility::generateUrl(strtolower(str_replace("_", "", $row["pmethod_code"])) . "Settings")), '<i class="ion-ios-gear icon"></i>', true);
                }
                break;
            default:
                $tr->appendElement('td', array(), $row[$fld]);
                break;
        }
    }
    if ($row['pmethod_id'] != 5) { // Hide sage pay payment gateway
        $n++;
    }
}

if (count($data) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arrFlds)), 'No Records Found');
}

$tbl->appendElement('tr')->appendElement(
        'td', array('colspan' => count($arrFlds)), FatUtility::createHiddenFormFromData(
                $postedData, array('name' => 'frmPaymentMethodSearchPaging')
        ), true
);

echo $tbl->getHtml();

if ($pageCount > 1) {
    $pageLinks = new HtmlElement(
            'ul', array("class" => "pagination"), $pagination, //Generating pagination links
            true
    );
    echo $pageLinks->getHtml();
}