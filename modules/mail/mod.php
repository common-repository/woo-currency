<?php
class mailWcu extends moduleWcu {
	private $_smtpMailer = null;
	private $_sendMailMailer = null;
	
	public function init() {
		parent::init();

		//dispatcherWcu::addFilter('optionsDefine', array($this, 'addOptions'));
	}
	public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$type = frameWcu::_()->getModule('options')->get('mail_send_engine');
		$res = false;
		switch($type) {
			case 'smtp':
				$res = $this->sendSmtpMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				break;
			case 'sendmail':
				$res = $this->sendSendMailMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				break;
			case 'wp_mail': default:
				$res = $this->sendWpMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				if(!$res) {
					// Sometimes it return false, but email was sent, and in such cases 
					// - in errors array there are only one - empty string - value.
					// Let's count this for now as Success sending
					$mailErrors = array_filter( $this->getMailErrors() );
					if(empty($mailErrors)) {
						$res = true;
					}
				}
				break;
		}
		return $res;
	}
	private function _getSmtpMailer() {
		if(!$this->_smtpMailer) {
			$this->_connectPhpMailer();
			$this->_smtpMailer = new PHPMailer();  // create a new object
			$this->_smtpMailer->IsSMTP(); // enable SMTP
			$this->_smtpMailer->Debugoutput = array($this, 'pushPhpMailerError');
			$this->_smtpMailer->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
			$this->_smtpMailer->SMTPAuth = true;  // authentication enabled
			$smtpSecure = frameWcu::_()->getModule('options')->get('smtp_secure');
			if(!empty($smtpSecure)) {
				$this->_smtpMailer->SMTPSecure = $smtpSecure; // secure transfer enabled REQUIRED for GMail
			}
			$this->_smtpMailer->Host = trim(frameWcu::_()->getModule('options')->get('smtp_host'));
			$this->_smtpMailer->Port = trim(frameWcu::_()->getModule('options')->get('smtp_port'));
			$this->_smtpMailer->Username = trim(frameWcu::_()->getModule('options')->get('smtp_login'));
			$this->_smtpMailer->Password = trim(frameWcu::_()->getModule('options')->get('smtp_pass'));
		}
		return $this->_smtpMailer;
	}
	public function pushPhpMailerError( $errorStr ) {
		if(strpos($errorStr, 'SMTP ERROR') !== false) {
			$this->pushError( $errorStr );
		}
	}
	private function _getSendMailMailer() {
		if(!$this->_sendMailMailer) {
			$this->_connectPhpMailer();
			$this->_sendMailMailer = new PHPMailer();  // create a new object
			$this->_sendMailMailer->isSendmail(); // enable SendMail
			$sendMailPath = trim(frameWcu::_()->getModule('options')->get('sendmail_path'));
			if(!empty($sendMailPath)) {
				$this->_sendMailMailer->Sendmail = $sendMailPath;
			}
		}
		return $this->_sendMailMailer;
	}
	private function _connectPhpMailer() {
		if(!function_exists('PHPMailerAutoload')) {
			require_once( $this->getModDir(). 'engines'. DS. 'PHPMailerAutoload.php');
		}
	}
	public function sendSendMailMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$this->_getSendMailMailer();
		if($fromEmail && $fromName) {
			$this->_sendMailMailer->setFrom($fromEmail, $fromName);
		}
		if($replyToName || $replyToEmail) {
			$this->_sendMailMailer->addReplyTo($replyToName, $replyToEmail);
        }
		/*if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_sendMailMailer->ReturnPath = $params['return_path_email'];
        }*/
		$this->_sendMailMailer->Subject = $subject;
		$this->_sendMailMailer->addAddress($to);
		if(frameWcu::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_sendMailMailer->Body = $message;
		} else {
			$this->_sendMailMailer->msgHTML( $message );
		}
		if($this->_sendMailMailer->send()) {
			return true;
		} else {
			$this->pushError( 'Mail error: '.$this->_sendMailMailer->ErrorInfo );
		}
		return false;
	}
	public function sendSmtpMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$this->_getSmtpMailer();
		if($fromEmail && $fromName) {
			$this->_smtpMailer->setFrom($fromName, $fromName);
		}
		if($replyToName || $replyToEmail) {
			$this->_smtpMailer->addReplyTo($replyToName, $replyToEmail);
        }
		/*if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_smtpMailer->ReturnPath = $params['return_path_email'];
        }*/
		$this->_smtpMailer->Subject = $subject;
		$this->_smtpMailer->addAddress($to);
		if(frameWcu::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_smtpMailer->Body = $message;
		} else {
			$this->_smtpMailer->msgHTML( $message );
		}
		if($this->_smtpMailer->send()) {
			return true;
		} else {
			$this->pushError( 'Mail error: '.$this->_smtpMailer->ErrorInfo );
		}
		return false;
	}
	public function sendWpMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$headersArr = array();
		$eol = "\r\n";
        if(!empty($fromName) && !empty($fromEmail)) {
            $headersArr[] = 'From: '. $fromName. ' <'. $fromEmail. '>';
        }
		if(!empty($replyToName) && !empty($replyToEmail)) {
            $headersArr[] = 'Reply-To: '. $replyToName. ' <'. $replyToEmail. '>';
        }
		if(!function_exists('wp_mail'))
			frameWcu::_()->loadPlugins();
		if(!frameWcu::_()->getModule('options')->get('disable_email_html_type')) {
			add_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}
		
		$attach = null;
		if(isset($additionalParameters['attach']) && !empty($additionalParameters['attach'])) {
			$attach = $additionalParameters['attach'];
		}
		if(empty($attach)) {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		} else {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr), $attach);
		}
		if(!frameWcu::_()->getModule('options')->get('disable_email_html_type')) {
			remove_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}

		return $result;
	}
	public function getMailErrors() {
		global $ts_mail_errors;
		global $phpmailer;
		$type = frameWcu::_()->getModule('options')->get('mail_send_engine');
		switch($type) {
			case 'smtp': case 'sendmail':
				return $this->getErrors();
				break;
			case 'wp_mail': default:
				// Clear prev. send errors at first
				$ts_mail_errors = array();

				// Let's try to get errors about mail sending from WP
				if (!isset($ts_mail_errors)) $ts_mail_errors = array();
				if (isset($phpmailer)) {
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
				if(empty($ts_mail_errors)) {
					$ts_mail_errors[] = __('Cannot send email - problem with send server', WCU_LANG_CODE);
				}
				return $ts_mail_errors;
				break;
		}
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Mail', WCU_LANG_CODE),
			'opts' => array(
				'mail_function_work' => array('label' => __('Mail function tested and work', WCU_LANG_CODE), 'desc' => ''),
				'notify_email' => array('label' => __('Notify Email', WCU_LANG_CODE), 'desc' => __('Email address used for all email notifications from plugin', WCU_LANG_CODE), 'html' => 'text', 'def' => get_option('admin_email')),
			),
		);
		return $opts;
	}
}