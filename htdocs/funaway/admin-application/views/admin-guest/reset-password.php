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
				<div class="layerLeft" style="background-image:url(../images/dealsbg.jpg); background-repeat:no-repeat;">
					<figure class="logo"><img alt="" src="<?php echo FatUtility::generateFullUrl('image','companyLogo',array('conf_website_admin_logo'),CONF_BASE_DIR);?>"></figure>
				</div>
				<div class="layerRight" style="background-image:url(../images/sailing-boats.jpg); background-repeat:no-repeat;">
					<figure class="logo"><img alt="" src="<?php echo FatUtility::generateFullUrl('image','companyLogo',array('conf_website_admin_logo'),CONF_BASE_DIR);?>"></figure>
				</div>
			</div>
			<div class="panels">
				<div class="innerpanel">
					<div class="left" id="forgot-password"></div>
					<div class="right">
						<div class="formcontainer">
							<?php echo $frm_password->getFormTag();?>
								<div class="field_control login fieldicon user">
									<label class="field_label">New Password <span class="mandatory">*</span></label>
									<div class="field_cover">
									   <?php echo $frm_password->getFieldHtml('admin_password');?>
									</div>
								</div>
								<div class="field_control login fieldicon key">
									<label class="field_label">Confirm Password <span class="mandatory">*</span></label>
									<div class="field_cover">
									   <?php echo $frm_password->getFieldHtml('cpassword');?>
									</div>
								</div>
								<span class="circlebutton"> <?php echo $frm_password->getFieldHtml('btn_submit');?> </span>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php echo $frm_password->getExternalJS();?>
		</main>
	</body>
</html>