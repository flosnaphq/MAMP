<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

    <div class="fixed_container">
        <div class="row">



            <div class="col-sm-12">  
                <h1>User Requests</h1>   
<section class="section searchform_filter">
						<div class="sectionhead ">
						<h4>Search</h4>
						
						
						</div>
						<div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">
							
							<?php 
								$search->setFormTagAttribute ( 'onsubmit', 'search(this); return(false);');
								$search->setFormTagAttribute ( 'class', 'web_form' );
								$search->developerTags['fld_default_col'] = 4;
								echo  $search->getFormHtml();
							?>	
						</div>
					</section>
                <div id = "form-tab">
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4>User Requests</h4>
                    </div>
                    <div class="message-box">
                        <div class="sectionbody thread-listing">
                            <div id="listing">
                                processing....
                            </div>	
                            <div class = "message-list"> 
                            </div>
                        </div>
                    </div>
                </section>  


            </div> 


        </div>
    </div>
 
  								