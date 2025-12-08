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

namespace BaksDev\Auth\Vk\UseCase\User\Auth\Tests;

use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifier;
use BaksDev\Auth\Vk\UseCase\User\Auth\Active\AccountVkActiveDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\Invariable\AccountVkInvariableDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\VkAuthDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\VkAuthHandler;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('auth-vk')]
#[When(env: 'test')]
class VkAuthHandlerTest extends KernelTestCase
{

    public static function setUpBeforeClass(): void
    {

        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        $AccountVk = $em->getRepository(AccountVk::class)
            ->find(UserUid::TEST);

        if($AccountVk)
        {
            $em->remove($AccountVk);
        }

        /** @var AccountVkEvent $AccountEvent */
        $AccountEvent = $em->getRepository(AccountVkEvent::class)
            ->findBy(['account' => UserUid::TEST]);

        foreach($AccountEvent as $remove)
        {
            $em->remove($remove);
        }

        /* Получить тестовый User и удалить */
        $User = $em->getReference(User::class, new UserUid(UserUid::TEST));

        if($User instanceof User)
        {
            $em->remove($User);
        }


        $em->flush();
    }

    public function testUseCase(): void
    {

        $VkAuthDTO = new VkAuthDTO();

        $AccountVkActiveDTO = new AccountVkActiveDTO();
        $AccountVkActiveDTO->setActive(true);

        $VkAuthDTO->setActive($AccountVkActiveDTO);


        $AccountVkInvariableDTO = new AccountVkInvariableDTO();
        $AccountVkInvariableDTO->setVkid(new VkIdentifier(VkIdentifier::TEST));

        $VkAuthDTO->setInvariable($AccountVkInvariableDTO);


        self::bootKernel();

        /** @var VkAuthHandler $vkHandler */
        $vkHandler = self::getContainer()->get(VkAuthHandler::class);
        $handle = $vkHandler->handle($VkAuthDTO);

        self::assertTrue(($handle instanceof AccountVk), $handle.': Ошибка AuthVk');
    }
}