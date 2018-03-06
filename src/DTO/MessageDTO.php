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
    protected $conversationId;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->text = $data['text'];
        $this->type = $data['type'];
        $this->fromId = $data['from']['id'];
        $this->fromName = $data['from']['name'];
        $this->serviceUrl = $data['serviceUrl'];
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
}

