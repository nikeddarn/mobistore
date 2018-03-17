<?php
/**
 * Sms message data builder
 */

namespace App\Messages;


class SmsMessage
{
    /**
     * @var int Id of message
     */
    public $id = null;

    /**
     * @var string Sender's name
     */
    public $sender = null;

    /**
     * @var string Phone number with leading '+' char
     */
    public $recipient = null;

    /**
     * @var string Date and time to start transmit sms.
     */
    public $beginTransfer = null;

    /**
     * @var string Date and time to end transmit undelivered sms.
     */
    public $endTransfer = null;

    /**
     * @var string Attach link to sms
     */
    public $url = null;

    /**
     * @var string type of sms
     */
    public $type = null;

    /**
     * @var string Text of sms
     */
    public $text = null;

    /**
     * Set message id.
     *
     * @param int $messageId
     * @return SmsMessage
     */
    public function setMessageId(int $messageId)
    {
        $this->id = $messageId;
        return $this;
    }

    /**
     * Set message sender.
     *
     * @param string $sender
     * @return SmsMessage
     */
    public function setSender(string $sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Set recipient.
     *
     * @param string $recipient
     * @return SmsMessage
     */
    public function setRecipient(string $recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * Set begin transfer date and time
     *
     * @param string $beginTransfer
     * @return SmsMessage
     */
    public function setBeginTransfer(string $beginTransfer)
    {
        $this->beginTransfer = $beginTransfer;
        return $this;
    }

    /**
     * Set end transfer date and time
     *
     * @param string $endTransfer
     * @return SmsMessage
     */
    public function setEndTransfer(string $endTransfer)
    {
        $this->endTransfer = $endTransfer;
        return $this;
    }

    /**
     * Set wap push url.
     *
     * @param string $url
     * @return SmsMessage
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set message type.
     *
     * @param string $type
     * @return SmsMessage
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set message text.
     *
     * @param string $text
     * @return SmsMessage
     */
    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }
}