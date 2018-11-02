<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section <?php echo $cls;?>" id="<?php echo $id; ?>">
	<div class="section__header">
		<div class="container container--static">
			<div class="span__row">
				<div class="span span--10 span--center">
					<hgroup>
						<h5 class="heading-text text--center"><?php echo Info::t_lang("VISIT_HEADQUARTERS"); ?></h5>
					</hgroup>
				</div>
			</div>
		</div>   
	</div>
	<div class="section__body">
		<div class="container container--static"> 
			<div class="span__row"> 
				<div class="span span--10 span--center"> 
					<article class="wcu"> 
						<div class="point__list wcu-point__list">
							<?php
							if(isset($offices[1]))
							{
								$office = $offices[1];
							?>
								<div class="media point__item">
									<div class="media__figure point__image">
										<div class="point__icon"><img alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/singapore.svg" /></div>
									</div>
									<div class="media__body media--middle point__content">
										<h6 class="point__heading"><?php echo $office['office_country'];?></h6>
										<ul class="list list--vertical">
											<li><?php echo nl2br($office['office_address']);?></li>   
										</ul>
									</div>
								</div>
							<?php
							} 
							if(isset($offices[2]))
							{
								$office = $offices[2];
							?>
								<div class="media point__item">
									<div class="media__figure point__image">
										<div class="point__icon"><img alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/singapore.svg" /></div>
									</div>
									<div class="media__body media--middle point__content">
										<h6 class="point__heading"><?php echo $office['office_country'];?></h6>
										<ul class="list list--vertical">
											<li><?php echo nl2br($office['office_address']);?></li>
										</ul>
									</div>
								</div>
							<?php
							}
							?>
						</div> 
					</article>
				</div> 
			</div> 
		</div> 
	</div>
</section>