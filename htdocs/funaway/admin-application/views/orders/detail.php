<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
            <div class="fixed_container">
                <div class="row">
                    <div class="col-sm-12">  
                    <h1>Order Id - <?php echo $order['order_id']?></h1> 
                    <div class="containerwhite">
                        <aside class="grid_2">
						<div class="areabody">
				<div class="row">	
					<div class="col-sm-12">
					<div class="repeatedrow">
						<h3><i class="ion-bag icon"></i> Order Detail</h3>
						<div class="rowbody">
							<div class="listview">
								<dl class="list">
									<dt>Order Id</dt>
									<dd><?php echo $order['order_id']?></dd>
								</dl>
								<dl class="list">
									<dt>User Name</dt>
									<dd><?php echo $order['user_firstname'].' '.$order['user_lastname']; ?></dd>
								</dl>
								<dl class="list">
									<dt>Email</dt>
									<dd><?php echo $order['user_email']?></dd>
								</dl>
								
								<dl class="list">
									<dt>Phone Number</dt>
									<dd><?php echo $order['user_phone']?></dd>
								</dl>
								<dl class="list">
									<dt>Total Paid</dt>
									<dd><?php echo $order['order_total_amount']?></dd>
								</dl>
								<dl class="list">
									<dt>Booking Price</dt>
									<dd><?php echo $order['order_net_amount']?></dd>
								</dl>
								<dl class="list">
									<dt>Amount Received</dt>
									<dd><?php echo $order['order_received_amount']?></dd>
								</dl>
								<dl class="list">
									<dt>Payment Status</dt>
									<dd><?php echo Info::getPaymentStatus($order['order_payment_status'])?></dd>
								</dl>
								
								
							</div>
						</div>    
					</div>
					</div>
					
					<div class="col-sm-12">
						<table class="table table-responsive table-select" style="border:1px solid #ddd">
						<thead style="background-color:#f9f9f9">
							<tr>
								<th>Booking Id</th>
								<th>Activity</th>
								<th>Price</th>
								<th>Number</th>
								<th>Addons</th>
								<th>Received</th>
								<th>Total</th>
							</tr>
						</thead>	
							<?php if(!empty($activities)){ ?>
								<?php foreach($activities as $act){ ?>
									<tr>
										<td><?php echo $act['oactivity_booking_id']?></td>
										<td><?php echo $act['oactivity_activity_name'];?></td>
										<td><?php echo $act['oactivity_unit_price']?></td>
										<td><?php echo $act['oactivity_members']?></td>
										<td><?php if(!empty($act['addons'])){
											?>
											<style>
											.table .table th, .table .table td{
												padding:7px 15px;
											}
											</style>
											<table class="table table-responsive table-select" style="border:1px solid #ddd">
											
											<tr>
												<th>Name</th>
												<th>Price</th>
												<th>Number</th>
												<th>Total</th>
												
											</tr> 
											<?php
											$addon_totals =0;
											foreach($act['addons'] as $addon){
												$addon_totals += $addon['oactivityadd_unit_price']*$addon['oactivityadd_quantity'];
												?>
												<tr>
													<td><?php echo $addon['oactivityadd_addon_name']?></td>
													<td><?php echo $addon['oactivityadd_unit_price']?></td>
													<td><?php echo $addon['oactivityadd_quantity']?></td>
													<td><?php echo ($addon['oactivityadd_unit_price']*$addon['oactivityadd_quantity'])?></td>
												</tr>
												<?php
												
											}
											?>
											
											<tr>
												<th style="background-color:#ddd" colspan="3">
												Total
												</th>
												<th style="background-color:#ddd"><?php echo $addon_totals; ?>
												</th>
											<tr> 
											</table>
											<?php
										}
										else{
										echo '--';
										}
										?></td>
										<td><?php echo $act['oactivity_received_amount']?></td>
										<td><?php echo $act['oactivity_total_amount']?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<tfoot style="background-color:#f9f9f9">
							<tr>
								<th colspan="7" >&nbsp;</th>
							</tr>
							<tr>
								<th colspan="6" >Booking Amount</th>
								<th><?php echo $order['order_net_amount']?></th>
							</tr>
							<tr>
								<th colspan="6" style="background-color:#ddd">Total Paid</th>
								<td style="background-color:#ddd"><?php echo $order['order_total_amount']?></td>
							</tr>
							<?php 
							$total_transaction_fee=0;
							if(isset($extra_charges[2])){
								$total_transaction_fee +=$extra_charges[2]['ordercharge_amount'];
							}
							if(isset($extra_charges[3])){
								$total_transaction_fee +=$extra_charges[3]['ordercharge_amount'];
							}
							?>
							<tr>
								<th colspan="6" style="background-color:#ddd">Received Amount</th>
								<th style="background-color:#ddd"><?php echo $order['order_received_amount']?></th>
							</tr>
						</tfoot>	
						</table>
					</div>
				</div>
				</div>
            </aside>  
                    </div>
                   </div>   
                </div>
            </div>