<?php 
// Merchant id provided by CCAvenue
$Merchant_Id = "1234566"
// Item amount for which transaction perform
$Amount = "100";
// Unique OrderId that should be passed to payment gateway
$Order_Id = "006789";
// Unique Key provided by CCAvenue
$WorkingKey= "";
// Success page URL
$Redirect_Url="success.php";
$Checksum = getCheckSum($Merchant_Id,$Amount,$Order_Id ,$Redirect_Url,$WorkingKey);
?>
<form id="ccavenue" method="post" action="https://world.ccavenue.com/servlet/ccw.CCAvenueController">
<input type=hidden name="Merchant_Id" value="Merchant_Id">
<input type="hidden" name="Amount" value="Amount">
<input type="hidden" name="Order_Id" value="Order_Id">
<input type="hidden" name="Redirect_Url" value="<?php echo $Redirect_Url; ?>">
<input type="hidden" name="TxnType" value="A">
<input type="hidden" name="ActionID" value="TXN">
<input type="hidden" name="Checksum" value="<?php echo $Checksum; ?>">
<input type="hidden" name="billing_cust_name" value="name of user">
<input type="hidden" name="billing_cust_address" value="address of user">
<input type="hidden" name="billing_cust_country" value="user country">
<input type="hidden" name="billing_cust_state" value="state of user">
<input type="hidden" name="billing_cust_city" value="city">
<input type="hidden" name="billing_zip" value="zip/pin code">
<input type="hidden" name="billing_cust_tel" value="telphone no">
<input type="hidden" name="billing_cust_email" value="emailid">
<input type="hidden" name="delivery_cust_name" value="user name">
<input type="hidden" name="delivery_cust_address" value="delivering address">
<input type="hidden" name="delivery_cust_country" value="delivering country">
<input type="hidden" name="delivery_cust_state" value="delivering state">
<input type="hidden" name="delivery_cust_tel" value="telphone no">
<input type="hidden" name="delivery_cust_notes" value="this is a test">
<input type="hidden" name="Merchant_Param" value="">
<input type="hidden" name="billing_zip_code" value="zip/pin">
<input type="hidden" name="delivery_cust_city" value="city">
<input type="submit" value="Buy Now" />
</form>