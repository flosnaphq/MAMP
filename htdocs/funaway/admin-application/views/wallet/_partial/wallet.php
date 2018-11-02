<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<table width="100%" class="table table-responsive">
   <thead>
      <tr>
         <th>Debit Amount</th>
         <th>Credit Amount</th>
         <th>Total Balance</th>
        
      </tr>
   </thead>
   <tbody>
      <tr>
         <td><?php echo Info::price($wallet['debit_amount'])?></td>
         <td><?php echo Info::price($wallet['credit_amount'])?></td>
         <td><?php echo Info::price($wallet['wallet_balance'])?></td>
      </tr>
      
   </tbody>
</table>