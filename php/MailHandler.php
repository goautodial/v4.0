<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

namespace creamy;

define ('CRM_NO_REPLY_ADMIN_EMAIL_ADDRESS', 'no-reply@creamycrm.com');

require_once('CRMDefaults.php');
require_once('LanguageHandler.php');

// constants
define ('CRM_MAIL_PARAMETER_TITLE', "{title}");
define ('CRM_MAIL_PARAMETER_TEXT', "{text}");
define ('CRM_MAIL_PARAMETER_LINK_URL', "{linkurl}");
define ('CRM_MAIL_PARAMETER_LINK_TITLE', "{linktitle}");


/**
 *  MailHandler.
 *  This class is in charge of sending emails and communicating system information to users. 
 *  MailHandler uses the Singleton pattern, thus gets instanciated by the MailHandler::getInstante().
 */
class MailHandler {
	/** Variables && data */
	private $db; // database handler

	/** Creation and class lifetime management */

	/**
     * Returns the singleton instance of UIHandler.
     * @staticvar LanguageHandler $instance The LanguageHandler instance of this class.
     * @return LanguageHandler The singleton instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

	
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        require_once dirname(__FILE__) . '/DbHandler.php';
        // opening db connection
        $this->db = new \creamy\DbHandler();
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
    
	/** Mailing methods */
	
	/** 
	 * Sends a recovery mail to the user. The user must have a valid email contained in the database.
	 * @param $email string string of the user.
	 * @return true if successful, false if email couldn't be sent.
	 */
	public function sendPasswordRecoveryEmail($email) {
		// safety check.
		if (!$this->db->userExistsIdentifiedByEmail($email)) { return false; }
		
		// prepare values.
		$lh = \creamy\LanguageHandler::getInstance();
		$dateAsString = date('Y-m-d-H-i-s');
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		// generate a nonce to avoid replay attacks && the password reset code.
		$randomStringGenerator = new \creamy\RandomStringGenerator();
		$nonce = $randomStringGenerator->generate(40);
		// build the message.
		$resetCode = $this->db->generateEmailSecurityCode($email, $dateAsString, $nonce);
		$title = $lh->translationFor("password_recovery_title");
		$text = $lh->translationFor("password_recovery_text");
		$linkTitle = $lh->translationFor("password_recovery_button");
		$linkURL = "$baseURL/passwordrecovery.php?email=".urlencode($email)."&amp;code=".urlencode($resetCode)."&amp;date=".urlencode($dateAsString)."&amp;nonce=".urlencode($nonce);
		
		return $this->sendCreamyEmailWithValues($title, $text, $linkURL, $linkTitle, $title, $email);		
	}
	
	/**
	 * Sends a activate user email with a security token to a user. This email will be sent to 
	 * confirm the creation of a new user automatically. The user must click on the verification
	 * link of this email up to 24 hours after the message is sent or it will not be valid.
	 * Once the user clicks the link, their user account will be activated.
	 */
	public function sendAccountActivationEmail($email) {
		// prepare values.
		$lh = \creamy\LanguageHandler::getInstance();
		$dateAsString = date('Y-m-d-H-i-s');
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		// generate a nonce to avoid replay attacks && the password reset code.
		$randomStringGenerator = new \creamy\RandomStringGenerator();
		$nonce = $randomStringGenerator->generate(40);
		// security code
		$securityToken = $this->db->generateEmailSecurityCode($email, $dateAsString, $nonce);
		// build the message.
		$title = $lh->translationFor("activate_account_title");
		$text = $lh->translationFor("activate_account_text");
		$linkTitle = $lh->translationFor("activate_account_title");
		$linkURL = "$baseURL/accountactivation.php?email=".urlencode($email)."&amp;code=".urlencode($securityToken)."&amp;date=".urlencode($dateAsString)."&amp;nonce=".urlencode($nonce);
		
		return $this->sendCreamyEmailWithValues($title, $text, $linkURL, $linkTitle, $title, $email);		
		
	}
	
