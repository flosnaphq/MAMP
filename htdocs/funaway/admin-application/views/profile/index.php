<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

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
                    $frmProf->setFormTagAttribute('action', FatUtility::generateUrl('profile', 'update'));
                    $frmProf->setFormTagAttribute('method', 'post');
                    $frmProf->setFormTagAttribute('id', 'frmProfFrm');
                    $frmProf->addFormTagAttribute('class', 'form_horizontal web_form');
                    $frmProf->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
                    $frmProf->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
                    $frmProf->developerTags['fld_default_col']=12;
                    $adminNameFld = $frmProf->getField('admin_name');
                    $adminNameFld->setFieldTagAttribute('id', 'admin_name');

                    $adminEmailFld = $frmProf->getField('admin_email');
                    $adminEmailFld->setFieldTagAttribute('id', 'admin_email');

                    $adminSubmitFld = $frmProf->getField('btn_submit');
                    $adminSubmitFld->setFieldTagAttribute('id', 'btn_submit');

                    
                    ?>
                    <div class="repeatedrow">
                        <h3><i class="ion-podium icon"></i>Profile Info</h3>
                        <?php echo $frmProf->getFormHtml(); ?>
                    </div>
          
                </div>
            </div>
        </aside>
    </div>

