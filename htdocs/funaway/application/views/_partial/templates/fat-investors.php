<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section <?php echo $cls;?>" id="<?php echo $id; ?>">
	<div class="section__header">
		 <div class="container container--static">
			 <div class="span__row">
				 <div class="span span--10 span--center">
					<hgroup>
						<h5 class="heading-text text--center"><?php echo Info::t_lang('OUR_INVESTORS');?></h5>
						<h6 class="sub-heading-text text--center text--green"><?php echo Info::t_lang('WHO_BELIEVE_US');?></h6>
					</hgroup>
				 </div>
			 </div>
		</div>   
	</div>
	<div class="section__body">
		<div class="container container--static">
			<div class="span__row">
				<div class="span span--12">
					<div class="js-carousel" data-slides="6">
					<?php 
						foreach($investors as $investor)
						{
						?>
						<div>
							<a href="<?php echo $investor['investor_link'];?>" target="_blank">
								<img src="<?php echo FatUtility::generateUrl('image', 'investor', array($investor['investor_id'],155,103));?>" alt="">
							</a>
						</div>
						<?php
						}	
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>