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

namespace BaksDev\Auth\Vk\UseCase\User\Auth;

use BaksDev\Auth\Vk\Entity\Event\AccountVkEventInterface;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventUid;
use BaksDev\Auth\Vk\UseCase\User\Auth\Active\AccountVkActiveDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\Invariable\AccountVkInvariableDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class VkAuthDTO implements AccountVkEventInterface
{

    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?AccountVkEventUid $id = null;

    public function setEvent(?AccountVkEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    private AccountVkActiveDTO $active;

    private ?AccountVkInvariableDTO $invariable;

    public function getEvent(): ?AccountVkEventUid
    {
        return $this->id;
    }


    public function getActive(): AccountVkActiveDTO
    {
        return $this->active;
    }

    public function setActive(AccountVkActiveDTO $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getInvariable(): ?AccountVkInvariableDTO
    {
        return $this->invariable;
    }

    public function setInvariable(?AccountVkInvariableDTO $invariable): self
    {
        $this->invariable = $invariable;
        return $this;
    }

}