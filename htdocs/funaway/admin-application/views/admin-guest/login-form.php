<?php defined ( 'SYSTEM_INIT' ) or die ( 'Invalid Usage.' ); ?>
<html>
	<head>
		<title><?php echo FatApp::getConfig('conf_website_name')?>-Admin</title>
		<style>
			<?php echo AppUtilities::includeFonts();?>
        </style>
		<?php
		$this->addCss('common-css/3_ionicons.css');
		$this->addCss('common-css/2_system_messages.css');
		$this->addJs('common-js/01-jquery-2.1.4.min.js');
		$this->addJs('common-js/form-validation.js');
		$this->addJs('common-js/jquery-form.js');
		$this->addJs('common-js/mbsmessage.js');
		$this->addJs('common-js/rn-common.js');
		echo $this->getJsCssIncludeHtml(false, FALSE); 
		?>
	</head>
	<body class="enterpage">
		<!--wrapper start here-->
		<main id="wrapper">
			<div class="backlayer">
				<div class="layerLeft" style="background-image:url(../images/sailing-boats.jpg); background-repeat:no-repeat;">
					<figure class="logo"><img alt="" src="<?php echo FatUtility::generateFullUrl('image','companyLogo',array('conf_website_admin_logo'),CONF_BASE_DIR);?>"></figure>
				</div>
				<div class="layerRight" style="background-image:url(../images/sailing-boats.jpg); background-repeat:no-repeat;">
					<figure class="logo"><img alt="" src="<?php echo FatUtility::generateFullUrl('image','companyLogo',array('conf_website_admin_logo'),CONF_BASE_DIR);?>"></figure>
				</div>
			</div>
			<div class="panels">
				<div class="innerpanel">
					<div class="left" id="forgot-password">
						<div class="formcontainer">
							<h5>Forgot your password? </h5>
							<h6>Enter The E-mail Address Associated With Your Account.</h6>
							<?php 
							$forgot->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
							$str1 = $forgot->getFormHtml();
							echo $forgot->getFormTag();?>
								<div class="field_control forgot fieldicon mail">
									<label class="field_label">Email <span class="mandatory">*</span></label>
									<div class="field_cover">
									   <?php echo $forgot->getFieldHtml("admin_email")?>
									</div>
								</div>
								
								<div class="field_control forgot fieldicon secure">
									<label class="field_label">Security Code <span class="mandatory">*</span></label>
								   
									<div class="field_cover">
										 <?php echo $forgot->getFieldHtml("security_code")?>
										 <br>
										 <?php echo $forgot->getFieldHtml("captcha")?>
									</div>
								</div>
								
								 <span class="circlebutton"> <?php echo $frm->getFieldHtml('btn_submit');?> </span>
								<a id="moveright" href="javascript:void(0)" class="linkright linkslide">Back to Login</a>
							</form>   
							<?php echo $forgot->getExternalJS();?>						
						</div>
					</div>
					<div class="right">
						<div class="formcontainer">
							<?php 
								$frm->setValidatorJsObjectName ( 'loginValidator' );
								$frm->setFormTagAttribute ( 'onsubmit', 'login(loginValidator); return(false);' );
								$frm->setFormTagAttribute ( 'class', 'web_form post-messages' );
								$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
								$str = $frm->getFormHtml();
							?>	
							<?php echo $frm->getFormTag();?>
								<div class="field_control login fieldicon user">
									<label class="field_label">Username <span class="mandatory">*</span></label>
									<div class="field_cover">
									   <?php echo $frm->getFieldHtml('username');?>
									</div>
								</div>
								<div class="field_control login fieldicon key">
									<label class="field_label">Password <span class="mandatory">*</span></label>
									<div class="field_cover">
									   <?php echo $frm->getFieldHtml('password');?>
									</div>
								</div>
								<div class="field_control">
									<a id="moveleft" href="javascript:void(0)" class="linkright linkslide">Forgot Password?</a>
								</div>
								<span class="circlebutton">
									<?php echo $frm->getFieldHtml('btn_submit');?>
								</span>
							</form>    
							<?php echo $frm->getExternalJS();?>
						</div>
					</div>
				</div>
			</div>
		</main>
	</body>
</html>