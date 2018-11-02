<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<script>
var order_id = '<?php echo $order_id; ?>';
</script>


            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Transactions</h1>   
					
					
					
					
				    <div id = "form-tab" >
						
					</div>
					
					
					
					<section class="section">
                        <div class="sectionhead">
                            <h4>ORDER : <?php echo $order_id; ?></h4>
							<a href="javascript:;clearSearch()" id="clearSearch" style="display:none" class="themebtn btn-default btn-sm">Clear search</a>
							<?php if($canEdit){ ?>
							<a href="javascript:;" onclick="getForm('<?php echo $order_id; ?>');return;"  class="themebtn btn-default btn-sm">
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
      
        								