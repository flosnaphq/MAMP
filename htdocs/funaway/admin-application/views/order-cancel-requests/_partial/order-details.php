<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
//Info::test($comments);
?>
<div class="areabody">
<div class="row">	
	<div class="col-sm-6">
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
	<div class="col-sm-6">
	<div class="repeatedrow">
		<h3><i class="icon ion-compose"></i> Order Cancel Request Details</h3>
		<div class="rowbody">
			<div class="listview">
				<dl class="list">
					<dt>Request Time</dt>
					<dd><?php echo FatDate::format($cancel_details['ordercancel_datetime'])?></dd>
				</dl>
				<dl class="list">
					<dt>Request Status</dt>
					<dd><?php echo Info::getOrderCancelRequestStatusByKey($cancel_details['ordercancel_status'])?></dd>
				</dl>
				<dl class="list">
					<dt>Requested Booking Id</dt>
					<dd><?php echo $cancel_details['ordercancel_booking_id']?></dd>
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
				<th>CANCELLED</th>
				<th>Price</th>
				<th>Number</th>
				<th>Received</th>
				
				<th>Total Price</th>
			</tr>
		</thead>	
			<?php if(!empty($activities)){ ?>
				<?php foreach($activities as $act){ ?>
					<tr>
						<td><?php echo $act['oactivity_booking_id']?></td>
						<td>
						<?php echo $act['oactivity_activity_name'];
						$addons ='';
						if(!empty($act['addons'])){
							foreach($act['addons'] as $addon){
								$addons .=$addon['oactivityadd_addon_name'].'+';
							}
						}
						$addons = trim($addons,'+');
						?>
						<br>
						<small><?php	echo $addons;?></small>
						</td>
						<td><?php echo Info::getOrderCancelStatusByKey($act['oactivity_is_cancel'])?></td>
						<td><?php echo $act['oactivity_unit_price']?></td>
						<td><?php echo $act['oactivity_members']?></td>
						<td><?php echo $act['oactivity_received_amount']?></td>
						
						<td><?php echo $act['oactivity_total_amount']?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		<tfoot style="background-color:#f9f9f9">
							<tr>
								<th colspan="9" >&nbsp;</th>
								
							</tr>
							<tr>
								<th colspan="8" >Booking Amount</th>
								<th><?php echo $order['order_net_amount']?></th>
							</tr>
						
							<tr>
								<th colspan="8" style="background-color:#ddd">Total Paid</th>
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
								<th colspan="8" style="background-color:#ddd">Received Amount</th>
								<th style="background-color:#ddd"><?php echo $order['order_received_amount']?></th>
							</tr>
						</tfoot>	
		</table>
	</div>
	
</div>
</div>