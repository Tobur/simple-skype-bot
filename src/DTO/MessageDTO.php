<?php

namespace SimpleSkypeBot\DTO;

class MessageDTO
{
    /**
     * @var null|string
     */
    protected $text;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $serviceUrl;

    /**
     * @var null|string
     */
    protected $fromId;

    /**
     * @var null|string
     */
    protected $fromName;

    /**
     * @var null|string
     */
    protected $recipientId;

    /**
     * @var null|string
     */
    protected $recipientName;

    /**
     * @var null|string
     */
    protected $conversationId;

    /**
     * @var string
     */
    protected $channelId;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (empty($data)) {
            return;
        }

        $this->id = $data['id'];
        $this->text = $data['text'];
        $this->type = $data['type'];
        $this->fromId = $data['from']['id'];
        $this->fromName = $data['from']['name'];
        $this->serviceUrl = $data['serviceUrl'];
        $this->conversationId = $data['conversation']['id'];
        $this->text = $data['text'];
        $this->channelId = $data['channelId'];
        $this->recipientId = $data['recipient']['id'];
        $this->recipientName = $data['recipient']['name'];
    }

    /**
     * @return null|string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getServiceUrl(): ?string
    {
        return $this->serviceUrl;
    }

    /**
     * @return null|string
     */
    public function getFromId(): ?string
    {
        return $this->fromId;
    }

    /**
     * @return null|string
     */
    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    /**
     * @return null|string
     */
    public function getConversationId(): ?string
    {
        return $this->conversationId;
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @param null|string $recipientId
     * @return MessageDTO
     */
    public function setRecipientId($recipientId): self
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRecipientName()
    {
        return $this->recipientName;
    }

    /**
     * @param null|string $recipientName
     * @return MessageDTO
     */
    public function setRecipientName($recipientName): self
    {
        $this->recipientName = $recipientName;

        return $this;
    }

    /**
     * @param null|string $text
     * @return MessageDTO
     */
    public function setText($text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param null|string $type
     * @return MessageDTO
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param null|string $id
     * @return MessageDTO
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param null|string $serviceUrl
     * @return MessageDTO
     */
    public function setServiceUrl($serviceUrl): self
    {
        $this->serviceUrl = $serviceUrl;

        return $this;
    }

    /**
     * @param null|string $fromId
     * @return MessageDTO
     */
    public function setFromId($fromId): self
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * @param null|string $fromName
     * @return MessageDTO
     */
    public function setFromName($fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @param null|string $conversationId
     * @return MessageDTO
     */
    public function setConversationId($conversationId): self
    {
        $this->conversationId = $conversationId;

        return $this;
    }
}

