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
                             
                                 </div>
                             </div>
                         </div>
                    </div>
                </section>
            </div>
        </main>