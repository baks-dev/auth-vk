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

namespace BaksDev\Auth\Vk\Repository\AllAccountVk;

use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifier;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventUid;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;

final readonly class AllAccountVkResult
{
    public function __construct(
        private string $id,
        private string $event,
        private bool $vk_status,
        private string $vk_update,
        private string $vk_user_id,
        private ?string $username,
    ) {}

    public function getId(): UserUid
    {
        return new UserUid ($this->id);
    }

    public function getEvent(): AccountVkEventUid {
        return new AccountVkEventUid($this->event);
    }

    public function getVkStatus(): bool
    {
        return $this->vk_status;
    }

    public function getVkUpdate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->vk_update);
    }

    public function getVkUserId(): VkIdentifier
    {
        return new VkIdentifier($this->vk_user_id);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

}