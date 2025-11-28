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

namespace BaksDev\Auth\Vk\Entity\Event;

use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\Active\AccountVkActive;
use BaksDev\Auth\Vk\Entity\Event\Invariable\AccountVkInvariable;
use BaksDev\Auth\Vk\Entity\Modify\AccountVkModify;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventUid;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* AccountVkEvent */

#[ORM\Entity]
#[ORM\Table(name: 'account_vk_event')]
class AccountVkEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: AccountVkEventUid::TYPE)]
    private AccountVkEventUid $id;

    /**
     * Идентификатор пользователя
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: UserUid::TYPE, nullable: false)]
    private ?UserUid $account = null;

    /**
     * Постоянная величина
     */
    #[ORM\OneToOne(targetEntity: AccountVkInvariable::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private AccountVkInvariable $invariable;

    /**
     * Аккаунт активен
     */
    #[ORM\OneToOne(targetEntity: AccountVkActive::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private AccountVkActive $active;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: AccountVkModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private AccountVkModify $modify;

    public function getModify(): AccountVkModify
    {
        return $this->modify;
    }


    public function __construct()
    {
        $this->id = new AccountVkEventUid();
        $this->modify = new AccountVkModify($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Идентификатор
     */
    public function getId(): AccountVkEventUid
    {
        return $this->id;
    }

    public function setId(AccountVkEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Идентификатор пользователя
     */
    public function setMain(AccountVk|UserUid $account): void
    {
        $this->account = $account instanceof AccountVk ? $account->getId() : $account;
    }

    public function getMain(): ?UserUid
    {
        return $this->account;
    }

    /**
     * Постоянная величина
     */
    public function getInvariable(): AccountVkInvariable
    {
        return $this->invariable;
    }

    /**
     * Аккаунт активен
     */
    public function getActive(): AccountVkActive
    {
        return $this->active;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof AccountVkEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof AccountVkEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
