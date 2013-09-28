<?php
/**
 * Copyright (c) 2010 Richard Castera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without li`ation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace rcastera\Email;

class Mailer
{
    /**
     * Carriage return, new line.
     *
     * @var string
     */
    const EOL = "\r\n";

    /**
     * Delimiters.
     *
     * @var string
     */
    private $boundary = '';

    /**
     * Who the email is going to.
     *
     * @var array
     */
    private $to = array();

    /**
     * Carbon Copy.
     *
     * @var array
     */
    private $cc = array();

    /**
     * Blind Carbon Copy.
     *
     * @var array
     */
    private $bcc = array();

    /**
     * Who the email is from.
     *
     * @var string
     */
    private $from = '';

    /**
     * Subject of the email.
     *
     * @var string
     */
    private $subject = '';

    /**
     * Body of the email.
     *
     * @var string
     */
    private $body = '';

    /**
     * Priority of the email.
     *
     * @var integer
     */
    private $priority = 3;

    /**
     * Attachments of the email.
     *
     * @var array
     */
    private $attachments = array();

    /**
     * Headers of the email.
     *
     * @var string
     */
    private $headers = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->boundary = '----=_NextPart_' . md5(rand());
    }

    /**
     * Destructor.
     **/
    public function __destruct()
    {
        unset($this);
    }

    /**
     * Set who the email is going to.
     *
     * @param string/array $to
     *
     * @return $this
     */
    public function to($to = '')
    {
        if (is_array($to)) {
            foreach ($to as $recipient) {
                array_push($this->to, $this->clean($recipient));
            }
        } else {
            array_push($this->to, $this->clean($to));
        }
        return $this;
    }

    /**
     * Set who gets a carbon copy of this email.
     *
     * @param string/array $cc
     *
     * @return $this
     */
    public function cc($cc = '')
    {
        if (is_array($cc)) {
            foreach ($cc as $carbonCopy) {
                array_push($this->cc, $this->clean($carbonCopy));
            }
        } else {
            array_push($this->cc, $this->clean($cc));
        }
        return $this;
    }

    /**
     * Set who gets a blind carbon copy of this email.
     *
     * @param string/array $bcc
     *
     * @return $this
     */
    public function bcc($bcc = '')
    {
        if (is_array($bcc)) {
            foreach ($bcc as $blindCopy) {
                array_push($this->bcc, $this->clean($blindCopy));
            }
        } else {
            array_push($this->bcc, $this->clean($bcc));
        }
        return $this;
    }

    /**
     * Set who the email is from.
     *
     * @param string $fromName - name of the person who is sending the email.
     * @param string $fromEmail - email of the person who is sending the email.
     *
     * @return $this
     */
    public function from($fromName = '', $fromEmail = '')
    {
        if (! empty($fromName) && ! empty($fromEmail)) {
            $this->from = (string)($this->clean($fromName) . '<' . $this->clean($fromEmail) . '>');
        } else {
            $this->from = $this->clean($fromEmail);
        }
        return $this;
    }

    /**
     * Sets the subject of the email.
     *
     * @param string $subject
     *
     * @return $this
     */
    public function subject($subject = '')
    {
        $this->subject = $this->clean($subject);
        return $this;
    }

    /**
     * Set the body of the message.
     *
     * @param string
     *
     * @return $this
     */
    public function body($body = '')
    {
        $this->body = $this->clean($body);
        return $this;
    }

    /**
     * Set the priority of the email.
     *
     * @param integer - accepts 1 or 3.
     *
     * @return $this
     */
    public function priority($priority = 3)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Attach files to the email.
     *
     * @param array - A list of files to attach to the email.
     *
     * @return $this
     */
    public function attach($files = array())
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                array_push($this->attachments, $file);
            }
        } else {
            array_push($this->attachments, $files);
        }
        return $this;
    }

    /**
     * Clean the parameter.
     *
     * @param string
     *
     * @return Clean.
     */
    private function clean($param)
    {
        return trim($param);
    }

    /**
     * Gets and sets various header information for the email.
     */
    private function getHeaders()
    {
        $this->headers = 'From: ' . $this->from . self::EOL;
        $this->headers .= 'Reply-To: ' . $this->from . self::EOL;
        $this->headers .= 'Return-Path: ' . $this->from . self::EOL;
        $this->headers .= 'X-Mailer: PHP/' . phpversion() . self::EOL;
        $this->headers .= 'MIME-Version: 1.0' . self::EOL;
        $this->headers .= 'Content-Type: multipart/mixed; boundary="' . $this->boundary . '"' . self::EOL;

        // Setup Carbon Copy
        if (! empty($this->cc)) {
            $this->headers .= 'Cc: ' . implode(',', $this->cc) . self::EOL;
        }

        // Setup Blind Carbon Copy
        if (! empty($this->bcc)) {
            $this->headers .= 'Bcc: ' . implode(',', $this->bcc) . self::EOL;
        }

        // Get priority.
        $this->headers .= $this->getPriority();
    }

    /**
     * Gets and sets the message information for the email.
     */
    private function getMessage()
    {
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
    private function getAttachments()
    {
        foreach ($this->attachments as $attachment) {
            $filename = basename($attachment);

            if (! file_exists($attachment)) {
                throw new \Exception("Attachment <{$filename}> does not exist!");
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
     *
     * @return string
     */
    private function getPriority()
    {
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
     *
     * @return boolean
     */
    public function send()
    {
        if (empty($this->to) || empty($this->from) || empty($this->subject) || empty($this->body)) {
            return false;
        }

        $this->getHeaders();
        $this->getMessage();
        $this->getAttachments();

        // Mail it.
        return mail(implode(',', $this->to), $this->subject, $this->body, $this->headers);
    }
}
