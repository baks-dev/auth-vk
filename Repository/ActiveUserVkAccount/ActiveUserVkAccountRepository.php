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

namespace BaksDev\Auth\Vk\Repository\ActiveUserVkAccount;

use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\Entity\Event\Active\AccountVkActive;
use BaksDev\Auth\Vk\Entity\Event\Invariable\AccountVkInvariable;
use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifier;
use BaksDev\Core\Doctrine\DBALQueryBuilder;


final readonly class ActiveUserVkAccountRepository implements ActiveUserVkAccountInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    public function findByVkId(VkIdentifier $vkid): ActiveUserVkAccountResult|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->addSelect('invariable.account as account')
            ->from(AccountVkInvariable::class, 'invariable')
            ->where('invariable.vkid = :vkid')
            ->setParameter('vkid', $vkid, VkIdentifier::TYPE);

        $dbal
            ->leftJoin(
                'invariable',
                AccountVkEvent::class,
                'event',
                'event.id = invariable.event'
            );

        $dbal
            ->addSelect('account_vk_active.active')
            ->join(
                'event',
                AccountVkActive::class,
                'account_vk_active',
                'account_vk_active.event = event.id'
            );

        return $dbal->fetchHydrate(ActiveUserVkAccountResult::class);
    }

}