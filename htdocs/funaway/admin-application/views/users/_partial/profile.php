<div class="avtararea">
	<figure class="pic">
		
			<img src="<?php echo FatUtility::generateUrl("image","user",array($record['user_id'],200,200),CONF_WEBROOT_URL)."?".Info::timestamp()?>" alt="" id="u-pic">
			<?php if($canEditProfile){?>
               
			
			<span class="uploadavtar">
				<i class="icon ion-android-camera"></i> Update Profile Picture 
                              
				<form id= "profile-img" action = "<?php echo FatUtility::generateUrl("users","update")?>" method="POST"  enctype="multipart/form-data" id = "profile-img-form">
                            <input type="hidden" name="img_data" id="img-data" value = "" />
							<figure class="avtarframe">
								<input type="hidden" id = "avatar-action" name="action" value="demo_avatar">
								<input type="hidden" id = "avatar-action" name="user_id" value="<?php echo $record['user_id']?>">
								<input type="hidden" id = "crop-response" name="response" value="">
                                <input type="file" id="user_image" name="user_image" onchange = "popupImage('profile-img')">
                            </figure>
                        </form>
				
			</span>
                        <?php if(true === $userImageExist) { ?>
                        <span class="uploadavtar" style="top:auto; bottom:0; background: #f25454;" onClick="removeUserImage(<?php echo $record['user_id']?>)">  <i class="icon ion-android-camera"></i> Remove Picture </span>
						<?php } ?>
			<?php } ?>
		    
	</figure>
	<div class="picinfo">
		<span class="name"><?php echo $record['user_firstname']." ".$record['user_lastname']?></span>
	</div>
	
	  <div class="contactarea">
		<h3>Contact Info    </h3>
		<ul class="contactlist">
			<li><i class="icon ion-android-call"></i><?php echo $record['user_phone']?></li>
			<li><i class="icon ion-android-mail"></i><?php echo $record['user_email']?></li>
			<!-- <li><i class="icon ion-ios-location"></i><?php # echo  $record['country_name']?></li> -->
		</ul>
	</div>
</div>