<?php
defined('SYSTEM_INIT') or die('Invalid Usage');?> 
<main id="MAIN" class="site-main site-main--dark">
            <header class="site-main__header site-main__header--light">
               <div class="site-main__header__content">
                    <div class="section section--vcenter">
                        <div class="container container--static">
                            <!--<h5 class="special-heading-text text--center"><?php echo Info::t_lang('CANCELLATION_POLICY')?></h5>-->
                            <!--<h6 class="sub-heading-text text--center text--red">For Traveller</h6>-->
                            <nav class="menu text--center">
                                 <ul class="list list--horizontal">
                                     <li><a href="<?php echo FatUtility::generateUrl('cancellation-policy','traveler')?>" class="button button--fill <?php if($active_tab == 'traveler') echo 'button--blue'; else echo 'button--dark'; ?> button--small"><?php echo Info::t_lang('FOR_TRAVELER')?></a></li>
                                     <li><a href="<?php echo FatUtility::generateUrl('cancellation-policy','host')?>" class="button button--fill <?php if($active_tab == 'host') echo 'button--blue'; else echo 'button--dark'; ?>  button--small"><?php echo Info::t_lang('FOR_HOST')?></a></li>
                                 </ul>
                             </nav>
                        </div>
                    </div>
                </div>
            </header>
            <div class="site-main__body">
                <section class="section cancel__section no--padding-top">
                    <div class="cancellation__tab js-tab">
                        <div class="section__header menu-bar text--center">
                             <nav class="menu tab__nav">
                                 <ul class="list list--horizontal">
									<?php 
									$i=0;
									foreach($records as $record){
										$i++;
										?>
										<li><a href="#tab<?php echo $record[CancellationPolicy::DB_TBL_PREFIX.'id']?>" class="button button--fill <?php if($i == 1){ ?>current<?php } ?>"><?php echo $record[CancellationPolicy::DB_TBL_PREFIX.'name']?></a></li>
								   <?php } ?>
                                    
                                 </ul>
                             </nav>
                         </div>
                         <div class="section__body">
                             <div class="container container--static">
                                 <div class="span__row">
                                     <div class="span span--10 span--center block cancellation__block">
                                         <div class="tab__container">
											<?php 
											$i=0;
											foreach($records as $record){ 
												$i++;
											?>
												<div class="tab__content <?php if($i>1) echo 'hide';?>" id="tab<?php echo $record[CancellationPolicy::DB_TBL_PREFIX.'id']?>">
													<div class="innova-editor">
														<?php echo html_entity_decode($record[CancellationPolicy::DB_TBL_PREFIX.'content'])?>
													</div>
												 </div>
											<?php } ?>
											
                                             
                                         </div>
                                         
                                     </div>
                                 </div>
                                
                             </div>
                         </div>
                     </div>
                </section>                
            </div>
        </main>