<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section <?php echo $cls;?> " id="<?php echo $id;?>">
	 <div class="section__header">
		 <div class="container container--static">
			 <div class="span__row">
				 <div class="span span--10 span--center">
					 <hgroup>
						 <h5 class="heading-text text--center"><?php echo Info::t_lang('MEET_THE_FOUNDERS');?></h5>
						 <h6 class="sub-heading-text text--center text--green"><?php echo Info::t_lang('WHO_MADE_IDEA_REAL'); ?></h6>
					 </hgroup>
				 </div>
			 </div>
		</div> 
	</div>
	<div class="section__body">
		<div class="container container--static">
			<div class="span__row">
				<div class="span span--10 span--center">
					<div class="founder__list">
						<?php 
						foreach($founders as $founder)
						{
						?>
							<div class="media founder__item">
								<div class="media__figure media--left founder__image">
									<img src="<?php echo FatUtility::generateUrl('image','founder',array($founder['founder_id'],480,480));?>" alt="">
								</div>
								<div class="media__body founder__content">
									<h6 class="founder__name"><?php echo $founder['founder_name'];?></h6>
									<span class="founder__desi"><?php echo $founder['founder_designation'];?></span>
									<div class="founder__desc">
										<div class="innova-editor">
											<?php echo html_entity_decode($founder['founder_content']); ?>
										</div>
									</div>
								</div>
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

