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

namespace BaksDev\Auth\Vk\UseCase\Admin\Delete\Tests;

use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\UseCase\Admin\Delete\AccountVkDeleteDTO;
use BaksDev\Auth\Vk\UseCase\Admin\Delete\AccountVkDeleteHandler;
use BaksDev\Auth\Vk\UseCase\Admin\NewEdit\Tests\AccountVkEditHandlerTest;
use BaksDev\Auth\Vk\UseCase\Admin\NewEdit\Tests\AccountVkHandlerTest;
use BaksDev\Auth\Vk\UseCase\User\Auth\Tests\VkAuthHandlerTest;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


#[Group('auth-vk')]
#[When(env: 'test')]
class AccountVkDeleteHandlerTest extends KernelTestCase
{

    #[DependsOnClass(AccountVkEditHandlerTest::class)]
    public function testUseCase(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $AccountVk = $em->getRepository(AccountVk::class)
            ->find(UserUid::TEST);

        if($AccountVk instanceof AccountVk)
        {
            $AccountVkEvent = $em->getRepository(AccountVkEvent::class)->find(
                $AccountVk->getEvent()
            );

            /** @var AccountVkDeleteDTO $AccountVkDeleteDTO */
            $AccountVkDeleteDTO = new AccountVkDeleteDTO();
            $AccountVkEvent->getDto($AccountVkDeleteDTO);

            /* Вызвать обработчик */
            /** @var AccountVkDeleteHandler $AccountVkDeleteHandler */
            $AccountVkDeleteHandler = self::getContainer()->get(AccountVkDeleteHandler::class);

            $handle = $AccountVkDeleteHandler->handle($AccountVkDeleteDTO);

            self::assertTrue(($handle instanceof AccountVk), $handle.': Ошибка AccountVk');

        }
    }

    public static function tearDownAfterClass(): void
    {
        VkAuthHandlerTest::setUpBeforeClass();
    }
}