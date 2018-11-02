<?php defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<?php require_once(CONF_THEME_PATH.'blog/common/social_js.php');?>
<main id="MAIN" class="site-main site-main--light">
	<header class="site-main__header site-main__header--dark main-carousel__list js-main-carousel">
		<?php
		foreach($__sliderImages as $__sliderImage){
			?>
			<div class="site-main__header__image main-carousel__item"><img src="<?php echo FatUtility::generateUrl('image','post-image',array($__sliderImage[Blogposts::DB_IMG_TBL_PREFIX.'id'],2000,500,$post[Blogposts::DB_TBL_PREFIX.'id']))?>" />
			</div>
			<?php
		
		}
		?>
		
		
	</header>
	<div class="site-main__body">
		<section class="section">
			<div class="section__body">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--10 span--center">
						   <div class="post">
							   <div class="post__text">
								   <h5 class="post__title"><a href="javascript:;"><?php echo @$post[BlogPosts::DB_TBL_PREFIX.'title']?></a></h5>
								   <span class="post__meta"><?php echo Info::t_lang('BY')?> <a href="javascript:;"><?php echo $post['post_contributor_name']?></a></span>
								   <div class="post__content">
									   <?php echo html_entity_decode($post[BlogPosts::DB_TBL_PREFIX.'content'])?>
								   </div>
								   <nav class="menu post__social-menu">
										<ul class="list">
											 <li><a  href="javascript:;" onclick="graphStreamPublish('<?php echo FatUtility::generateFullUrl('blog','post',array($post['post_seo_name']))?>', '<?php echo FatUtility::generateFullUrl('image','postDefaultImage',array($post['post_id']))?>','<?php echo addslashes($post['post_title'])?>','<?php echo addslashes($post['post_short_description'])?>')">
												<svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-facebook"></use></svg>
												<span class="assistive__text"><?php echo Info::t_lang('FACEBOOK')?></span></a></li>
											
											<li>
											<a href="http://twitter.com/share?url=<?php echo FatUtility::generateFullUrl('blog','post',array($post['post_seo_name'])); ?>;text=<?php echo urlencode($post['post_title'])?>;size=l&amp;count=none" target="_blank" >
												<svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-twitter"></use></svg>
												<span class="assistive__text"><?php echo Info::t_lang('TWITTER')?></span></a></li>
											<li><a onclick="pinit('<?php echo FatUtility::generateFullUrl('blog','post',array($post['post_seo_name'])); ?>', '<?php echo FatUtility::generateFullUrl('image','postDefaultImage',array($post['post_id']))?>', '<?php echo addslashes($post['post_short_description'])?>')" href="javascript:;">
												<svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-pinterest"></use></svg>
												<span class="assistive__text"><?php echo Info::t_lang('PINIT')?></span></a></li>
											
											
											
											
										</ul>
									</nav>
							   </div>
						   </div>
							
						   <nav  class="pagination no--padding text--center">
								<?php if($pre_post_slug){ ?>
								<a  href="<?php echo FatUtility::generateUrl('blog','post', array($pre_post_slug))?>" class="button button--fill button--dark fl--left">
									<span><?php echo Info::t_lang('PREV')?> <span class="hidden-on--mobile"><?php echo Info::t_lang('BLOG')?> </span><?php echo Info::t_lang('POST')?></span>
								</a>
								<?php } ?>
								<?php if($next_post_slug){ ?>
								<a  href="<?php echo FatUtility::generateUrl('blog','post', array($next_post_slug))?>" class="button button--fill button--dark fl--right">
									<span><?php echo Info::t_lang('NEXT')?> <span class="hidden-on--mobile"><?php echo Info::t_lang('BLOG')?> </span><?php echo Info::t_lang('POST')?></span>
								</a>
								<?php } ?>
							</nav>
							
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="section section--light" id="comments_area_wrapper">
			<div class="section__header">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--10 span--center">
							<hgroup>
								<h5 class="heading-text"><?php echo Info::t_lang('READ_COMMENTS')?></h5>
								<h6 class="sub-heading-text text--orange"><?php echo Info::t_lang('WHAT_TRAVELLERS_SAY')?></h6>
							</hgroup>
						</div>
					</div>
				</div> 
			</div>
			<div class="section__body">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--10 span--center">
							<div class="comment__list" id="comments_area">
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php if( $commentFrm && FatUtility::int( $post[ BlogPosts::DB_TBL_PREFIX . 'comment_status' ] ) == 1 ) { 
		$commentFrm->setFormTagAttribute( 'class', 'form form--vertical form--theme' );
		$commentFrm->setRequiredStarPosition( 'none' );
		$commentFrm->setValidatorJsObjectName( 'commentFrmValidator' );
		$commentFrm->setFormTagAttribute( "action", FatUtility::generateUrl( 'Blog', 'saveComment', array( $postSeoName ) ) );
		/* $commentFrm->setFormTagAttribute( "onsubmit", 'return submitComment( commentFrmValidator, this );' ); */
		
		 $nameFld = $commentFrm->getField( 'comment_author_name' );
		$nameFld->addFieldTagAttribute( 'id', 'comment_author_name' );
		/*
		$nameFld->developerTags['col'] =6; */
		
		if ( !User::isUserLogged() ) { 
			$emailFld = $commentFrm->getField( 'comment_author_email' );
			$emailFld->addFieldTagAttribute( 'id', 'comment_author_email' );
			/*
			$emailFld->developerTags['col'] =6; */
		}
		
		$conFld = $commentFrm->getField( 'comment_content' );
		$conFld->addFieldTagAttribute( 'id', 'comment_content' );
		
		//$conFld->developerTags['col'] =12;
		
		$scFld = $commentFrm->getField( 'security_code' );
		$scFld->addFieldTagAttribute( 'id', 'security_code' );
		
		$scFld->addFieldTagAttribute( 'autocomplete', 'off' );
		$scFld->setWrapperAttribute( 'class', 'security-wrapper' );
		$scFld->requirements()->setCustomErrorMessage( Info::t_lang( 'ENTER_SECURITY_CODE' ) );
		//$scFld->developerTags['col'] =6;
		
		$btn_submit = $commentFrm->getField('btn_submit');
		$btn_submit->setFieldTagAttribute('class','button button--fill button--secondary');
	//	$btn_submit->developerTags['col'] =6;
		//$btn_submit->htmlBeforeField='<br>';
		
		?>
		<section class="section">
			<div class="section__header">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--10 span--center">
							<hgroup>
								<h5 class="heading-text"><?php echo Info::t_lang('WRITE_COMMENT')?></h5>
							</hgroup>
						</div>
					</div>
				</div> 
			</div>
			<div class="section__body">
				<div class="container container--static">
					<div class="span__row">
						<div class="span span--10 span--center">
							<?php
							echo $commentFrm->getFormHtml();
							?>
							
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php } ?>
	</div>
</main>
		
<script type="text/javascript">
	var postId = <?php echo $post[BlogPosts::DB_TBL_PREFIX . 'id']; ?>;
</script>
