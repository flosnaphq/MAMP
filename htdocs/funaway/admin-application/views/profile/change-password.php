<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="fixed_container">
    <div class="section">
        <div class="sectionhead">
            <h4>My Profile</h4> 
        </div>
    </div>
    <div class="containerwhite">
        <aside class="grid_1 profile">
            <?php include( CONF_THEME_PATH . 'profile/_partial/profile-left-pan.php') ?>
        </aside>  
        <aside class="grid_2">
            <?php include( CONF_THEME_PATH . 'profile/_partial/profile-tabs.php') ?>
            <div class="areabody">   
                <div class="formhorizontal">
                    <?php
                    $pwdFrm->setFormTagAttribute('action', FatUtility::generateUrl('profile', 'updatePassword'));
                    $pwdFrm->setFormTagAttribute('method', 'post');
                    $pwdFrm->setFormTagAttribute('id', 'getPwdFrm');
                    $pwdFrm->addFormTagAttribute('class', 'form_horizontal web_form');
                    $pwdFrm->setFormTagAttribute('autocomplete', 'off');
                    $pwdFrm->developerTags['fld_default_col'] =12;
                  
                    $pwdFrm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
                    $pwdFrm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);

                    $currentPassFld = $pwdFrm->getField('current_password');
                    $currentPassFld->setFieldTagAttribute('id', 'current_password');

                    $newPassFld = $pwdFrm->getField('new_password');
                    $newPassFld->setFieldTagAttribute('id', 'new_password');

                    $confNewPassFld = $pwdFrm->getField('conf_new_password');
                    $confNewPassFld->setFieldTagAttribute('id', 'conf_new_password');

                    $submitFld = $pwdFrm->getField('btn_submit');
                    $submitFld->setFieldTagAttribute('id', 'btn_submit');

                   
                    ?>
                    <div class="repeatedrow">
                        <h3><i class="ion-podium icon"></i>Change Password</h3>
                         <?php echo $pwdFrm->getFormHtml(); ?>
                    </div>
            
                </div>
            </div>
        </aside>
    </div>
</div>
