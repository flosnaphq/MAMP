						<aside class="grid_2 message-listing">
                            <div class="toptitle">
                                <ul class="actions">
                                    <!--<li><a href="javascript:void(0)"  title="Expand All" class="expandlink"><i class="ion-arrow-expand icon"></i></a></li>-->
                                    <li><a href="javascript:;" onclick ="backThread()" title="Back"><i class="ion-android-arrow-back icon"></i></a></li>
                                </ul>
								<br/>
                                <?php if(!empty($activity_data)){ ?>
								<h4>Activity : <a target="_blank" href="<?php echo FatUtility::generateUrl('activities','details',array($activity_data['activity_id'])); ?>"><?php  echo $activity_data['activity_name']?></a></h4>
								<?php } ?>
                            </div>
                            
                        <div class="bodyarea"> 
                            <ul class="medialist">
									<?php foreach($records as $msg){  
									if($msg['user_firstname'] == "") $msg['user_firstname'] = "admin";
										?>
									
                                    <li class="bodycollapsed">
                                        <span class="grid first"><figure class="avtar bgm-<?php echo MyHelper::backgroundColor($msg['user_firstname'][0])?>"><?php echo $msg['user_firstname'][0]?></figure></span>    
                                        <div class="grid second">
                                            <div class="desc">
												<span class="name">
													<?php echo $msg['user_firstname']." ".$msg['user_lastname']?> 
													<span class="lightxt">
														<span> < </span>
														<?php echo ($msg['message_user_id'] == 0 ? FatApp::getConfig('ADMIN_SUPPORT_EMAIL_ID', FatUtility::VAR_STRING, 'asasd') : $msg['user_email'])?>
														<span> > </span>
													</span>
												</span>
                                                <div class="descbody">
                                                <?php echo $msg['message_text']?>
                                                </div>    
                                            </div> 
                                        </div>    
                                        <span class="grid third">
                                            <span class="date"><i class="icon ion-ios-clock-outline"></i> <?php echo FatDate::format($msg['message_date'])?></span>
                                        </span>
                                    </li>
									
									<?php } ?>
                                
                                
                            </ul> 
                            
                        </div> 
                            
                            
                        <div class="areareply">
                            <aside class="grid_1"></aside>    
                            <aside class="grid_2">
                               
                                <!--div class="boxcontainer" -->
                                   <?php 
							$frm->setValidatorJsObjectName ( 'formValidator' );
							$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
							$frm->setFormTagAttribute ( 'class', 'web_form' );
							$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form",'.$msg['message_thread_id'].'); return(false);' );
							$frm->developerTags['fld_default_col'] = 12;
							
							echo  $frm->getFormHtml();
							?>	
                            </aside>    
                        <!--/div-->    
                            
                            
                                
                            
                        </aside> 