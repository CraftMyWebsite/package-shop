<?php

namespace CMW\Entity\Shop;

class ShopConfigMail
{
    private ?string $replyMail;
    private ?string $object;
    private ?string $content;
    private ?string $type;
    private ?string $lastUpdate;

    /**
     * @param string|null $replyMail
     * @param string|null $object
     * @param string|null $content
     * @param string|null $type
     * @param string|null $lastUpdate
     */
    public function __construct(?string $replyMail, ?string $object, ?string $content, ?string $type, ?string $lastUpdate)
    {
        $this->replyMail = $replyMail;
        $this->object = $object;
        $this->content = $content;
        $this->type = $type;
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return string|null
     */
    public function getReplyMail(): ?string
    {
        return $this->replyMail;
    }

    /**
     * @return string|null
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getLastUpdate(): ?string
    {
        return $this->lastUpdate;
    }

}