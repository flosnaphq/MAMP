<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Users</h1>   
					
					<!--<section class="section searchform_filter">-->
						<!-- <div class="sectionhead ">-->
							<div class="tabs_nav_container responsive flat">
								 
								<ul class="tabs_nav">
									<li><a class="active" rel="tabs_01" href="javascript:;"  onclick="tab(1)" >PENDING USERS</a></li>
									<li><a rel="tabs_02" href="javascript:;"  onclick="tab(2)">USERS LIST</a></li>

								</ul>

								<div class="tabs_panel_wrap">

									<!--tab1 start here-->
									<span class="togglehead active" rel="tabs_01">PENDING USERS </span>
									<div id="tabs_01" class="tabs_panel">
										<?php echo $frm_1->getFormHtml(); ?>
									
									</div>
									<!--tab1 end here-->


									
									<!--tab3 start here-->
									<span class="togglehead" rel="tabs_02">USERS LIST </span>
									<div id="tabs_02" class="tabs_panel">
										<?php echo $frm_3->getFormHtml(); ?>
									</div>
									<!--tab3 end here-->  


								</div>      

							</div> 
						
						
						
						<!--</div>-->
						<div class="sectionbody togglewrap space" style="overflow: hidden; display: none;">
							
							
						</div>
					<!--</section>-->
					
					
				    <div id = "form-tab" >
						
					</div>
					
					
					
					<section class="section">
                        <div class="sectionhead">
                            <h4>Users</h4>
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
   
        								