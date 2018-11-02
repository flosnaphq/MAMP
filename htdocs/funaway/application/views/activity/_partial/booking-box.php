<div class="book-card" id="ActivityBookingBlock">
    <div class="book-card__block active">
        <div class="book-card__block__header">
            <span><?php echo Info::t_lang('CHOOSE_YOUR_DATE') ?></span>
        </div>
        <div class="book-card__block__body no--padding-bottom">
            <div id = 'book-now-section'></div>
        </div>
    </div>
    <div class="book-card__block">	
        <div class="book-card__block__header">
            <span><?php echo Info::t_lang('ENTER_YOUR_DETAILS') ?></span>
        </div>
        <div class="book-card__block__body">
            <div class="book-card__participant">
                    <!--h6 class="book-card__participant__heading"><?php echo Info::t_lang('NO_OF_Members') ?></h6-->
                <label><?php echo Info::t_lang('NO_OF_') . ' Members' ?></label>
                <fieldset class="book-card__participant__form">
                    <a href="javascript:;" onclick="minusMem(1);" class="fl--left">-</a>
                    <a href="javascript:;" onclick="plusMem();"  class="fl--right">+</a>
                    <input id="memberCount" type="text" value="1" readonly>
                </fieldset>
            </div>
        </div>
    </div>
	<?php if (!empty($addons) ) { ?>
    <div class="book-card__block">
        <div class="book-card__block__header">
            <span><?php echo Info::t_lang('ADD_-ONS') ?></span>
        </div>
        <div class="book-card__block__body">
			<div class="book-card__addons">
				<!--h6 class="book-card__addons__heading"><?php echo Info::t_lang('Addons') ?></h6-->
				<fieldset class="book-card__addons__form">
					<ul class="list list--vertical text--left">
						<?php $i = 0;
						foreach ($addons as $add) {
						?>
							<li class="clearfix">
								<label class="selectbox" style="text-decoration: none;">
									<?php echo $add['activityaddon_text']; ?> (<?php echo Currency::displayPrice($add['activityaddon_price']); ?>)
									<a href="#inlinecont<?php echo $add['activityaddon_id'] ?>" class="modaal link fl--right"  data-modaal-type="inline"><?php echo Info::t_lang('Activity_Detail_Page_AddOn_Desc'); ?></a>	
								</label>
								<select class="addons-type"  name='addons[<?php echo $add['activityaddon_id']; ?>]' rel="<?php echo $add['activityaddon_id']; ?>">
									<?php for ($i = 0; $i <= $activity['activity_members_count']; $i++) { ?>
										<option value ='<?php echo $i; ?>'> <?php echo $i; ?></option>
									<?php } ?>
								</select>
								<div id="inlinecont<?php echo $add['activityaddon_id'] ?>" style="display:none;">
									<div class="modal share-card text--center">
										<div class="modal__header">
												<h6 class="modal__heading"><?php echo $add['activityaddon_text']; ?> - <?php echo Currency::displayPrice($add['activityaddon_price']); ?></h6>
												<p class="regular__text"><?php echo $add['activityaddon_comments']; ?></p>
										</div>
										<div class="modal__content share-card__image">
											<?php
											if (!empty($add['images'])) {
												$gallery_class = count($add['images']) > 2 ? 4 : 2;
											?>
												<ul class="list list--horizontal gallery gallery--<?php echo $gallery_class ?>">
													<?php 
													foreach ($add['images'] as $add_image) { ?>
														<li class="gallery__item">
															<img src="<?php echo FatUtility::generateUrl('image', 'addon', array($add_image['afile_id'], 600, 400)) ?>">
														</li>
													<?php } ?>
												</ul>
											<?php } ?>
										</div>
									</div>
								</div>		
							</li>
						<?php } ?>
					</ul>			
				</fieldset>
			</div>

        </div>
    </div>
    <?php } ?>
</div>
<div class="book-card text--center">
    <div class="book-card__block active">
        <div class="book-card__block__body">
            <div class="book-card__price priceOpt">
                <h6 class="book-card__price__heading"><?php echo Currency::displayPrice(0); ?></h6>
                <button id = "book-now" onclick="addInCart()" class="button button--large button--fit button--disabled button--fill button--red"><?php echo INFO::t_lang('ADD_TO_CART') ?></button>
				<p class="regular-text no--margin"><?php echo INFO::t_lang('INCLUSIVE ALL TAXES') ?></p>
            </div>
        </div>
    </div>
</div>