	/**
	 * Sends an email warning the user that a new task has been assigned to them by another user.
	 * @param String $fromuserid		ID of the user assigning the task to the user
	 * @param String $touserid			ID of the user the task has been assigned to.
	 * @param String $taskDescription	Description of the task assigned to the user.
	 * @return Bool true if the email was successfully sent, false otherwise.
	 */
	public function sendNewTaskMailToUser($fromuserid, $touserid, $taskDescription) {
		$fromuser = $this->db->getDataForUser($fromuserid);
		$touser = $this->db->getDataForUser($touserid);
		if (!isset($fromuser) || !isset($touser)) { return false; }
		if (!isset($touser["email"]) || !isset($fromuser["name"]) || !isset($fromuser["email"])) { return false; }

		// prepare values.
		$lh = \creamy\LanguageHandler::getInstance();
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		// mail parameters
		$title = $lh->translationFor("new_task_title");
		$text = $lh->translationFor("new_task_description");
		$linkURL = "$baseURL/tasks.php";
		$linkTitle = $lh->translationFor("new_task_button");
		// substitute values taskdescription and username in text.
		$text = str_ireplace("taskdescription", $taskDescription, $text);
		$text = str_ireplace("username", $fromuser["name"]." (".$fromuser["email"].")", $text);
		
		return $this->sendCreamyEmailWithValues($title, $text, $linkURL, $linkTitle, $title, $touser["email"]);
	}

	
	/**
	 * Sends an email warning the user of an event for today.
	 * @param Array $event Associative array containing the event data.
	 * @return Bool true if the email was successfully sent, false otherwise.
	 */
	public function sendNewEventMailToUser($event) {
		if ((!isset($event)) || (!isset($event["user_id"]))) { return false; }
		$eventUser = $this->db->getDataForUser($event["user_id"]);
		if (empty($eventUser)) { return false; }

		// prepare values.
		$lh = \creamy\LanguageHandler::getInstance();
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		// mail parameters
		$title = $lh->translationFor("event_for_today");
		$text = $lh->translationFor("you_have_an_event").$event["title"];
		$linkURL = "$baseURL/events.php?initial_date=".urlencode($event["start_date"]);
		$linkTitle = $lh->translationFor("see_event");
		
		return $this->sendCreamyEmailWithValues($title, $text, $linkURL, $linkTitle, $title, $eventUser["email"]);
	}

	
	/**
	 * Sends an email to a Creamy user with the given parameters. The email template is taken from /skins/creamyEmail.html
	 * The function substitutes the title, text, linkURL and linkTitle elements with the parameters.
	 * @param String $title				Title of the message.
	 * @param String $text				Text for the message.
	 * @param String $linkURL			URL for the action link button.
	 * @param String $linkTitle			Title for the action link button.
	 * @param String $emailSubject		A valid RFC 2047 subject. See http://www.faqs.org/rfcs/rfc2047
	 * @param String $emailRecipients	A valid RFC 2822 recipients address set. See http://www.faqs.org/rfcs/rfc2822
	 * @return Bool true if the email was successfully sent, false otherwise.
	 */
	public function sendCreamyEmailWithValues($title, $text, $linkURL, $linkTitle, $emailSubject, $emailRecipients) {
		// 1. grab email contents.
		$htmlContent = file_get_contents(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.CRM_SKEL_DIRECTORY.DIRECTORY_SEPARATOR.CRM_RECOVERY_EMAIL_FILE);
		if (empty($htmlContent)) { return false; }
		// 2. substitute strings
		$htmlContent = str_replace(CRM_MAIL_PARAMETER_TITLE, $title, $htmlContent);
		$htmlContent = str_replace(CRM_MAIL_PARAMETER_TEXT, $text, $htmlContent);
		$htmlContent = str_replace(CRM_MAIL_PARAMETER_LINK_TITLE, $linkTitle, $htmlContent);
		$htmlContent = str_replace(CRM_MAIL_PARAMETER_LINK_URL, $linkURL, $htmlContent);
		// 3. create subject and headers
		$replyEmailAddress = $this->getSystemAdminReplyToEmailAddress();
		$subject = "Password reset link for your Creamy account.";
		$headers = "From: ".$replyEmailAddress."\r\n";
		$headers .= "Reply-To: ".$replyEmailAddress."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		// 4. send email
		return mail($emailRecipients, "Creamy: ".$emailSubject, $htmlContent, $headers);
	}
	
	
	/** Gets the email from the mail system administrator user. */
	protected function getSystemAdminReplyToEmailAddress() {
		$dbAdminData = $this->db->getMainAdminUserData();
		// try to return the main admin email address.
		if (is_array($dbAdminData) && (array_key_exists("email", $dbAdminData))) { return $dbAdminData["email"]; }
		else { // fallback to no-reply.
			return CRM_NO_REPLY_ADMIN_EMAIL_ADDRESS;
		}	
	}
	
