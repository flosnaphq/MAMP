<main id="MAIN" class="site-main site-main--darkest">
	<div class="site-main__body">
	   <div class="section section--vcenter no--margin section__join">
			<div class="section__body">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--8 span--center text--center"  style="max-width:500px">
							<h6 class="heading-text heading-text--medium"><?php echo Info::t_lang("WELCOME")?></h6>
							<p class="sub-heading-text"><?php echo sprintf(Info::t_lang('SIGN_UP_BELOW_TO_JOIN_%s'),FatApp::getConfig('conf_website_name'))?></p>
							<nav class="menu" role="navigation">
								<ul class="list list--vertical">
									<li><a href="javascript:;" onclick="fbLogin('<?php echo Info::t_lang('PLEASE_WAIT...')?>')"  class="button button--fill button--fit button--facebook">
									<span><svg class="icon"><use xlink:href="#icon-facebook" /></svg></span>  <span class="hidden-on--mobile"><?php echo Info::t_lang('CONNECT_WITH')?> </span><span><?php echo Info::t_lang('FACEBOOK')?></span></a></li>
									<!--<li><a href="javascript:;" onclick="socialLogin('<?php echo FatUtility::generateUrl('guest-user','twitterLogin')?>')" class="button button--fill button--fit button--twitter">
									<span><svg class="icon"><use xlink:href="#icon-twitter" /></svg></span>  <span class="hidden-on--mobile"><?php echo Info::t_lang('CONNECT_WITH')?> </span><span><?php echo Info::t_lang('TWITTER');?></span></a></li> -->
									<!--<li><a href="javascript:;" onclick="socialLogin('<?php echo FatUtility::generateUrl('guest-user','linkedin')?>')" class="button button--fill button--fit button--linkedin">
									<span><svg class="icon"><use xlink:href="#icon-linkedin" /></svg></span>  <span class="hidden-on--mobile"><?php echo Info::t_lang('CONNECT_WITH')?> </span><span><?php echo Info::t_lang('LINKEDIN');?></span></a></li> -->
									<?php if(isset($_SESSION['login_as']) && $_SESSION['login_as'] == 'traveler'){ ?>
									
									<li><a href="<?php echo FatUtility::generateUrl('guest-user','signup-form')?>"><?php echo Info::t_lang('OR_JUST')?> <span class="link"><?php echo Info::t_lang('USE_YOUR_EMAIL')?></span></a></li>
									<?php }else{ ?>
									
									<li><a href="<?php echo FatUtility::generateUrl( 'become-a-host')?>"><?php echo Info::t_lang('OR_JUST')?> <span class="link"><?php echo Info::t_lang('USE_YOUR_EMAIL')?></span></a></li>
									<?php } ?>
								</ul>
							</nav>
						</div>
					</div>
				</div>
		   </div>
			<p class="regular-text disclaimer"><?php echo sprintf(Info::t_lang('BY_PROCEEDING,_YOU_AGREE_TO_%s'),FatApp::getConfig('conf_website_name'))?> <a href="<?php echo FatUtility::generateUrl('cms', 'terms', array('privacy'))?>"> <?php echo Info::t_lang('PRIVACY_POLICY')?> </a><?php echo Info::t_lang("AND")?><a href="<?php echo Info::generateCustomUrl('cms','terms')?>"> <?php echo Info::t_lang('TERMS_OF_USE')?></a>.</p>
		</div>
	</div>
</main>