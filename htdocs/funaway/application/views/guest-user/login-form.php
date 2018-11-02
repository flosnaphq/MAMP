<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<?php
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);

$frm->setFormTagAttribute('class', 'form form--vertical form--theme');
$frm->setValidatorJsObjectName('loginValidator');
$frm->setFormTagAttribute('onsubmit', 'login(this, loginValidator); return(false);');





defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<main id="MAIN" class="site-main site-main--dark">
    <div class="site-main__body">
        <div class="section section--vcenter no--margin section__login">
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--8 span--center text--center" style="max-width:500px;">
                            <h6 class="heading-text heading-text--medium"><?php echo Info::t_lang('WELCOME_BACK'); ?></h6>
                            <nav class="menu" role="navigation">
                                <ul class="list list--vertical">
                                    <li><a href="<?php echo FatUtility::generateUrl('facebook'); ?>"  class="button button--large button--icon button--fill button--fit button--facebook">
                                            <span><svg class="icon"><use xlink:href="#icon-facebook" /></svg></span>  
                                            <span class="hidden-on--mobile"><?php echo Info::t_lang('LOGIN_WITH') ?> </span><span><?php echo Info::t_lang('FACEBOOK'); ?></span></a></li>
                                    <li><a href="<?php echo FatUtility::generateUrl('google'); ?>"  class="button button--large button--icon button--fill button--fit button--google">   <span>
                                                <svg class="icon"><use xlink:href="#icon-google" /></svg></span>  
                                            <span class="hidden-on--mobile"><?php echo Info::t_lang('LOGIN_WITH') ?> </span><span><?php echo Info::t_lang('GOOGLE'); ?></span></a></li>

                                </ul>
                            </nav>
                            <label class="heading-text"><?php echo Info::t_lang('OR') ?></label>
                            <?php echo $frm->getFormTag(); ?>
                            <div class="cotainer container--fluid">
                                <div class="span__row">
                                    <div class="span span--12">
                                        <div class="form-element">
                                            <div class="form-element__control">
                                                <?php echo $frm->getFieldHtml("username"); ?>
                                                <label class="form-element__label"><?php echo Info::t_lang('EMAIL_ADDRESS'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span span--12">
                                        <div class="form-element">
                                            <div class="form-element__control">
                                                <?php echo $frm->getFieldHtml("password"); ?>
                                                <label class="form-element__label"><?php echo Info::t_lang('PASSWORD'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php echo $frm->getFieldHtml("btn_submit"); ?>

                                <?php echo $frm->getExternalJs(); ?>
                                <div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="regular-text disclaimer">
                        <?php printf(Info::t_lang("NEW_USER_%s"), '<a href="' . FatUtility::generateUrl("guest-user", "signupForm") . '" class="link text--primary">' . Info::t_lang("GO_TO_SIGNUP") . '</a>'); ?> | <a href="<?php echo FatUtility::generateUrl("guest-user", "forgot-form") ?>" class="link text--primary"><?php echo Info::t_lang("FORGOT_PASSWORD?"); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://apis.google.com/js/api:client.js" ></script>