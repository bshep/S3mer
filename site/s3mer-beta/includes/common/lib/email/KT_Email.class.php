<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

/*
	Copyright (c) InterAKT Online 2000-2006. All rights reserved.
*/

/** 
* This class is sending simple emails; (it was designed to be use internal);
* Currently it is useing the PEAR Email classes;
* @access public
*/
class KT_Email
{
	/**
	 * server used for sending emails;
	 * @var string
	 * @access private
	 */ 
	var	$server;
	
	/**
	 * email server port number;
	 * @var string
	 * @access private
	 */
	var $port;
	
	/**
	 * Username used for sending email
	 * @var string
	 * @access private
	 */
	var $user;
	
	/**
	 * The password used to send email
	 * @var string
	 * @access private
	 */
	var $password;
	
	/**
	 * From address string
	 * @var string
	 * @access private
	 */
	var $from;
	
	/**
	 * To address
	 * @var string
	 * @access private
	 */
	var $to;
	
	/**
	 * copy carbon address
	 * @var string
	 * @access private
	 */
	var $cc;
	
	/**
	 * blind copy carbon address
	 * @var string
	 * @access private
	 */
	var $bcc;
	
	/**
	 * Priority of the message: high, low, normal
	 * @var string
	 * @access private
	 */
	var $priority;
	
	/**
	 * The email subject
	 * @var string
	 * @access private
	 */
	var $subject;
	
	/**
	 * Encoding for the email
	 * @var string
	 * @access private
	 */
	var $encoding;
	
	/**
	 * Text version of the email body
	 * @var string
	 * @access private
	 */
	var $textBody;
	
	/**
	 * HTML version of the email body
	 * @var string
	 * @access private
	 */
	var $htmlBody;
	
	/**
	 * error message to be displayed as User Error
	 * @var array
	 * @access private
	 */
	var $errorType = array();
	
	/**
	 * error message to be displayed as Developer Error
	 * @var string
	 * @access private
	 */
	var $develErrorMessage = array();
	
	/**
	 * end of line character;
	 * @var string
	 * @access private
	 */
	var $KT_CRLF;
	
	/**
	 * filename with absolute paths of the attachements;
         * Only for PRO version	 
	 * @var array
	 * @access private
	 */
	var $attachements = array();
	
	/**
	 * Constructor. Doing nothing.
	 */
	function KT_Email()
	{
		
	}
	
