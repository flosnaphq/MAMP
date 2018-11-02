<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>

            <div class="fixed_container">
                <div class="row">
							
					
                    
                  <div class="col-sm-12">  
                    <h1>Earnings</h1>   
					
					
					
					
				    <div id = "form-tab">
					</div>
					
					<section class="section">
                        <div class="sectionbody">
									<div id="wallet-record"><table width="100%" class="table table-responsive">
									<thead>
									<tr>
										<th>Total Balance</th>
										<th>Credit Balance</th>
										<th>Debit Balance</th>
									</tr>
									<tr>
										<td><?php 	echo Currency::displayPrice($wallet['wallet_balance']);?></td>
										<td><?php 	echo Currency::displayPrice($wallet['credit_amount']);?></td>
										<td><?php 	echo Currency::displayPrice(abs($wallet['debit_amount']));?></td>
									</tr>
									</thead>

									</table></div>		
								</div>
						
						
							
                        </section>  
					
					<section class="section">
                        <div class="sectionhead">
                            <h4>Earnings</h4>
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
		var user_id = 0;
		</script>
        								