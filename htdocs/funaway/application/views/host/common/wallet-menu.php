<?php

defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="menu-bar">
	<nav class="fl--left" role="navigation">
	  <p id="bread-crumb-label" class="assistive__text"><?php echo Info::t_lang('YOU_ARE_HERE')?>:</p>
	  <?php  if(isset($breadcrumb)) echo html_entity_decode($breadcrumb); ?>
	</nav>
	<nav class="menu fl--right" role="navigation">
		<ul class="list list--horizontal">
			<li>
				<a href="<?php echo FatUtility::generateUrl('host','history')?>" <?php if(isset($action) && ( $action == 'history' || $action == 'bookingDetail')){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('MY_WALLET')?></a>
			</li>
			<li><a href="<?php echo FatUtility::generateUrl('host','withdrawalRequests')?>" <?php if(isset($action) && ($action == 'withdrawalRequests' || $action == 'addWithdrawalRequest'|| $action == 'withdrawalRequestDetails')){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('REQUEST_WITHDRAWAL')?></a></li>
		</ul>
	</nav>
</div>