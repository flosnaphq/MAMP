<!DOCTYPE HTML>
<html>
    <head>
		<meta charset="utf-8" />
    </head>
    <body style="margin: 0;">
    <!-- Sharing Card -->
    <table width="504" style="border: 1px solid #e1e9f3;" bgcolor="#ffffff">
        <tr>
            <td colspan="2" style="padding: 10px 10px 0;">
				<div style="position:relative;">
                <img src="<?php echo FatUtility::generateFullUrl("image",'activity',array($activity['activity_image_id'],620,620))?>" alt="" style="display: inline-block; vertical-align: middle; width: 100%; -webkit-border-radius: 10px; border-radius: 10px;">    
				</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <h6 style="font-family: 'solitas_norm_medium', sans-serif; font-size: 16px; line-height: 1.2; margin: 0 0 20px;"><?php echo Helper::truncateString($activity['activity_name'],25)?></h6>
				<p style="font-family: 'solitas_norm_book', sans-serif; font-size: 12px; line-height: 1.2;"><?php echo $activity_content;?></p>
			</td>
			<td style="width: 150px; padding: 20px; text-align:right;">
				<img width="150" src="<?php echo FatUtility::generateFullUrl('image','companyLogo',array('conf_website_logo'));?>" alt="">
			</td>
        </tr>
    </table>    

    </body>
</html>
