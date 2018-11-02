<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('action', FatUtility::generateUrl('guest-user', 'forgot-password'));
$frm->setFormTagAttribute('id', 'forgotPassword');
$frm->setValidatorJsObjectName('forgotValidator');
$frm->setFormTagAttribute('onsubmit', 'submitForm(forgotValidator,this); return false;');
$user_email = $frm->getField('user_email');
$user_email->developerTags['noCaptionTag'] = true;
$security_code = $frm->getField('security_code');
$security_code->developerTags['noCaptionTag'] = true;
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'form form--vertical form--theme');
?>
<main id="MAIN" class="site-main site-main--dark">
    <div class="site-main__body">
        <div class="section section--vcenter no--margin section__forgot">
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--6 span--center text--center"  style="max-width:500px">

                            <?php
                            echo $frm->getFormHtml();
                            /*  echo $frm->getFormTag();?>
                              <div class="form-element">
                              <div class="form-element__control">
                              <?php echo $frm->getFieldHtml('useremail');?>
                              <label class="form-element__label"><?php echo Info::t_lang('EMAIL_ADDRESS')?></label>
                              </div>
                              </div>

                              <?php echo $frm->getFieldHtml('btn_submit');?>
                              <?php echo $frm->getExternalJs(); */
                            ?>

                        </div>
                    </div>
                </div>
            </div>
            <p class="regular-text disclaimer"><?php echo sprintf(Info::t_lang('BY_PROCEEDING,_YOU_AGREE_TO_%s'), FatApp::getConfig('conf_website_name')) ?> <a href="<?php echo FatUtility::generateUrl('cms', 'terms', array('privacy')) ?>" class="link text--primary"> <?php echo Info::t_lang('PRIVACY_POLICY') ?> </a> <?php echo Info::t_lang("AND") ?> <a href="<?php echo FatUtility::generateUrl('cms', 'terms') ?>" class="link text--primary"> <?php echo Info::t_lang('TERMS_OF_USE') ?></a>.</p>
        </div>
    </div>
</main>