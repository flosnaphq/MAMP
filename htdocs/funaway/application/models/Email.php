<?php

class Email extends FatModel
{

    function __construct()
    {
        parent::__construct();
    }

    public static function simpleMail($to, $subject, $body)
    {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $headers .= 'From: ' . FatApp::getConfig("conf_website_name", FatUtility::VAR_STRING, '') . "<" . FatApp::getConfig("conf_emails_from", FatUtility::VAR_STRING, '') . ">" . "\r\n";
        $headers .= "Reply-to: " . FatApp::getConfig("conf_emails_from", FatUtility::VAR_STRING, '');

        return mail($to, $subject, $body, $headers);
    }

    static function sendMail($to, $tpl_id = 0, $vars = array(), $file = array())
    {
        $viasmtp = FatApp::getConfig("conf_email_method", FatUtility::VAR_INT, 0);

        $response = Self::formatBody($tpl_id, $vars);
        $subject = $response['subject'];
        $body = $response['message'];
        /* if(!empty($file)) {
          $filename = $file['file_name'];
          $diaplayname = $file['display_name'];
          $headers = get_attachment($filename,$diaplayname,$body);
          } else {
          $headers  = 'MIME-Version: 1.0' . "\r\n";
          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          $headers .= 'From: '. FatApp::getConfig('conf_website_name').' ' . FatApp::getConfig('conf_emails_from',FatUtility::VAR_STRING) . "\r\n";
          } */


        if ($viasmtp == 1) {
            $smtp_arr = array(
                "host" => FatApp::getConfig("conf_smtp_host", FatUtility::VAR_STRING, ''),
                "port" => FatApp::getConfig("conf_smtp_port", FatUtility::VAR_INT, 0),
                "username" => FatApp::getConfig("conf_smtp_username", FatUtility::VAR_STRING, ''),
                "password" => FatApp::getConfig("conf_smtp_password", FatUtility::VAR_STRING, '')
            );
        }

        if (FatApp::getConfig("conf_emails_from", FatUtility::VAR_STRING, '')) {
            // if ((Settings::getSetting("CONF_SEND_SMTP_EMAIL")) || $viasmtp)
            if ($viasmtp == 1) {
                return Self::sendSmtpEmail($to, $subject, $body, '', $smtp_arr);
            } else {
                return Self::simpleMail($to, $subject, $body);
            }
        } else {
            return true;
        }

        // mail($to, $subject, $body, $headers);
        // return true;
    }

    static function formatBody($tpl_id, $replace_vars = array())
    {
        $response = array();
        $search = new SearchBase('tbl_email_templates');
        $search->addMultipleFields(array('tpl_subject', 'tpl_body', 'tpl_replacements'));
        $search->addCondition('tpl_id', '=', FatUtility::int($tpl_id));
        $row = FatApp::getDb()->fetch($search->getResultSet());
        if (!$row) {
            return false;
        }
        if (empty($row['tpl_subject']) || empty($row['tpl_body'])) {
            return false;
        }
        $response['subject'] = $row['tpl_subject'];
        $body = $row['tpl_body'];
        foreach ($replace_vars as $key => $val) {
            $body = str_replace($key, $val, $body);
            $response['subject'] = str_replace($key, $val, $response['subject']);
        }
        $vars['{message}'] = $body;
        $vars['{date}'] = FatDate::format(Info::currentDate('Y-m-d'));
        $vars['{site_name}'] = FatApp::getConfig('conf_website_name', FatUtility::VAR_STRING, '');
        $vars['{site_logo}'] = FatUtility::generateFullUrl('Image', 'companyLogo', array('CONF_WEBSITE_LOGO'), CONF_WEBROOT_URL);
        $vars['{url}'] = FatUtility::generateFullUrl('', '', array(), '/');
        $vars['{image_url}'] = FatUtility::generateFullUrl('', '', array(), '/') . 'images';
        $vars['{fburl}'] = FatApp::getConfig('conf_facebook_url', FatUtility::VAR_STRING, '');
        $vars['{twitterurl}'] = FatApp::getConfig('conf_twitter_url', FatUtility::VAR_STRING, '');
        $vars['{tumblrurl}'] = FatApp::getConfig('CONF_TUMBLR_URL', FatUtility::VAR_STRING, '');
        $vars['{pinteresturl}'] = FatApp::getConfig('CONF_PINTERES_URL', FatUtility::VAR_STRING, '');
        $vars['{conf_instagram_url}'] = FatApp::getConfig('conf_instagram_url', FatUtility::VAR_STRING, '');
        $vars['{conf_snapchat_url}'] = FatApp::getConfig('conf_snapchat_url', FatUtility::VAR_STRING, '');
        $body = file_get_contents(CONF_INSTALLATION_PATH . '/public/mail-template/default.html');
        foreach ($vars as $key => $val) {
            $body = str_replace($key, $val, html_entity_decode($body));
            $response['subject'] = str_replace($key, $val, html_entity_decode($response['subject']));
        }
        $response['message'] = $body;
        return $response;
    }

