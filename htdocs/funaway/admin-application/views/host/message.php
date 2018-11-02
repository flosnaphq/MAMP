
            <div class="fixed_container">
                <div class="row">
                    
                   
                    
                  <div class="col-sm-12">  
                    
                    <h1>My Profile</h1> 
                    <div class="containerwhite">
                        <aside class="grid_1 profile">
                            <div id="profile-section">
						
							</div>
                        </aside>  
						<div class="message-box">
                        <aside class="grid_2 thread-listing">
						
							<?php  require_once("_partial/heading.php")  ?>
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
                            <div class = "message-list"> 
							</div>
                        </aside>  
						</div>
                    </div>
                   </div> 
                   
                    
                </div>
            </div>

        
		
		<script>
			user_id = <?php echo $user_id?>;
		</script>