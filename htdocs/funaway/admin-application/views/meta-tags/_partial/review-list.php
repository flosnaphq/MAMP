<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php
$arr_flds = array(
    //'listserial'=>'Sr no.',
    //'photo'=>'Photo',
    'user_name' => 'Review By',
    'activity_name' => 'Activity Name',
    'review_rating' => 'Rating',
    'review_date' => 'Date',
    'reported' => 'Reported Inappropriate',
    'abreport_taken_care' => 'Inappropriate Status',
    'review_active' => 'Status',
    'action' => 'Action',
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');

foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['review_active'] == 0) {
        $tr->setAttribute("class", "inactive-tr");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            /* case 'review_rating':
              $td->appendElement('div', array(),Info::rating($row[$key]), true);

              break; */
            case 'user_name':
                if ($row['review_user_id']) {
                    $td->appendElement('plaintext', array(), $row[$key]);
                } else {
                    $td->appendElement('plaintext', array(), $row['review_user_name'] . "*");
                }
                break;
            case 'review_active':
                $td->appendElement('plaintext', array(), Info::getReviewStatusByKey($row[$key]));
                break;
            case 'abreport_taken_care':
                $st = Info::getAbuseReportStatusByKey($row[$key]);
                if ($row[$key] === null) {
                    $st = '--';
                }
                $td->appendElement('plaintext', array(), $st);
                break;
            case 'review_entity_type':
                $td->appendElement('plaintext', array(), Info::getReviewEntityTypeByKey($row[$key]));
                break;
            case 'review_date':
                $td->appendElement('plaintext', array(), FatDate::format($row['review_date'], true));
                break;
            case 'reported':
                $td->appendElement('plaintext', array(), !empty($row['abreport_id']) ? 'Yes' : 'No');
                break;


            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));

                if ($canView) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'Javascript:popupView("' . FatUtility::generateUrl('reviews', 'viewReview', array('review_id' => $row['review_id'])) . '");', 'class' => 'button small green', 'title' => 'View detail'), '<i class="ion-eye icon"></i>', true);
                }

                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Edit', "onclick" => "getReviewForm(" . $row['review_id'] . ")"), '<i class="ion-edit icon"></i>', true);
                }
                if ($canEdit && !empty($row['abreport_id'])) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Edit Abuse Report', "onclick" => "getAbuseForm(" . $row['abreport_id'] . ")"), '<i class="ion-android-notifications-none icon"></i>', true);
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
                                                  

