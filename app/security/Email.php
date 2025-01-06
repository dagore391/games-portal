<?php
    namespace app\security;

use app\config\Constants;
    
    class Email {
		public static function sendMail(string $to, string $subject, string $message) : bool {
			$message = wordwrap($message, 70, '\r\n');
			return mail($to, $subject, $message, 'From: ' . Constants::SITE_EMAIL);
        }
    }
?>
