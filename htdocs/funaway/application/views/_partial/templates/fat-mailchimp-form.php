<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
?>
<div class="span__row">
	<div class="span span--6 span--center newsletter">
		<h5 class="heading-text heading-text--small text--center">
			<?php echo ((isset($params['mctitle']) && $params['mctitle'] != '') ? $params['mctitle'] : Info::t_lang('Subscribe_now_to_get_recent_updates'));?>
		</h5>
		<?php echo FatApp::getConfig('CONF_MAILCHIMP_NEWS_LETTER_URL', FatUtility::VAR_STRING, '');?>
	</div>
</div>