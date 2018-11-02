<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Admins</h1>   
					<section class="section">
						<div class="sectionbody space">
						<div id = "form-tab">
							<?php 
								$frm->setValidatorJsObjectName ( 'formValidator' );
								$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
								$frm->setFormTagAttribute ( 'class', 'web_form' );
								$frm->developerTags['fld_default_col'] = 12;
								$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("password","action") );
								$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
								echo  $frm->getFormHtml();
							?>	
						</div>
						</div>
					</section>  
				</div> 
                </div>
            </div>
       
        								