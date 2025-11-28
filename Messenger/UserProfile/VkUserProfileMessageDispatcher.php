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

namespace BaksDev\Auth\Vk\Messenger\UserProfile;


use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\Info\InfoDTO;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\UserProfileDTO;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\UserProfileHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Создает профиль пользователя при создании аккаунта для авторизации через Vk
 */
#[AsMessageHandler(priority: 0)]
final class VkUserProfileMessageDispatcher
{

    public function __construct(
        #[Target('authVkLogger')] private LoggerInterface $logger,
        private readonly UserProfileHandler $userProfileHandler,

    ) {}

    public function __invoke(VkUserProfileMessage $message): bool
    {

        /**
         * Создаем профиль пользователя по умолчанию
         */
        $UserUid = $message->getId();

        $UserProfileDTO = new UserProfileDTO();
        $UserProfileDTO->setSort(100);
        $UserProfileDTO->setType(new TypeProfileUid(TypeProfileUser::class));

        /** @var InfoDTO $InfoDTO */
        $InfoDTO = $UserProfileDTO->getInfo();
        $InfoDTO->setUrl(uniqid('', false));
        $InfoDTO->setUsr($UserUid);
        $InfoDTO->setStatus(new UserProfileStatus(UserProfileStatusActive::class));

        $UserProfileDTO->setInfo($InfoDTO);

        $PersonalDTO = $UserProfileDTO->getPersonal();

        $PersonalDTO->setUsername($message->getUsername());

        $UserProfileDTO->setPersonal($PersonalDTO);

        $UserProfile = $this->userProfileHandler->handle($UserProfileDTO);


        /* Сделать проверку и logger */
        if(false === ($UserProfile instanceof UserProfile))
        {
            $this->logger->error(
                sprintf(
                    'Ошибка при создании профиля пользователя с идентификатором: %s',
                    $UserUid
                )
            );

            return false;
        }

        return true;
    }
}