<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

?>
<?php
if(!empty($posts)){
	$total_post = count($posts);
	$i=0;
	foreach($posts as $post){
		$i++;
		?>
	
		<div class="post <?php if($i == $total_post) echo 'last';?> post-id-<?php echo $post['post_id']; ?>">
		   <div class="post__image">
			   <a href="<?php echo FatUtility::generateUrl('blog','post',array($post['post_seo_name']))?>"><img title="<?php echo $post['post_title']?>" alt="<?php echo $post['post_title']?>" src="<?php echo FatUtility::generateUrl('image','postDefaultImage',array($post['post_id'],1000,250))?>" />
				</a>
			   
		   </div>
		   <div class="post__text">
			   <h5 class="post__title"><a href="<?php echo FatUtility::generateUrl('blog','post',array($post['post_seo_name']))?>"><?php echo $post['post_title']?></a></h5>
			   <span class="post__meta"><?php echo Info::t_lang('BY')?> <?php echo $post['post_contributor_name']?></a></span>
			   <div class="post__content">
				   <p><?php echo nl2br( $post[BlogPosts::DB_TBL_PREFIX . 'short_description'] ); ?></p>
			   </div>
			   <a href="<?php echo FatUtility::generateUrl('blog','post',array($post['post_seo_name']))?>" class="post__link"><?php echo Info::t_lang('VIEW_FULL_POST')?></a>
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
	<?php
	}
	?>
	
	<?php
	echo html_entity_decode( $pagination);
}
else{
		echo Helper::noRecord('NO_POST');
	}
?>
