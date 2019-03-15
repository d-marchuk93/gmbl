<?php

namespace App\Handler\VO;

use App\Exception\Fetcher\InvalidData;

class KeyValueVO
{
    /** @var string */
    private $key = '';
    /** @var string */
    private $data = '';
    /** @var string */
    private $type = '';
    /** @var string */
    private $originalKey = '';
    /** @var array */
    private $tags = [];

    public static function createForFetch(array $data): KeyValueVO
    {
        if (!isset($data['data'])) {
            throw new InvalidData();
        }
        if (!isset($data['key'])) {
            throw new InvalidData();
        }
        if (!isset($data['type'])) {{
            throw new InvalidData();
        }}
        $value = json_decode($data['data'], true);
        if (!isset($value[KeyDTO::FIELD_DATA])) {
            throw new InvalidData();
        }
        $self = new self;
        $self->setKey((string)$data['key']);
        $self->setType((string)$data['type']);
        $self->setData($value[KeyDTO::FIELD_DATA]);
        $self->setOriginalKey($value[KeyDTO::FIELD_KEY]);

        if (isset($data['tag'])) {
            $tags = explode(',', $data['tag']);
            $tagsToGet = [];
            foreach ($tags as $tag) {
                $tagsToGet[] = KeyTagVO::create([
                    'property_key' => $data['key'],
                    'tag' => $tag
                ]);
            }

            $self->setTags($tagsToGet);
        }

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
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
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
        $this->type = $type;
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
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}