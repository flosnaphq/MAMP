<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = array(
    'listserial' => 'Sr no.',
    'city_name' => 'Name',
    'activities' => 'Acivities',
    'city_display_order' => 'Display Order',
    'city_featured' => 'Featured',
    'city_active' => 'Status',
    'action' => 'Action',
);
if (!$canEdit) {
    unset($arr_flds['action']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');

foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if (array_key_exists('country_active', $row) && $row['country_active'] == 0) {
        $tr->setAttribute("class", "inactive-tr");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'city_featured':
                if ($canEdit) {
                    $active = $row[$key] != 1 ? 'active' : '';
                    $toggle = '<label class="statustab addmarg ' . $active . '" onclick="setFeatured(this, \'' . $row['city_id'] . '\')">
								  <span data-off="Mark" data-on="Unmark" class="switch-labels"></span>
								  <span class="switch-handles"></span>
								</label>';
                    $td->appendElement('plaintext', array(), $toggle, true);
                } else {
                    $td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
                }
                break;

            case 'city_display_order':
                if ($canEdit) {
                    $td->appendElement('input', array('value' => $row[$key], 'onblur' => 'changeOrder("' . $row['city_id'] . '",this)', 'class' => 'text-display-order'));
                } else {
                    $td->appendElement('plaintext', array(), $row[$key]);
                }
                break;
            case 'city_active':
                $td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => FatUtility::generateUrl('cities', 'setup', array($row['city_id'])), 'class' => 'button small green', 'title' => 'Edit'), '<i class="ion-edit icon"></i>', true);
                }

                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}

if (count($arr_listing) == 0)
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), 'No records found');

echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmUserSearchPaging', 'id' => 'pretend_search_form'
));
if ($totalPage > 1) {
    ?>
    <div class="footinfo">
        <aside class="grid_1">
            <ul class="pagination">
                <?php
                echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', $totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, '<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', ' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', '<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', '<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
                ?>
            </ul>
        </aside>  

    </div>
    <?php
}
?>


