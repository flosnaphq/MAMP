<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
    <!-- Wrapper -->

        <main id="MAIN" class="site-main site-main--dark">
            <header class="site-main__header site-main__header--light">
               <div class="site-main__header__content">
                    <div class="section section--vcenter">
                        <div class="container container--static">
                            <h5 class="special-heading-text text--center"><?php echo Info::t_lang('FREQUENTLY_ASKED_QUESTION')?></h5>
                            <h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('GET_YOUR_ALL_ANSWERS')?></h6>
                        </div>
                    </div>
                </div>
            </header>
			
			
            <div class="site-main__body">
                <section class="section faq__section" id="founder">
                     <div class="section__body">
                         <div class="container container--static">
                             <div class="span__row">
									<div class="span span--10 span--center">
									
									<?php if(!empty($faqs) && !empty($faqCategories)){ 
										foreach($faqCategories as $faqCategory){
											if(isset($faqs[$faqCategory['faqcat_id']])){
												?>
												<div class="faq__list">
												 <div class="faq__category">
													<h5 class="faq__category__heading"><?php echo $faqCategory['faqcat_name']?></h5>
													<div class="faq__question__list">
													<?php
													$catFaqs = $faqs[$faqCategory['faqcat_id']];
													foreach($catFaqs as $faq){
														?>
														<div class="faq__question__item js-faq">
														 <button class="toggle"></button>
														 <h6 class="question"><?php echo $faq['faq_question']?></h6>
														 <div class="answer">
														 <div class="innova-editor">
														 <?php echo html_entity_decode($faq['faq_answer']);?>
														 </div>
														 </div>
													 </div>
														<?php
													}
													?>
													</div>
												</div>
												</div>
												<?php
											}
										}
										?>
										
									<?php } ?>
									<div class="block gallery__block gallery gallery--1">
									<?php if(!empty($video_url)){ 
										$video_data = Info::getVideoDetail($video_url);
									?>
									<?php if($video_data['video_type'] == 2){?>
										<div class="gallery__item gallery__video-iframe">
											<iframe src="https://player.vimeo.com/video/<?php echo $video_data['video_id']?>" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
										</div>
									<?php } else { ?>	
										<div class="gallery__item gallery__video-iframe">
											<iframe src="https://www.youtube.com/embed/<?php echo $video_data['video_id']?>" width="100%" height="100%" frameborder="0"  allowfullscreen></iframe>
										</div>	
									<?php } ?>
									<?php } ?>
									
									<?php if(!empty($help_image)){ ?>
									<div class="gallery__item">
										<img src="<?php echo FatUtility::generateUrl('image','hostHelp',array(1400,700),'/')?>">
										</div>
									<?php } ?>
									
                                 </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </section>
            </div>
        </main>