	/**
	 * send the email
	 * @param string $server server name
	 * @param string $port port number
	 * @param string $user username
	 * @param string $password the password
	 * @param string $from from address
	 * @param string $to to address
	 * @param string $cc copy carbon address
	 * @param string $bcc blind copy carbon address
	 * @param string $encoding encoding of the email
	 * @param string $textBody text body of the email
	 * @param string $htmlBody the HTML body of the email
	 * @return string empty on succes, error message on error;
	 * @access public
	 */
	function sendEmail($server, $port, $user, $password, $from, $to, $cc, $bcc, $subject, $encoding, $textBody, $htmlBody)
	{
		$this->server = $server;
		$this->port = $port;
		$this->user	= $user;
		$this->password = $password;
		$this->from = $from;
		$this->to = $to;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->subject = $subject;
		$this->encoding = $encoding;
		if ($textBody=='' && $htmlBody!='') {
			$this->textBody = strip_tags($htmlBody);
		} else {
			$this->textBody = $textBody;
		}
		$this->htmlBody = $htmlBody;
		if ($this->KT_CRLF=='') {
			// fix for different os's line endings
			if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
				$this->KT_CRLF = "\r\n";
			} elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
			 $this->KT_CRLF = "\r";
			} else {
			 $this->KT_CRLF = "\n";
			}
		}
		$auth = true;
		if (strlen(trim($user)) == 0 && strlen(trim($password)) == 0) {
		  $auth = false;
		}
		
		// load the required PEAR classes
		$this->loadPEARclasses();
		
		$mime = new Mail_mime($this->KT_CRLF);
		$mime->setTXTBody($this->getTextBody());
		$mime->setHTMLBody($this->getHtmlBody());
		foreach ($this->attachements as $filename) {
			$mime->addAttachment($filename, function_exists('mime_content_type') ? mime_content_type($filename) : 'application/octet-stream');
		}	
		$body = $mime->get($this->getEncodingSettings());
		$headers = $mime->headers($this->getHeaders());
		
		if ($this->server != '') {
			if ($this->from == '') {
				$this->setError('PHP_EMAIL_FROM_NOT_SET' , array(), array());
				return ;
			}
			$arrSMTPParameters = array(
								'host'=>$server,
								'port'=>$port,
								'auth'=>$auth,
								'username'=>$user,
								'password'=>$password
								); 
			$mail_object = &Mail::factory('smtp', $arrSMTPParameters);
			$headers['To'] = $this->getTo();
			$mailresult	= $mail_object->send($this->getSendToSMTP(), $headers, $body);
		} else {
			$mail_object = &Mail::factory('mail');
			$mailresult	= $mail_object->send($this->getSendTo(), $headers, $body);
		}
		
		if ($mailresult!==true) {
			$this->setError('EMAIL_ERROR_SENDING' , array(), array($mailresult->message));
		}
	}
	
	/**
	 * add an attachement
         * Only for PRO version	 
	 * @var string real path to the filename to be attached
	 * @return nothing
	 * @access public
	 */
	function addAttachment($filename) {
		$this->attachements[] = $filename;
	}
	
	/**
	 * setter. et the priority
	 * @var string $priority priority of the email: high, low, normal
	 * @return nothing
	 * @access public
	 */
	function setPriority($priority='')
	{
		$priority = strtolower($priority);
		switch ($priority) {
			case 'high':
				break;
			case 'low':
				break;
			case 'normal':
			default:
				$priority = 'normal';
				break;
		}
		$this->priority = $priority;
	}
	
	/**
	 * getter. 
	 * @return string the priority of the email: 1 for high, 3 for normal, 5 for low;
	 * @access private
	 */
	function getPriority($textual=0)
	{
		if ($this->priority=='')  {
			$this->setPriority();
		}
		if ($textual) {
			return ucfirst($this->priority);
		} else {
			switch ($this->priority) {
				case 'high':
					$priority = 1;
					break;
				case 'low':
					$priority = 5;
					break;
				case 'normal':
				default:
					$priority = 3;
					break;
			}
			return $priority;
		}
	}
	
	/**
	 * getter
	 * @return string the text body of the email with lines not longer than 900 chars;
	 * @access private
	 */
	function getTextBody()
	{
		return $this->formatMessageLines($this->textBody, 900);
	}
	
	/**
	 * getter
	 * @return string the HTML body of the email with lines not longer than 900 chars;
	 * @access private
	 */
	function getHtmlBody()
	{
		return $this->formatMessageLines($this->htmlBody, 900);	
	}
	
	/**
	 * getter
	 * @return array returns an array with the setting for html_encoding, html_charset, text_charset, head_charset;
	 * @access private
	 */
	function getEncodingSettings()
	{
		$arr = array(
                   'html_encoding' => '8bit',
	                 'html_charset'  => $this->encoding,
	                 'text_charset'  => $this->encoding,
	                 'head_charset'  => $this->encoding
	                );
         return $arr;
	}
	
	/**
	 * getter
	 * @return array return the header;
	 * @access private
	 */
	function getHeaders()
	{
		$arr = array();
		$arr['Subject']	= $this->getSubject();
		$arr['From']	= $this->getFrom();
		// [cor] se pare ca unele servere de mail pun de doua ori adresa to daca o setam si in header si in to de la mail()
		//$arr['To'] = $this->getTo();
		if ($this->getCc()!='') {
			$arr['Cc'] = $this->getCc();
		}
		if ($this->getBcc()!='') {
			$arr['Bcc'] = $this->getBcc();
		}
		$arr['X-Priority'] = $this->getPriority();
		// [cor] se pare ca scad sansele de incadrare ca spam fara asta
		//$arr['X-MSMail-Priority'] = $this->getPriority(1);
		$arr['X-Mailer'] = 'InterAKT tNG mailer';
		return $arr;
	}
	/**
	 * getter
	 * @return string subject;
	 * @access private
	 */
	function getSubject()
	{
		$subj = $this->prepareText($this->subject);
		$subj = str_replace(array("\r", "\n"), array('', ''), $subj);
		return $subj;
	}
	/**
	 * getter
	 * @return string to field;
	 * @access private
	 */
	function getTo()
	{
		return $this->prepareText($this->to);
	}
	/**
	 * getter
	 * @return array to, cc, bcc email addresses;
	 * @access private
	 */
	function getSendToSMTP()
	{
		$ret = array();
		$to = $this->prepareText($this->to);
		$cc = '';
		$bcc = '';
		if ($this->getCc()!='') {
			$cc = $this->getCc();
		}
		if ($this->getBcc()!='') {
			$bcc = $this->getBcc();
		}
		
		if (preg_match('/.*<([^@]+@[^>]+)>.*/', $to, $matches)) {
			$to = $matches[1];
		}
		$ret[] = $to;
		if ($cc != '') {
			if (preg_match('/.*<([^@]+@[^>]+)>.*/', $cc, $matches)) {
				$cc = $matches[1];
			}
			$ret[] = $cc;
		}
		if ($bcc != '') {
			if (preg_match('/.*<([^@]+@[^>]+)>.*/', $bcc, $matches)) {
				$bcc = $matches[1];
			}
			$ret[] = $bcc;
		}
		return $ret;
	}
	
	/**
	 * getter
	 * @return string to email address;
	 * @access private
	 */
	function getSendTo()
	{
		$to = $this->prepareText($this->to);
		if (preg_match('/.*<([^@]+@[^>]+)>.*/', $to, $matches)) {
			$to = $matches[1];
		}
		return $to;
	}
	
	/**
	 * getter
	 * @return string from string;
	 * @access private
	 */
	function getFrom()
	{
		return $this->prepareText($this->from);
	}
	/**
	 * getter
	 * @return string cc;
	 * @access private
	 */
	function getCc()
	{
		$cc = $this->prepareText($this->cc);
		$cc = str_replace(array("\r", "\n"), array('', ''), $cc);
		return $cc;
	}
	/**
	 * getter
	 * @return string bcc;
	 * @access private
	 */
	function getBcc()
	{
		$bcc = $this->prepareText($this->bcc);
		$bcc = str_replace(array("\r", "\n"), array('', ''), $bcc);
		return $bcc;
	}
	/**
	 * Trim the white spaces if starts, ends with white space; 
	 * replace all " occurences with nothing;
	 * @param string $text
	 * @return string ;
	 * @access private
	 */
	function prepareText($text)
	{
		return trim(str_replace('"', "", $text));
	}
	
	/**
	 * Return the message with lines not longer than $chars characters;
	 * @param string $messagetext text to wrap
	 * @param integer $chars number of chars per line;
	 * @return string ;
	 * @access private
	 */
	function formatMessageLines($messagetext, $chars) 
	{
		$messagetext = wordwrap($messagetext, $chars, $this->KT_CRLF);
		return $messagetext;
	}
		
	/**
	 * Setter. set the end of line chars
	 * @var string $Crlf end of line chars;
	 * @return nothing;
	 * @access public
	 */
	function setCrlf($Crlf)
	{
		$this->KT_CRLF = $Crlf;
	}
	
	/**
	 * Loads the PEAR dependecies at runtime.
	 * @return nothing;
	 * @access private
	 */
	function loadPEARclasses() {
		$KT_PEAR_dependecies = array(
			'PEAR' => 'PEAR.php',
			'Mail' => 'Mail.php',
			'Mail_mail' => 'Mail/mail.php',
			'Mail_smtp' => 'Mail/smtp.php',
			'Mail_RFC822' => 'Mail/RFC822.php',
			'Mail_mime' => 'Mail/mime.php',
			'Mail_mimePart' => 'Mail/mimePart.php',
			'Net_SMTP' => 'Net/SMTP.php',
			'Net_Socket' => 'Net/Socket.php',
			'Auth_SASL' => 'Auth/SASL.php',
			'Auth_SASL_Common' => 'Auth/SASL/Common.php',
			'Auth_SASL_Anonymous' => 'Auth/SASL/Anonymous.php',
			'Auth_SASL_Login' => 'Auth/SASL/Login.php',
			'Auth_SASL_Plain' => 'Auth/SASL/Plain.php',
			'Auth_SASL_CramMD5' => 'Auth/SASL/CramMD5.php',
			'Auth_SASL_DigestMD5' => 'Auth/SASL/DigestMD5.php'
		);
		
		foreach ($KT_PEAR_dependecies as $k => $v) {
			if (!class_exists($k)) {
				require_once(PEARDIR . $v);
			}
		}
	}
	
	/**
	 * Setter. set error for developper and user.
	 * @var string $errorCode error message code;
	 * @var array $arrArgsUsr  array with optional parameters for sprintf functions;
	 * @var array $arrArgsDev array with optional parameters for sprintf functions.
	 * @return nothing;
	 * @access private
	 */
	function setError($errorCode, $arrArgsUsr, $arrArgsDev)
	{
		$errorCodeDev = $errorCode;
		if ( !in_array($errorCodeDev, array('', '%s')) ) {
			$errorCodeDev .= '_D';
		}
		if ($errorCode!='') {
      $this->errorType[] = KT_getResource($errorCode, 'Email', $arrArgsUsr);
		} else {
			$this->errorType = array();
		}
		if ($errorCodeDev!='') {
      $this->develErrorMessage[] = KT_getResource($errorCodeDev, 'Email', $arrArgsDev);
		} else {
			$this->develErrorMessage = array();
		}
	}
	
	/**
	 * check if an error was setted.
	 * @return boolean true if error is set or false if not;
	 * @access public
	 */
	function hasError()
	{	
		if (count($this->errorType)>0 || count($this->develErrorMessage)>0) {
			return 1;	
		}	
		return 0;
	}
		
	/**
	 * Getter. 	return the errors setted.
	 * @return array  array - 0=>error for user, 1=>error for developer;
	 * @access public
	 */
	function getError()
	{
		return array(implode('<br />', $this->errorType), implode('<br />', $this->develErrorMessage));	
	}

		
}
?>