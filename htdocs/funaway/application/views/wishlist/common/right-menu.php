<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
 <div class="menu-bar">
 <nav class="fl--left" role="navigation">
	  <p id="bread-crumb-label" class="assistive__text">You are here:</p>
	  <?php echo html_entity_decode($breadcrumb); ?>
	</nav>
	<?php /*?>
 <nav class="menu fl--right" role="navigation">
	<ul class="list list--horizontal">
		
		<li><a href="<?php echo FatUtility::generateUrl('wishlist')?>" <?php if(isset($action) && $action == 'index' && isset($controller) && $controller == 'wishlist'){ ?>class="active" <?php } ?>><?php echo Info::t_lang('WISHLIST')?></a></li>
		
	</ul>
</nav> <?php */?>
</div>