    /* static function get_attachment($file_name,$display_name,$body) {
      if(empty($file_name)) {
      return;
      }
      $file_size = filesize($file_name);
      $handle = fopen($file_name, "r");
      $content = fread($handle, $file_size);
      fclose($handle);
      $content = chunk_split(base64_encode($content));
      $uid = md5(uniqid(time()));
      $name = basename($file_name);
      $header  = 'MIME-Version: 1.0' . "\r\n";
      $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
      $header .= 'From: ' . CONF_EMAILS_FROM . "\r\n";
      $header .= "This is a multi-part message in MIME format.\r\n";
      $header .= "--".$uid."\r\n";
      $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
      $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
      $header .= $body."\r\n\r\n";
      $header .= "--".$uid."\r\n";
      $header .= "Content-Type: application/octet-stream; name=\"".$display_name."\"\r\n";
      // use different content types here
      $header .= "Content-Transfer-Encoding: base64\r\n";
      $header .= "Content-Disposition: attachment; filename=\"".$display_name."\"\r\n\r\n";
      $header .= $content."\r\n\r\n";
      $header .= "--".$uid."--";

      return $header;
      }
     */

    static function mail_attachment($filename, $path, $mailto, $subject, $message)
    {
        $file = $filename;


        $content = file_get_contents($filename);


        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $replyTo = "test@dummyid.com";
        $name = basename($file);
        $header = "From: " . $replyTo . " <" . $subject . ">\r\n";
        $header .= "Reply-To: " . $replyTo . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n";


        $header .= "--" . $uid . "\r\n";
        $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
        $header .= "Content-Transfer-Encoding: 7bit\r\n";
        $header .= $message . " \r\n";

        $header .= "--" . $uid . "\r\n";
        $header .= "Content-Type: application/octet-stream; name='" . $name . "'\r\n"; // use different content types here
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename='" . $name . "'\r\n"; // For Attachment
        $header .= $content . " \r\n";
        $header .= "--" . $uid . "--";
        die($header);
        mail($mailto, $subject, $message, $header);
        return true;
    }

    private static function sendSmtpEmail($toAdress, $subject, $body, $attachment = "", $smtp_arr = array())
    {
        require_once CONF_INSTALLATION_PATH . 'library/PHPMailer/PHPMailerAutoload.php';

        $host = ($smtp_arr["host"] != "" ? $smtp_arr["host"] : FatApp::getConfig("conf_smtp_host", FatUtility::VAR_STRING, '')); // or "mail.example.com" is using without ssl
        $port = ($smtp_arr["port"] != "" ? $smtp_arr["port"] : FatApp::getConfig("conf_smtp_port", FatUtility::VAR_STRING, '')); // only is using ssl
        $username = ($smtp_arr["username"] != "" ? $smtp_arr["username"] : FatApp::getConfig("conf_smtp_username", FatUtility::VAR_STRING, '')); // only is using ssl
        $password = ($smtp_arr["password"] != "" ? $smtp_arr["password"] : FatApp::getConfig("conf_smtp_password", FatUtility::VAR_STRING, '')); // only is using ssl
        //die($host."=".$port."=".$username."=".$password);
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        // $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;
        $mail->SMTPSecure = 'tls';
        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host = $host;
        //Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = $port;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = $username;
        //Password to use for SMTP authentication
        $mail->Password = $password;
        //Set who the message is to be sent from
        $mail->setFrom(FatApp::getConfig("conf_emails_from", FatUtility::VAR_STRING, ''), FatApp::getConfig("conf_website_name", FatUtility::VAR_STRING, ''));
        //Set an alternative reply-to address
        // $mail->addReplyTo(FatApp::getConfig("conf_reply_to_email", FatUtility::VAR_STRING, ''), FatApp::getConfig("conf_website_name", FatUtility::VAR_STRING, ''));
        $mail->addReplyTo('hello.footloos.com', FatApp::getConfig("conf_website_name", FatUtility::VAR_STRING, ''));
        //$mail->addReplyTo('info@dummyid.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($toAdress);
        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($body);
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        if (!$mail->send()) {
            //echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
            return false;
            //return $mail->ErrorInfo;
        } else {
            //echo 'Message sent.';
            return true;
        }
    }

}

?>