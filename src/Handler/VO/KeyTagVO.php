<?php

namespace App\Handler\VO;

class KeyTagVO
{
    private $key = '';
    private $tag = '';

    public static function create(array $data)
    {
        $self = new self();
        $self->setKey($data['property_key']);
        $self->setTag($data['tag']);

        return $self;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}