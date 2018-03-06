<?php

namespace SimpleSkypeBot\DTO;

class CoversationDTO
{
    /**
     * @var string
     */
    protected $conversationId;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int|string
     */
    protected $expiresIn;

    /**
     * @var string
     */
    protected $streamUrl;

    /**
     * @var string
     */
    protected $referenceGrammarId;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->expiresIn = $data['expires_in'];
        $this->token = $data['token'];
        $this->conversationId = $data['conversationId'];
        $this->streamUrl = $data['streamUrl'];
        $this->referenceGrammarId = $data['referenceGrammarId'];
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return int|string
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @return string
     */
    public function getStreamUrl(): string
    {
        return $this->streamUrl;
    }

    /**
     * @return string
     */
    public function getReferenceGrammarId(): string
    {
        return $this->referenceGrammarId;
    }
}

