<?php if(!empty($arr_listing)){ ?>

<ul class="medialist">
	<?php foreach($arr_listing as $msg){
		// Info::test($msg);
		if($msg['user_firstname'] == "") $msg['user_firstname'] = "admin";
		?>
	<li>
		<span class="grid first">
			<figure class="avtar bgm-<?php echo MyHelper::backgroundColor($msg['user_firstname'][0])?>">
				<?php echo $msg['user_firstname'][0]?>
			</figure>
		</span>    
		<div class="grid second">
			<div class="desc">
				<span class="name"><?php echo $msg['user_firstname']." ".$msg['user_lastname']?> (Activity: <?php echo $msg['activity_name'];?>)
					<span class="lightxt">
						<span> < </span>
						<?php echo ($msg['message_user_id'] == 0 ? FatApp::getConfig(ADMIN_SUPPORT_EMAIL_ID, FatUtility::VAR_STRING, 'asasd') : $msg['user_email'])?>
						<span> > </span>
					</span>
				</span>
				<div class="descbody"><?php echo $msg['message_text']?></div>
			</div>
		</div>    
		<span class="grid third">
			<span class="date"><i class="icon ion-ios-clock-outline"></i> <?php echo FatDate::format($msg['message_date'])?></span>
			<span class="date msg-thread themebtn btn-default btn-sm" onclick="viewThread(<?php echo $msg['message_thread_id']?>)">View<span>
		</span>
	</li>
	<?php } ?>
</ul>
		
<?php    
echo FatUtility::createHiddenFormFromData ( $postedData, array (
'name' => 'frmUserSearchPaging','id'=>'pretend_search_form' 
) );

	   if($totalPage>1){
?>
<div class="footinfo">
<aside class="grid_1">
<ul class="pagination">
<?php
echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', 
$totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, 
'<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
'<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', 
'<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
?>
</ul>
</aside>  

</div>
<?php
}	
}
else{
	?>
	<ul class="medialist">
		<li>
		No RECORD FOUND
		</li>
	</ul>
	<?php
}
?>