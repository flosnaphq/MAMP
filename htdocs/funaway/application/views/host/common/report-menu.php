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
			<li><a href="<?php echo FatUtility::generateUrl('host', 'request'); ?>" <?php if(isset($action) && ($action == 'request')){ ?>class="active" <?php } ?>><?php echo Info::t_lang('USER_MENU_MY_CONFIRMATION_REQUESTS'); ?></a></li>
			<li><a href="<?php echo FatUtility::generateUrl('host','bookings')?>" <?php if(isset($action) && ( $action == 'bookings' || $action == 'detail' )){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('MY_BOOKINGS')?></a></li>
			<li><a href="<?php echo FatUtility::generateUrl('host','bookingCancelRequests')?>" <?php if(isset($action) && ($action == 'bookingCancelRequests' || $action == 'bookingCancelDetail')){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('CANCELLATIONS')?></a></li>
			<li><a href="<?php echo FatUtility::generateUrl('hostReports')?>" <?php if(isset($action) && ($action == 'report' )){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('REPORTS')?></a></li>
		</ul>
	</nav>
</div>