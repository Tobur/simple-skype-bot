<?php

namespace SimpleSkypeBot\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class SkypeToken
{
    const API_TYPE_AUTH2 = 'auth2';
    const API_TYPE_DIRECT_LINE = 'direct_line';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="token_type", nullable=false)
     * @var string|null
     */
    protected $tokenType = 'Bearer';

    /**
     * @ORM\Column(type="datetime", name="expires_in", nullable=false)
     * @var \DateTime
     */
    protected $expiresIn;

    /**
     * @ORM\Column(type="text", name="access_token", nullable=false)
     * @var string|null
     */
    protected $accessToken;

    /**
     * @ORM\Column(type="string", name="api_type", nullable=false)
     * @var string|null
     */
    protected $apiType;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * @param null|string $tokenType
     */
    public function setTokenType(?string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresIn(): ?\DateTime
    {
        return $this->expiresIn;
    }

    /**
     * @param \DateTime $expiresIn
     */
    public function setExpiresIn(\DateTime $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return null|string
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @param null|string $accessToken
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return null|string
     */
    public function getApiType(): ?string
    {
        return $this->apiType;
    }

    /**
     * @param null|string $apiType
     */
    public function setApiType(?string $apiType): void
    {
        $this->apiType = $apiType;
    }
}

