Funaway-V-2.1.5

Library version 2.0
Library Documentation: http://192.168.0.102/lib-doc-new

=============================
Installation Instructions:
=============================

1. Unzip funaway.zip on root folder.
2. Import database/database.sql to your database 
3. Update CONF_DB_SERVER, CONF_DB_USER, CONF_DB_PASS and CONF_DB_NAME in "conf/{your-domain-name.com}.php"
4. Set write permission for users-uploads folder "path: /users-uploads" ( set 777  )
5. Set write permission for cache folder "path: /public/cache" ( set 777  )
6. Set write permission for sitemap_xml folder "path: /sitemap_xml" ( set 777  )
7. Set write permission for sitemap.xml file on root folder ( set 777  )
8. Update Sitemap by opening URL:- http://{domain-name}/sitemap
9. Set Cron to execute every week (or you can set as and when required) and set command "curl -s http://{domain-name}/sitemap > /dev/null 2>&1"
10. Update content/settings from admin section as per requirements.


Make sure to give write permissions to following files/folders:

root_dir/public/cache/
root_dir/public/cache/all-sub-folders(if-any)
root_dir/user-uploads/
root_dir/user-uploads/all-sub-folders
root_dir/sitemap.xml

hit following URL to generate/update sitemap xml file

http://replace-with-your-domain-name/sitemap


If we have to make functionality of Prior confirmation request auto cancellation working need to set a cron for one hour interval. Add an entry in DB table

Note: cron_duration should be in minutes

INSERT INTO `tbl_cron_schedules` (`cron_id`, `cron_name`, `cron_command`, `cron_duration`, `cron_active`) VALUES (NULL, 'Update Pending Prio Confirmation Request', 'ActivityController/updatependingpriorconfirmationrequests', '1', '1');

and set cron on server with an interval of 5 mins

curl -s {your-domain-name}/cron/execute > /dev/null 2>&1


***Important Instructions:

Replace any relevent information which is specific to domain name or not related to this installation
like need to replace 
- Tracking code for google and facebook to avoid conflicts or to save irrelevent analytics information,
- change domain specification details like google recaptcha APP details,
- google and facebook login details,
- Add this for social sharing details,
- Facebook Tracking Id 
- Sms APP details: Sms API Key and Sms Secret Key
- MapBox Access Token
- Set Default Currency as required
- Update Email Settings as required
- Social site page URLs
- Mailchimp news letter form url. Please note thaty need to add "newsletter__form" in <form> tag in case when we are updating the code to match the style with the theme
- Make sure fatcache enabled in root_dir/public/application-top.php etc.

Please review the settings sections from admin panel for more configurable items which are required to be changed