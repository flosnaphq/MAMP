<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="menu-bar">
	<nav class="fl--left" role="navigation">
	  <p id="bread-crumb-label" class="assistive__text"><?php echo Info::t_lang('YOU_ARE_HERE')?>:</p>
	  <?php if(isset($breadcrumb)) echo html_entity_decode($breadcrumb); ?>
	</nav>
	
	<nav class="menu fl--right" role="navigation">
		<ul class="list list--horizontal">
			<li><a href="<?php echo FatUtility::generateUrl('traveler','order')?>" <?php if(isset($action) && ($action == 'order' || $action == 'detail')){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('MY_BOOKINGS')?></a></li>
		
			<li><a href="<?php echo FatUtility::generateUrl('traveler','booking-cancel-requests')?>" <?php if(isset($action) && $action == 'bookingCancelRequests'){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('CANCELLATIONS')?></a></li>
			
			
		</ul>
	</nav>
</div>