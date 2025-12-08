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

namespace BaksDev\Auth\Vk\UseCase\Admin\NewEdit;

use BaksDev\Auth\Vk\Entity\Event\AccountVkEventInterface;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventUid;
use BaksDev\Auth\Vk\UseCase\User\Auth\Active\AccountVkActiveDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\Invariable\AccountVkInvariableDTO;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\Validator\Constraints as Assert;


/** @see AccountVkEvent */
final class AccountVkEditDTO implements AccountVkEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?AccountVkEventUid $id = null;

    /**
     * Идентификатор пользователя
     */
    #[Assert\Uuid]
    private ?UserUid $account = null;

    #[Assert\NotBlank]
    private AccountVkInvariableDTO $invariable;

    /**
     * Active
     */
    #[Assert\NotBlank]
    private AccountVkActiveDTO $active;

    public function getActive(): AccountVkActiveDTO
    {
        return $this->active;
    }

    public function setActive(AccountVkActiveDTO $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getEvent(): ?AccountVkEventUid
    {
        return $this->id;
    }

    /**
     * Account
     */
    public function getAccount(): ?UserUid
    {
        return $this->account;
    }

    public function setAccount(User|UserUid|null $account): self
    {
        $this->account = $account instanceof User ? $account->getId() : $account;
        return $this;
    }

    public function getInvariable(): AccountVkInvariableDTO
    {
        return $this->invariable;
    }

    public function setInvariable(AccountVkInvariableDTO $invariable): self
    {
        $this->invariable = $invariable;
        return $this;
    }

}