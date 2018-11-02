<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <div class="detail_approval">
            <h3 class="name_usr"><?php
                echo Info::t_lang('HI') . ' ';
                echo $user_name;
                ?> ,</h3>
            <p><?php echo Info::t_lang('WE_HAVE_SENT_VERIFICATION_LINK_TO_YOUR_EMAIL'); ?>,<br/><?php echo Info::t_lang('PLEASE_VERIFY_YOUR_EMAIL_ADDRESS'); ?></p>
            <p> <a href="javascript:;" onclick="resendVerification()"><?php echo Info::t_lang('CLICK_HERE') ?></a> <?php echo Info::t_lang('TO_RESEND_VERIFICATION_EMAIL') ?></p>
            <!--<div class="applicatin-no">
                    <h2><span>Application Refference :</span>#GT405TRD120W</h2>
            </div>-->
        </div>
    </div>
</main>