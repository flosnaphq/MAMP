<div class="avtararea">
	<figure class="pic">
		<form>
			<img src="images/emp_10.jpg" alt="">
			<span class="uploadavtar">
				<i class="icon ion-android-camera"></i> Update Profile Picture 
				<input type="file">
			</span>
		</form>    
	</figure>
	<div class="picinfo">
		<span class="name"><?php echo $record['user_firstname']." ".$record['user_lastname']?></span>
	</div>
	
	  <div class="contactarea">
		<h3>Contact Info    </h3>
		<ul class="contactlist">
			<li><i class="icon ion-android-call"></i><?php echo $record['user_phone']?></li>
			<li><i class="icon ion-android-mail"></i><?php echo $record['user_email']?></li>
			<li><i class="icon ion-ios-location"></i><?php # echo $record['country_name']?></li>
		</ul>
	</div>
</div>