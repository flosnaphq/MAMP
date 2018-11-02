<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('action', FatApp::getConfig('CONF_MAILCHIMP_NEWS_LETTER_URL', FatUtility::VAR_STRING, ''));
$frm->setFormTagAttribute('id', 'mc-embedded-subscribe-form');
$frm->setFormTagAttribute('class', 'newsletter__form');
$frm->setFormTagAttribute('method', 'post');

$fld = $frm->getField('EMAIL');

$fld->developerTags['col'] = ((isset($params['fieldcols']) && $params['fieldcols'] > 0) ? $params['fieldcols'] : 9);

$fld->addFieldTagAttribute('title', Info::t_lang('EMAIL_ADDRESS'));
$fld->addFieldTagAttribute('id', 'mce-EMAIL');
$fld->addFieldTagAttribute('class', 'mcfat-email');
$fld->addFieldTagAttribute('placeholder',Info::t_lang("ENTER_YOUR_EMAIL_ADDRESS"));

$btnfld = $frm->getField('btn_submit');
$btnfld->addFieldTagAttribute('class', 'button button--fill button--primary');
$btnfld->addFieldTagAttribute('id', 'mc-embedded-subscribe');

$fld->attachField($btnfld);

$fld->htmlAfterField = '<script type=\'text/javascript\' src=\'//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js\'></script><script type=\'text/javascript\'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]=\'EMAIL\';ftypes[0]=\'email\';fnames[1]=\'FNAME\';ftypes[1]=\'text\';fnames[2]=\'LNAME\';ftypes[2]=\'text\';}(jQuery));var  $mcj = jQuery.noConflict(true);</script>';
?>

<div class="span__row">
	<div class="span span--6 span--center newsletter">
		<h5 class="heading-text heading-text--small text--center">
			<?php echo ((isset($params['mctitle']) && $params['mctitle'] != '') ? $params['mctitle'] : Info::t_lang('Subscribe_now_to_get_recent_updates'));?>
		</h5>
		<?php echo $frm->getFormHtml();?>
	</div>
</div>