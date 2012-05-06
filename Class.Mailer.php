<?php
 /**
  * Mailer - A lightweight email class.
  * @copyright Richard Castera 2009 © Copyright.
  * @license GNU LESSER GENERAL Public LICENSE
  */

class Mailer {
  /**
   * Carriage return, new line.
   * @var String
   */
  const EOL = "\r\n";

  /**
   * Delimiters.
   * @var String
   */
  private $boundary = '';

  /**
   * Who the email is going to.
   * @var Array
   */
  private $to = array(); 
  
  /**
   * Carbon Copy.
   * @var Array
   */
  private $cc = array();
  
  /**
   * Blind Carbon Copy.
   * @var Array
   */
  private $bcc = array();
  
  /**
   * Who the email is from.
   * @var String
   */
  private $from = '';
  
  /**
   * Subject of the email.
   * @var String
   */
  private $subject = '';
  
  /**
   * Body of the email.
   * @var String
   */
  private $body = '';

  /**
   * Priority of the email.
   * @var Integer
   */
  private $priority = 3;

  /**
   * Attachments of the email.
   * @var Array
   */
  private $attachments = array();
  
  /**
   * Headers of the email.
   * @var String
   */
  private $headers = '';

  /**
   * Constructor.
   */ 
  public function __construct() {
    $this->boundary = '----=_NextPart_' . md5(rand());
  }
  
  /**
   * Destructor.
   **/
  public function __destruct() {
    unset($this);
  }
  
  /**
   * Set who the email is going to.
   * @param Mixed $to - Accepts an array or string.
   * @return $this.
   */
  public function to($to = '') {
    if (is_array($to)) {
      foreach ($to as $recipient) {
        array_push($this->to, $this->clean($recipient));
      }
    }
    else {
      array_push($this->to, $this->clean($to)); 
    }
    return $this;
  }

  /**
   * Set who gets a carbon copy of this email.
   * @param Mixed $cc - Accepts an array or string.
   * @return $this.
   */
  public function cc($cc = '') {
    if (is_array($cc)) {
      foreach ($cc as $carbonCopy) {
        array_push($this->cc, $this->clean($carbonCopy));
      }
    }
    else {
      array_push($this->cc, $this->clean($cc)); 
    }
    return $this;
  }

  /**
   * Set who gets a blind carbon copy of this email.
   * @param Mixed $bcc - Accepts an array or string.
   * @return $this.
   */
  public function bcc($bcc = '') {
    if (is_array($bcc)) {
      foreach ($bcc as $blindCopy) {
        array_push($this->bcc, $this->clean($blindCopy));
      }
    }
    else {
      array_push($this->bcc, $this->clean($bcc)); 
    }
    return $this;
  }

  /**
   * Set who the email is from.
   * @param String $fromName - Name of the person who is sending the email.
   * @param String $fromEmail - Email of the person who is sending the email.
   * @return $this.
   */
  public function from($fromName = '', $fromEmail = '') {
    if (!empty($fromName) && !empty($fromEmail)) {
      $this->from = (string)($this->clean($fromName) . '<' . $this->clean($fromEmail) . '>');
    }
    else {
      $this->from = $this->clean($fromEmail);
    }
    return $this;
  }

  /**
   * Sets the subject of the email.
   * @param Mixed $subject - Accepts a string for the subject.
   * @return $this.
   */
  public function subject($subject = '') {
    $this->subject = $this->clean($subject);
    return $this;
  }

  /**
   * Set the body of the message.
   * @param String - The body of the email.
   * @return $this. 
   */
  public function body($body = '') {
    $this->body = $this->clean($body);
    return $this;
  }

  /**
   * Set the priority of the email.
   * @param Integer - Accepts 1 or 3.
   * @return $this.
   */
  public function priority($priority = 3) {
    $this->priority = $priority;
    return $this;
  }

  /**
   * Attach files to the email.
   * @param Array - A list of files to attach to the email.
   * @return $this.
   */
  public function attach($files = array()) {
    if (is_array($files)) {
      foreach ($files as $file) {
        array_push($this->attachments, $file);
      }
    }
    else {
      array_push($this->attachments, $files);
    }
    return $this;
  }

