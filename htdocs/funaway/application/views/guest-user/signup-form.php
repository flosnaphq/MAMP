<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--vertical form--theme');
$frm->setFormTagAttribute('id', 'frmRegister');
$frm->setValidatorJsObjectName('signupValidator');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('guest-user', 'register'));
$frm->setFormTagAttribute('onsubmit', 'signup(signupValidator); return(false);');
?>
<main id="MAIN" class="site-main site-main--dark">
    <div class="site-content">
        <div class="site-main__body">
            <div class="section section--vcenter no--margin section__join">
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--8 span--center text--center">
                                <h6 class="heading-text heading-text--medium"><?php echo sprintf(Info::t_lang("WELCOME_TO_%s"), FatApp::getConfig('conf_website_name')) ?></h6>
                                <p class="sub-heading-text"><?php echo Info::t_lang('KINDLY_SETUP_YOUR_PROFILE_FIRST') ?></p>
                                <?php echo $frm->getFormTag() ?>
                                <div class="cotainer container--fluid">
                                    <div class="span__row">                                        
                                        <div class="span span--12">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_type"); ?>                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_firstname"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('FIRST_NAME') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_lastname"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('LAST_NAME') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span__row">
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_email"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('EMAIL_ADDRESS') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_country_id"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('COUNTRY') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span__row">
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_password"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('PASSWORD') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("password1"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('CONFIRM_PASSWORD') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span__row">
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php echo $frm->getFieldHTML("user_phone"); ?>
                                                    <label class="form-element__label"><?php echo Info::t_lang('PHONE') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span span--6">
                                            <div class="form-element">
                                                <div class="form-element__control">
                                                    <?php
                                                    $captchaSiteKey = FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
                                                    if ($captchaSiteKey != '') {
                                                        echo '<div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>';
                                                    }
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                /* if (empty($_SESSION['login_as']) || $_SESSION['login_as'] != 'traveler') {
                                  echo $frm->getFieldHTML("host_signup");
                                  } */


                                echo $frm->getFieldHTML("btn_submit");
                                ?>
                                <?php echo $frm->getExternalJs(); ?>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>

                <p class="regular-text disclaimer">
                    <?php printf(Info::t_lang("NEW_USER_%s"), '<a href="' . Route::getRoute("guest-user", "loginForm") . '" class="link text--primary">' . Info::t_lang("GO_TO_LOGIN") . '</a>'); ?> | 
                    <?php echo sprintf(Info::t_lang('BY_PROCEEDING,_YOU_AGREE_TO_%s'), FatApp::getConfig('conf_website_name')) ?> <a href="<?php echo Route::getRoute('cms', 'terms', array('privacy')) ?>" class="link text--primary"> <?php echo Info::t_lang('PRIVACY_POLICY') ?> </a><?php echo Info::t_lang("AND") ?><a href="<?php echo Route::getRoute('cms', 'terms', array('terms-of-service')) ?>" class="link text--primary"> <?php echo Info::t_lang('TERMS_OF_USE') ?></a>.

                </p>
            </div>
        </div>
    </div>
</main>	
<script>
    $(document).ready(function () {
        $("#user_country_id").trigger('change');
    });
<?php echo TrackingCode::getTrackingCode(8); ?>
    function facebookSignUpSuccessTracker() {
<?php echo TrackingCode::getTrackingCode(9); ?>
    }
</script>
<style>
    #country_code {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1;
        width: 35px;
        height: 35px;
        line-height: 35px;
        text-align: center;
    }
</style>