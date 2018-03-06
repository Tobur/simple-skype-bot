<?php

namespace SimpleSkypeBot\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class SkypeUser
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
     * @ORM\Column(type="string", name="skype_name", nullable=true)
     * @var string|null
     */
    protected $skypeName;

    /**
     * @ORM\Column(type="string", name="skype_login", nullable=true, unique=true)
     * @var string|null
     */
    protected $skypeLogin;

    /**
     * @ORM\Column(type="string", name="skype_login_id", nullable=true, unique=true)
     * @var string|null
     */
    protected $skypeLoginId;

    /**
     * @ORM\Column(type="string", name="conversation_id", nullable=false)
     * @var string|null
     */
    protected $conversationId;

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
    public function getSkypeName(): ?string
    {
        return $this->skypeName;
    }

    /**
     * @param null|string $skypeName
     * @return SkypeUser
     */
    public function setSkypeName(?string $skypeName): SkypeUser
    {
        $this->skypeName = $skypeName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSkypeLogin(): ?string
    {
        return $this->skypeLogin;
    }

    /**
     * @param null|string $skypeLogin
     * @return SkypeUser
     */
    public function setSkypeLogin(?string $skypeLogin): SkypeUser
    {
        $this->skypeLogin = $skypeLogin;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSkypeLoginId(): ?string
    {
        return $this->skypeLoginId;
    }

    /**
     * @param null|string $skypeLoginId
     * @return SkypeUser
     */
    public function setSkypeLoginId(?string $skypeLoginId): SkypeUser
    {
        $this->skypeLoginId = $skypeLoginId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConversationId(): ?string
    {
        return $this->conversationId;
    }

    /**
     * @param null|string $conversationId
     * @return SkypeUser
     */
    public function setConversationId(?string $conversationId): SkypeUser
    {
        $this->conversationId = $conversationId;

        return $this;
    }
}

