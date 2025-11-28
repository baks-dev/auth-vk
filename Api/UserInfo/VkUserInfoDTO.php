<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Auth\Vk\Api\UserInfo;


use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifier;

final readonly class VkUserInfoDTO
{
    public function __construct(
        private string $user_id,
        private ?string $first_name,
        private ?string $last_name,
        private ?string $avatar,
        private ?string $email,
        private ?int $sex,
        private bool $verified,
        private ?string $birthday
    ) {}

    public function getUserId(): VkIdentifier
    {
        return new VkIdentifier($this->user_id);
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

}