	/** 
	 * Sends a mail to the given recipients.
	 * @param String $recipients	A valid RFC 2822 recipients address set. See http://www.faqs.org/rfcs/rfc2822
	 * @param String $subject 		A valid RFC 2047 subject. See http://www.faqs.org/rfcs/rfc2047
	 * @param String $message		Message in HTML or plain text.
	 * @param Array  $attachments	Array of files as received by $_FILES.
	 * @return true if successful, false if email couldn't be sent.
	 */
	public function sendMailWithAttachments($recipients, $subject, $message, $attachments, $attachmentTag = "attachment") {
		// safety checks.		
		require_once('Session.php');
		if (empty($recipients)) { return false; }
		// boundary for this email.
		$boundaryId = md5(uniqid(time()));
		// get from user data.
		$user = \creamy\CreamyUser::currentUser(); if (!isset($user)) { return false; }
		$userData = $this->db->getDataForUser($user->getUserId()); if (!isset($userData)) { return false; }
		$userEmail = isset($userData["email"]) ? $userData["email"] : null; if (!isset($userEmail)) { return false; }
		// build header
		$header = $this->generateMultipartHeaderAndMessageContent($userEmail, $message, $boundaryId);
		$header .= $this->generateAttachmentMultipartFromFiles($attachments, $boundaryId, $attachmentTag);
		// generate a valid header including the attachments.
		return mail($recipients, $subject, null, $header);
	}
	
	/**
	 * Generates a multipart message header, appends the recipients, and the basic message in
	 * HTML format. This header is ready to be appended different attachments by invoking the function
	 * generateAttachmentMultipartFromFiles().
	 * @return String the multipart header and message content
	 */
	protected function generateMultipartHeaderAndMessageContent($from, $message, $boundaryId) {
		$strHeader = "";
		$strHeader .= "From: $from\r\n"; 
		$strHeader .= "MIME-Version: 1.0\n";
		$strHeader .= "Content-Type: multipart/mixed; boundary=\"".$boundaryId."\"\n\n";
		$strHeader .= "This is a multi-part message in MIME format.\n";
		
		$strHeader .= "--".$boundaryId."\n";
		$strHeader .= 'Content-Type: text/html; charset=UTF-8'.PHP_EOL;
		$strHeader .= "Content-Transfer-Encoding: 8bit\n\n";
		$strHeader .= $message."\n\n";
		
		return $strHeader;
	}
	
	/**
	 * Generates a valid form submit multipart with the given files (from $_FILES).
	 * @return String the multipart submit file upload multipart.
	 */
	protected function generateAttachmentMultipartFromFiles($files, $boundaryId, $attachmentTag = "attachment") {
		// no files, empty files.
		if (!is_array($files)) { return ""; }
	
		// process attachments.
		$strHeader = "";
		for($i = 0; $i < count($files[$attachmentTag]["tmp_name"]); $i++) {
	    // Check $files['<nameofinputfile>']['error'] value.
			if (($files[$attachmentTag]["tmp_name"][$i] != "") && ($files[$attachmentTag]['error'][$i] == UPLOAD_ERR_OK)) {
				$strFilesName = $files[$attachmentTag]['name'][$i];
				$strContent = chunk_split(base64_encode(file_get_contents($files[$attachmentTag]["tmp_name"][$i])));
				$strHeader .= "--".$boundaryId."\n";
				$strHeader .= "Content-Type: application/octet-stream; name=\"".$strFilesName."\"\n";
				$strHeader .= "Content-Transfer-Encoding: base64\n";
				$strHeader .= "Content-Disposition: attachment; filename=\"".$strFilesName."\"\n\n";
				$strHeader .= $strContent."\n\n";
			}
		}
		return $strHeader;
	}
}

?>