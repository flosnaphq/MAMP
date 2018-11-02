<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
    <div class="sectionhead">
        <h4>Edit Profile </h4>
    </div>
    <div class="sectionbody space">
        <?php
        $frm->setValidatorJsObjectName('formValidator');
        $frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);

        $frm->setFormTagAttribute('action', FatUtility::generateUrl("users", "updateDetail"));
        $frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator,"action_form"); return(false);');
        $frm->developerTags['fld_default_col'] = 6;
        echo $frm->getFormHtml();
        ?>	
    </div>
</section>			


