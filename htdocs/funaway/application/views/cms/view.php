<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

?>
<main id="MAIN" class="site-main site-main--light">
	<header class="site-main__header <?php if(isset($cmsData['cms_show_banner']) && $cmsData['cms_show_banner'] == 1) echo 'site-main__header--dark';?> main-carousel__item">
		<!-- hide -->
		<?php if(isset($cmsData['cms_show_banner']) && $cmsData['cms_show_banner'] == 1){ ?>
		<div class="site-main__header__image">
			<div class="img"><img src="<?php echo FatUtility::generateUrl('image','cms-image',array($cmsData['cms_id']))?>" alt=""></div>
		</div>
		<?php } ?>
		<!-- hide -->
		<div class="site-main__header__content">
			<div class="section section--vcenter">
				<div class="section__body">
					<div class="container container--static">
						<div class="span__row">
							<div class="span span--10 span--center text--center">
								<hgroup style="margin-bottom:2em;">
									<?php if(!empty($cmsData['cms_name'])){ ?>
									<h5 class="main-carousel__special-heading"><?php echo $cmsData['cms_name']; ?></h5>
									<?php } ?>
									<?php if(!empty($cmsData['cms_sub_heading'])){ ?>
									<h6 class="main-carousel__sub-heading"><?php echo $cmsData['cms_sub_heading']; ?></h6>
									<?php } ?>
								</hgroup>
								<?php if(isset($cmsData['cms_show_banner']) && $cmsData['cms_show_banner'] == 1 && !empty($cmsData['cms_banner_content'])){ ?>
								<p class="main-carousel__regular"><?php echo $cmsData['cms_banner_content']; ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</header>
	<div class="site-main__body">
		<div class="section">
			<?php if(!empty($cmsData['cms_content'])){ ?>
				<div class="innova-editor">
					<div class="container container--static">
						<?php echo html_entity_decode($cmsData['cms_content']); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</main>