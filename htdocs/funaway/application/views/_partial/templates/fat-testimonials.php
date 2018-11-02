<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section <?php echo $cls;?>" id="<?php echo $id; ?>">
	<div class="section__header">
		 <div class="container container--static">
			 <div class="span__row">
				 <div class="span span--10 span--center">
					 <hgroup>
						 <h5 class="heading-text text--center"><?php echo Info::t_lang('AS_SEEN_ON');?></h5>
					 </hgroup>
				 </div>
			 </div>
		</div>   
	</div>
	<div class="section__body">
		<div class="container container--static">
			<div class="span__row js-carousel" data-slides="2">
			<?php 
				foreach($testimonials as $testimonial)
				{
			?>
					<div class="span span--6">
						<div class="media testimonial__item">
							<div class="media__figure media--left">
								<div class="testimonial__image"><img alt="' . $testimonial[Testimonial::DB_TBL_PREFIX.'name'] . '" title="' . $testimonial[Testimonial::DB_TBL_PREFIX.'name'] . '" src="<?php echo FatUtility::generateUrl('image','testimonial',array($testimonial[Testimonial::DB_TBL_PREFIX.'id'],100,100)); ?>"></div>
							</div>
							<div class="media__body testimonial__content">
								<h6 class="testimonial__heading"><?php echo $testimonial[Testimonial::DB_TBL_PREFIX.'name']; ?></h6>
								<p class="testimonial__text"><?php echo $testimonial[Testimonial::DB_TBL_PREFIX.'content']; ?></p>
							</div>     
						</div>
					</div>
			<?php 
				}
			?>
			</div>
		</div>
	 </div>
</section>