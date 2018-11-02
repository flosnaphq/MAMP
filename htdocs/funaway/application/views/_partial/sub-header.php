<div class="menu-bar">
	<nav role="navigation" class="fl--left">
	  <p class="assistive__text" id="bread-crumb-label"><?php echo INFO::t_lang('YOU_ARE_HERE:');?></p>
	  <?php echo html_entity_decode($breadcrumb);?>
	</nav>
	<nav role="navigation" class="menu fl--right">
		
		<ul class="list list--horizontal">
			<?php if(in_array($controller,array('message','notification','review'))){ ?>
			<li>
				<a <?php if($controller == 'notification') echo 'class="active"'?>  href="<?php echo FatUtility::generateUrl('notification');?>">
					<?php echo Info::t_lang('NOTIFICATIONS')?>
				</a>
			</li>
			<li>
				<a <?php if($controller == 'message') echo 'class="active"'?> href="<?php echo FatUtility::generateUrl('message');?>">	
					<?php echo Info::t_lang('MESSAGES')?>
				</a>
			</li>
			<li>
				<a <?php if($controller == 'review') echo 'class="active"'?> href="<?php echo FatUtility::generateUrl('review');?>">
					<?php echo Info::t_lang('REVIEWS')?>
				</a>
			</li>
			<?php } ?>
		</ul>
	</nav>
</div>