<?php defined('SYSTEM_INIT') or die("INVALID ACCESS"); ?>
<div class="avtararea">
	<!--<figure class="pic">
		<img id="prof_img" src="<?php echo FatUtility::generateUrl('profile', 'profileImage', array(270, 190, time())); ?>" alt="">
		<div id="dv-bg-processes"></div>
		<?php 
			
			$imgForm->setFormTagAttribute( 'onSubmit', 'submitImage(this, imageValidator); return false;' );
			$imgForm->setFormTagAttribute( 'id', 'frmProfImage' );
			
			$avtar = $imgForm->getField( 'admin_avtar' );
			$avtar->setFieldTagAttribute( 'class', 'chngimage' );
			$avtar->setFieldTagAttribute( 'accept', 'image/*' );
                            $avtar->developerTags['fld_default_col'] =12;
			echo $imgForm->getFormtag();
		?>
			<span class="uploadavtar">
				<i class="icon ion-android-camera"></i>
				<?php 
					echo "Profile Picture";
					$imgFld = $imgForm->getField('admin_avtar');
					$imgFld->setFieldTagAttribute('id', 'chngimage');
					echo $imgForm->getFieldHTML('admin_avtar');
				?>
			</span>
			</form>
		<?php echo $imgForm->getExternalJS(); ?>
	</figure>-->
	<div class="picinfo">
		<span class="name"><?php echo $data[AdminUsers::DB_TBL_PREFIX . 'name'];?></span>
		<span class="mailinfo"><a href="mailto:<?php echo $data[AdminUsers::DB_TBL_PREFIX . 'email'];?>"><?php echo $data[AdminUsers::DB_TBL_PREFIX . 'email'];?></a></span>
	</div>
</div>

<div class="contactarea">
	<h3>Contact Info</h3>
	<ul class="contactlist">
		<li><i class="icon ion-android-mail"></i><a href="mailto:<?php echo $data[AdminUsers::DB_TBL_PREFIX . 'email'];?>"><?php echo $data[AdminUsers::DB_TBL_PREFIX . 'email'];?></a></li>
	</ul>
</div>