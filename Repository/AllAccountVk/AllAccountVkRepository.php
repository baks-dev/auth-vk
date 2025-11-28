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

namespace BaksDev\Auth\Vk\Repository\AllAccountVk;


use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\Entity\Event\Active\AccountVkActive;
use BaksDev\Auth\Vk\Entity\Event\Invariable\AccountVkInvariable;
use BaksDev\Auth\Vk\Entity\Modify\AccountVkModify;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\Event\UserProfileEvent;


final class AllAccountVkRepository implements AllAccountVkInterface
{

    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    /** Метод возвращает paginator */
    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('vk.id ')
            ->addSelect('vk.event ')
            ->from(AccountVk::class, 'vk');


        $dbal
            ->leftJoin(
                'vk',
                AccountVkEvent::class,
                'vk_event',
                'vk_event.id = vk.event'
            );

        $dbal
            ->addSelect('vk_active.active AS vk_status')
            ->leftJoin(
                'vk',
                AccountVkActive::class,
                'vk_active',
                'vk_active.event = vk.event'
            );

        $dbal
            ->addSelect('vk_invariable.vkid AS vk_user_id')
            ->leftJoin(
                'vk',
                AccountVkInvariable::class,
                'vk_invariable',
                'vk_invariable.event = vk.event'
            );

        $dbal
            ->addSelect('vk_modify.mod_date AS vk_update')
            ->leftJoin(
                'vk',
                AccountVkModify::class,
                'vk_modify',
                'vk_modify.event = vk.event'
            );

        $dbal->leftJoin(
            'vk',
            UserProfileInfo::class,
            'user_profile_info',
            'user_profile_info.usr = vk.id'
        );

        $dbal->leftJoin(
            'user_profile_info',
            UserProfileEvent::class,
            'user_profile_event',
            'user_profile_info.event = user_profile_event.id'
        );

        $dbal
            ->addSelect('user_personal.username AS username')
            ->leftJoin(
                'user_profile_event',
                UserProfilePersonal::class,
                'user_personal',
                'user_personal.event = user_profile_event.id'
            );


        /* Поиск */
        if($this->search->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                //->addSearchEqualUid('account.id')
                ->addSearchLike('vk_invariable.vkid');
        }

        return $this->paginator->fetchAllHydrate($dbal, AllAccountVkResult::class);
    }
}
