<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Admin Commission</h1>   
					
					
					
				    <div id = "form-tab">
					</div>
					
					
					
					<section class="section">
                        <div class="sectionhead">
                            <h4>Admin Commission</h4>
							<a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
							<?php if($canEdit){ ?>
							<a href="javascript:;" onclick="getForm();return;"  class="themebtn btn-default btn-sm">
                               Add New
                            </a> 
							<?php } ?>
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
         
        								