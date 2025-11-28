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


use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\Messenger\AccountVkMessage;
use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Users\User\Entity\User;


final class VkAuthHandler extends AbstractHandler
{
    /** @see */
    public function handle(VkAuthDTO $command): string|AccountVk
    {
        $this->setCommand($command);

        /* Cоздать User и AccountVk */
        $user = new User();

        $AccountVk = new AccountVk();
        $AccountVk->setId($user->getId());

        $this->preEventPersistOrUpdate($AccountVk, new AccountVkEvent());


        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->persist($user);

        $this->flush();

        $this->messageDispatch->addClearCacheOther('auth-vk');

        /* Отправить сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new AccountVkMessage($this->main->getId(), $this->main->getEvent()),
            transport: 'auth-vk'
        );

        return $this->main;
    }
}