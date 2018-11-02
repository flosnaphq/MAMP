<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->developerTags['fld_default_col'] = 4;
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('action', FatUtility::generateUrl($handlerName, 'image-setup'));
$frm->setFormTagAttribute('id', 'islandImgFrm');
$frm->setFormTagAttribute('onsubmit', "jQuery.fn.submitImageForm('islandImgFrm'); return false;");
?>
<div class="sectionbody space"><i>(Inner Page Images: 1200X900	or [aspect ratio 4:3])</i></div>

<?php
$counter = 0;
$table = new HtmlElement('table', array('class' => 'table_form_horizontal threeCol', 'cellpadding' => 0, 'cellspacing' => 0, 'border' => 0, 'width' => '100%'));
$tr = $table->appendElement('tr');
if (!empty($images)) {

    foreach ($images as $image) {
        $counter ++;
        $td = $tr->appendElement('td');
        $logoWrap = $td->appendElement('div', array('class' => 'logoWrap'));
        $logothumb = $logoWrap->appendElement('div', array('class' => 'logothumb blackclr'));
        $img = $logothumb->appendElement('img', array('src' => FatUtility::generateUrl($handlerName, 'displayImage', array($image['afile_id'], 200, 400, rand(1, 1000)))));
        $logothumb->appendElement('a', array('href' => 'javascript:;', 'data-href' => FatUtility::generateUrl($handlerName, 'imageRemoveSetup'), 'onclick' => "jQuery.fn.removeImage(this,'Do You Want To Remove ?', '" . $image['afile_id'] . "')", 'class' => 'deleteLink white'), '<i class="ion-close-circled icon"></i>', true);
        $td->appendElement('label', array(), 'Display Order : ');
        $ar = array('value' => $image['afile_display_order'], 'onblur' => "jQuery.fn.changeDisplayOrder('" . $image['afile_id'] . "', this)", 'data-action' => FatUtility::generateUrl($handlerName, 'imageOrderSetup'));
        if (!$canEdit) {
            $ar['disabled'] = 'disabled';
        }

        $td->appendElement('input', $ar);
        if ($counter % 3 == 0) {
            $tr = $table->appendElement('tr');
        }
    }
}
echo $table->getHtml();
?>
<div class="sectionbody space"><?php echo $frm->getFormHtml(); ?></div>



