<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php 
$discoupon_type = $frm->getField('discoupon_type');
$discoupon_type->setFieldTagAttribute('onchange','changeCouponType(this.value)');
?>

<script>
var discoupon_type ='<?php echo $discoupon_type->value; ?>';
showTab();
</script>
<div class="tabs_nav_container responsive flat">
                         
	<ul class="tabs_nav">
		<li><a class="active" rel="tabs_01" href="javascript:;"> OFFER</a></li>
		
		<li id="tabs_02_li"><a rel="tabs_02" href="javascript:;">RESTAURANTS</a></li>
		
		<li id="tabs_03_li"><a rel="tabs_03" href="javascript:;">PRODUCTS</a></li>
		
		<li id="tabs_04_li"><a rel="tabs_04" href="javascript:;">CITIES</a></li>
		
	</ul>
	
	 <div class="tabs_panel_wrap">
				
				<!--tab1 start here-->
				<span class="togglehead active" rel="tabs_01">OFFER </span>
				<div id="tabs_01" class="tabs_panel">
						
						<?php 
	
	
							$frm->setValidatorJsObjectName ( 'formValidator' );
							$frm->setFormTagAttribute ( 'onsubmit', "submitForm(formValidator,'main_form'); return(false);" );
							$frm->setFormTagAttribute ( 'class', 'web_form' );
							$frm->setFormTagAttribute ( 'id', 'main_form' );
							$frm->developerTags['fld_default_col'] = 6;
							$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("offers","couponFormAction") );
							$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_POSITION_NONE);
							$submit_btn = $frm->getField('submit_btn');
							$submit_btn->developerTags['col']=3;
							$cancel = $frm->getField('cancel');
							$cancel->developerTags['col']=3;
							$cancel->setFieldTagAttribute('onclick','closeForm()');
							$discoupon_weekday_specific = $frm->getField('discoupon_weekday_specific');
							$discoupon_weekday_specific->setFieldTagAttribute('onchange','weekdays(this.value)');
							$weekdays = $frm->getField('weekdays');
							$weekdays->setWrapperAttribute('id','weekdays-wrapper');
							if($discoupon_weekday_specific->value == 0){
								$weekdays->setWrapperAttribute('style',"display:none;");
							}
							echo  $frm->getFormHtml();
							?>
						
				</div>
				<!--tab1 end here-->
			 
			  
			   <!--tab2 start here-->
				<span class="togglehead" rel="tabs_02">RESTAURANTS </span>
				<div id="tabs_02" class="tabs_panel">
					   <?php 
						$restForm->setValidatorJsObjectName('restFormValidator');
						$restForm->setFormTagAttribute( 'class', 'web_form' );
						$restForm->developerTags['fld_default_col'] = 12;
						$restForm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
						$restForm->setFormTagAttribute('action',FatUtility::generateUrl('offers','couponRestFormAction'));
						$restForm->setFormTagAttribute ( 'onsubmit', "submitForm(formValidator,'rest_form'); return(false);" );
						$restForm->setFormTagAttribute ( 'id', 'rest_form' );
						
						echo  $restForm->getFormHtml();
						?>	
						</form>
				</div>
			 <!--tab2 end here-->  
			 
			 
		   <!--tab3 start here-->
			<span class="togglehead" rel="tabs_03">PRODUCTS </span>
			<div id="tabs_03" class="tabs_panel">
				   <?php 
					$productForm->setValidatorJsObjectName('productFormValidator');
					$productForm->setFormTagAttribute( 'class', 'web_form' );
					$productForm->developerTags['fld_default_col'] = 12;
					$productForm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
					$productForm->setFormTagAttribute('action',FatUtility::generateUrl('offers','couponProductFormAction'));
					$productForm->setFormTagAttribute ( 'onsubmit', "submitForm(formValidator,'product_form'); return(false);" );
					$productForm->setFormTagAttribute ( 'id', 'product_form' );
					
					echo  $productForm->getFormHtml();
					?>	
					</form>
			</div>
		 <!--tab2 end here-->  
		 
		 <!--tab3 start here-->
			<span class="togglehead" rel="tabs_04">CITIES </span>
			<div id="tabs_04" class="tabs_panel">
				   <?php 
					$cityForm->setValidatorJsObjectName('productFormValidator');
					$cityForm->setFormTagAttribute( 'class', 'web_form' );
					$cityForm->developerTags['fld_default_col'] = 12;
					$cityForm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
					$cityForm->setFormTagAttribute('action',FatUtility::generateUrl('offers','couponCityFormAction'));
					$cityForm->setFormTagAttribute ( 'onsubmit', "submitForm(formValidator,'city_form'); return(false);" );
					$cityForm->setFormTagAttribute ( 'id', 'city_form' );
					
					echo  $cityForm->getFormHtml();
					?>	
					</form>
			</div>
		 <!--tab2 end here-->  
		 
		 
	 </div>      

</div> 
<script>

 $(".tabs_panel").hide();
 $('.tabs_panel_wrap').find(".tabs_panel:first").show();
  $(".tabs_nav li a").click(function() {
  $(this).parents('.tabs_nav_container:first').find(".tabs_panel").hide();
  var activeTab = $(this).attr("rel"); 
  $("#"+activeTab).fadeIn();		

  $(this).parents('.tabs_nav_container:first').find(".tabs_nav li a").removeClass("active");
  $(this).addClass("active");

  $(".togglehead").removeClass("active");
  $(".togglehead[rel^='"+activeTab+"']").addClass("active");

});

</script>
						