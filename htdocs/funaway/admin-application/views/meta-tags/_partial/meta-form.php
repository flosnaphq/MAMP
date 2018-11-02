<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['fld_default_col'] = 12;
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('MetaTags', 'metaTagAction'));
$frm->setFormTagAttribute('onsubmit', "submitForm(metatag_validator); return false;");
$frm->setFormTagAttribute('id', 'meta_tag');
?>
<section class="section">
    <div class="sectionhead">
        <h4>Add/Update </h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
    </div>
    <div class="sectionbody space"><?php echo $frm->getFormHtml(); ?></div>
</section>						