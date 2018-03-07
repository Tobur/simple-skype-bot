<?php

namespace SimpleSkypeBot\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class SkypeToken
{
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
}

