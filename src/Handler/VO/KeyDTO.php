<?php

namespace App\Handler\VO;

class KeyDTO
{
    const FIELD_KEY = 'key';
    const FIELD_DATA = 'data';
    const FIELD_TAG = '@tag';

    /** @var string */
    private $originalKey = '';
    /** @var string */
    private $key = '';
    /** @var string */
    private $data = '';
    /** @var string */
    private $type = '';
    /** @var null|string */
    private $tag = null;

    /**
     * @param string $key
     * @param string $type
     * @param $data
     * @return KeyDTO
     */
    public static function create(string $key, string $type, $data): KeyDTO
    {
        $self = new self;
        $self->setOriginalKey($key);
        $self->setType($type);
        $self->setData($data);
        $self->setKey($key . $type);

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'type' => $this->getType(),
            'data' => json_encode($this->getData())
        ];
    }
    /**
     * @return string
     */
    public function getOriginalKey(): string
    {
        return $this->originalKey;
    }

    /**
     * @param string $originalKey
     */
    public function setOriginalKey(string $originalKey): void
    {
        $this->originalKey = $originalKey;
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
        $this->key = sha1($key);
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data): void
    {
        if (is_array($data) && isset($data[self::FIELD_TAG])) {
            $this->setTag($data[self::FIELD_TAG]);
            unset($data[self::FIELD_TAG]);
        }

        $this->data = [
            self::FIELD_KEY => $this->getOriginalKey(),
            self::FIELD_DATA => $data
        ];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $type = mb_strtolower($type);
        $type = str_replace(' ', '_', $type);
        $this->type = trim($type);
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag
     */
    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

}