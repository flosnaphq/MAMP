<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Booking Cancel Requests</h1>   
					
					<section class="section searchform_filter">
						<div class="sectionhead ">
						<h4>Search</h4>
						
						
						</div>
						<div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">
							
							<?php 
								$search->setFormTagAttribute ( 'onsubmit', 'search(this); return(false);');
								$search->setFormTagAttribute ( 'class', 'web_form' );
								$search->developerTags['fld_default_col'] = 6;
								echo  $search->getFormHtml();
							?>	
						</div>
					</section>
					
					
				    <div id = "form-tab">
					</div>
					
					
					
					<section class="section">
                        <div class="sectionhead">
                            <h4>Booking Cancel Requests</h4>
							<a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
							
                        </div>
						
						
							<div class="sectionbody">
								<div id="listing">
									processing....
								</div>		
							</div>
                        </section>  
                      
                      
                     </div> 
                   
                    
                </div>
            </div>
    
		
		<script>
		var	user_id = 0;
		</script>
        								