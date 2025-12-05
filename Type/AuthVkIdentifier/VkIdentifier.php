<?php

namespace BaksDev\Auth\Vk\Type\AuthVkIdentifier;

final class VkIdentifier
{
    public const string TYPE = 'vk_id_type';

    public const string TEST = '196591820';

    public function __construct(
        private VkIdentifier|int|string|null $value,
    )
    {
        if(is_string($value) || is_int($value))
        {
            $value = (string) $value;
        }

        if($value instanceof self)
        {
            $value = $value->getValue();
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }


    public function getValue(): string
    {
        if(empty($this->value))
        {
            return '';
        }

        return $this->value;
    }
}