  /**
   * Clean the parameter.
   * @param Mixed
   * @return Clean.
   */
  private function clean($param) {
    return trim($param);
  }

  /**
   * Gets and sets various header information for the email.
   */
  private function getHeaders() {
    $this->headers = 'From: ' . $this->from . self::EOL;
    $this->headers .= 'Reply-To: ' . $this->from . self::EOL;
    $this->headers .= 'Return-Path: ' . $this->from . self::EOL;
    $this->headers .= 'X-Mailer: PHP/' . phpversion() . self::EOL;
    $this->headers .= 'MIME-Version: 1.0' . self::EOL;
    $this->headers .= 'Content-Type: multipart/mixed; boundary="' . $this->boundary . '"' . self::EOL;
    
    // Setup Carbon Copy
    if (!empty($this->cc)) {
      $this->headers .= 'Cc: ' . implode(',', $this->cc) . self::EOL;
    }
    
    // Setup Blind Carbon Copy
    if (!empty($this->bcc)) {
      $this->headers .= 'Bcc: ' . implode(',', $this->bcc) . self::EOL;
    }

    // Get priority.
    $this->headers .= $this->getPriority();
  }

  /**
   * Gets and sets the message information for the email.
   */
  private function getMessage() {
    $message = $this->body;

    $this->body  = '--' . $this->boundary . self::EOL;
    $this->body .= 'Content-Type: multipart/alternative; boundary="' . $this->boundary . '_alt"' . self::EOL;
    $this->body .= '--' . $this->boundary . '_alt' . self::EOL;
    $this->body .= 'Content-Type: text/html; charset="utf-8"' . self::EOL; 
    $this->body .= 'Content-Transfer-Encoding: base64' . self::EOL;
    $this->body .= chunk_split(base64_encode($message)); 
    $this->body .= '--' . $this->boundary . '_alt--' . self::EOL; 
  }

  /**
   * Gets and sets any attachments for the email.
   */
  private function getAttachments() {
    foreach ($this->attachments as $attachment){
      $filename = basename($attachment);
      if (!file_exists($attachment)){
        trigger_error("Attachment <{$filename}> does not exist!", E_USER_ERROR);
      }
      
      $handle = fopen($attachment, 'r');
      $content = fread($handle, filesize($attachment));
      fclose($handle);
      
      $this->body .= '--' . $this->boundary . self::EOL;
      $this->body .= 'Content-Type: application/octetstream' . self::EOL;
      $this->body .= 'Content-Transfer-Encoding: base64' . self::EOL;
      $this->body .= 'Content-Disposition: attachment; filename="' . $filename . '"' . self::EOL;
      $this->body .= 'Content-ID: <' . $filename . '>' . self::EOL . self::EOL;
      $this->body .= chunk_split(base64_encode($content));
    }
  }

  /**
   * Gets and sets the priority for the email.
   * @return String - Priority information.
   */
  private function getPriority() {
    switch ($this->priority) {
      // Urgent
      case 1:
        $this->priority = 'X-Priority: 1' . self::EOL;
        $this->priority .= 'X-MSMail-Priority: High' . self::EOL;
        $this->priority .= 'Importance: High' . self::EOL;
        break;
          
      // Normal
      case 3:
        $this->priority = 'X-Priority: 3' . self::EOL;
        $this->priority .= 'X-MSMail-Priority: Normal' . self::EOL;
        $this->priority .= 'Importance: Normal' . self::EOL;
        break;
        
      // Default level of normal if option not chosen.
      default:
        $this->priority = 'X-Priority: 3' . self::EOL;
        $this->priority .= 'X-MSMail-Priority: Normal' . self::EOL;
        $this->priority .= 'Importance: Normal' . self::EOL;
        break;
    }
    
    return $this->priority;
  }

  /**
   * Sends an email.
   * @return Boolean True if the email was sent, false if not.
   */ 
  public function send() {
    if (empty($this->to) || empty($this->from) || empty($this->subject) || empty($this->body)) {
      return FALSE; 
    }

    $this->getHeaders();
    $this->getMessage();
    $this->getAttachments();

    // Mail.
    return mail(implode(',', $this->to), $this->subject, $this->body, $this->headers);
  }
}
