
<div class="section__header">
	<div class="container container--static">
		<div class="span__row">
			<div class="span span--10 span--center">
	 <nav class="menu text--center">
		 <?php if(!empty($categories)){ ?>
		 <ul class="list list--horizontal buttons__group" style="white-space:normal">
			<?php foreach($categories as $category){ ?>
			<li><a href="<?php echo FatUtility::generateUrl('blog', 'category', array($category['category_seo_name']))?>" class="button button--non-fill button--dark button--small <?php if(isset($cat_seo_name) && $cat_seo_name == $category['category_seo_name']) echo 'has--active'?>"><?php echo $category['category_title']?></a></li>
			<?php } ?>
			
		 </ul>
		 <?php } ?>
	 </nav>
			</div>
		</div>
   </div>
 </div>