INSERT INTO `tbl_configurations` (`conf_name`, `conf_val`, `conf_common`) VALUES ('CONF_RECAPTCHA_SITEKEY', '6LcOY3IUAAAAAInKyIWy_eHfEBo1N8981IxY1wkD', '');
INSERT INTO `tbl_configurations` (`conf_name`, `conf_val`, `conf_common`) VALUES ('CONF_RECAPTCHA_SECRETKEY', '6LcOY3IUAAAAAO8unCMRZ9Tokj-sRn_fpPh2LLk8', '');
INSERT INTO `tbl_configurations` (`conf_name`, `conf_val`, `conf_common`) VALUES ('CONF_TAWK_TO_CODE', '', '');
UPDATE `tbl_configurations` SET `conf_val` = 'Europe/London' WHERE `tbl_configurations`.`conf_name` = 'conf_timezone';
