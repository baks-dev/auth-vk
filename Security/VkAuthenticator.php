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

namespace BaksDev\Auth\Vk\Security;


use BaksDev\Auth\Email\Messenger\CreateAccount\CreateAccountMessage;
use BaksDev\Auth\Email\Type\Email\AccountEmail;
use BaksDev\Auth\Vk\Api\AuthToken\VkOAuthTokenDTO;
use BaksDev\Auth\Vk\Api\AuthToken\VkOAuthTokenRequest;
use BaksDev\Auth\Vk\Api\UserInfo\VkUserInfoDTO;
use BaksDev\Auth\Vk\Api\UserInfo\VkUserInfoRequest;
use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Messenger\UserProfile\VkUserProfileMessage;
use BaksDev\Auth\Vk\Repository\ActiveUserVkAccount\ActiveUserVkAccountInterface;
use BaksDev\Auth\Vk\Repository\ActiveUserVkAccount\ActiveUserVkAccountResult;
use BaksDev\Auth\Vk\UseCase\User\Auth\Active\AccountVkActiveDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\Invariable\AccountVkInvariableDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\VkAuthDTO;
use BaksDev\Auth\Vk\UseCase\User\Auth\VkAuthHandler;
use BaksDev\Core\Cache\AppCacheInterface;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Repository\GetUserById\GetUserByIdInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class VkAuthenticator extends AbstractAuthenticator
{
    private const string LOGIN_ROUTE = 'auth-vk:public.auth';

    public function __construct(
        #[Target('authVkLogger')] private LoggerInterface $logger,
        private readonly AppCacheInterface $cache,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ActiveUserVkAccountInterface $ActiveUserVkAccount,
        private readonly GetUserByIdInterface $userById,
        private readonly VkOAuthTokenRequest $vkOAuthTokenRequest,
        private readonly VkUserInfoRequest $vkUserInfoRequest,
        private VkAuthHandler $vkAuthHandler,
        protected readonly MessageDispatchInterface $messageDispatch,
    ) {}

    public function supports(Request $request): ?bool
    {

        /** Проверяем, что путь содержит auth/vk */
        $auth_uri = $this->urlGenerator->generate(name: self::LOGIN_ROUTE);
        return str_contains($request->getPathInfo(), $auth_uri);
    }

    public function authenticate(Request $request): Passport
    {

        return new SelfValidatingPassport(
            new UserBadge('vk_authenticator', function() use ($request) {

                /** Получить code device_id  */
                $code = $request->query->get('code');
                $device_id = $request->query->get('device_id');

                /** Отправка данных на id.vk дляя получения user_id */
                $VkOAuthTokenDTO = $this->vkOAuthTokenRequest->get($code, $device_id);


                if(false === ($VkOAuthTokenDTO instanceof VkOAuthTokenDTO))
                {
                    return new SelfValidatingPassport(
                        new UserBadge('error', function() {
                            return null;
                        }),
                    );
                }

                /** Получаем данные UserInfo пользователя */
                $VkUserInfoDTO = $this->vkUserInfoRequest->get($VkOAuthTokenDTO->getAccessToken());


                if(false === ($VkUserInfoDTO instanceof VkUserInfoDTO))
                {
                    return new SelfValidatingPassport(
                        new UserBadge('error', function() {
                            return null;
                        }),
                    );
                }

                /** Идентификатор пользователя Vk - user_id */
                $VkUserId = $VkUserInfoDTO->getUserId();
                $ActiveUserVkAccount = $this->ActiveUserVkAccount->findByVkId($VkUserId);

                /* Если такой аккаунт есть НО он не активен  */
                if(true == ($ActiveUserVkAccount instanceof ActiveUserVkAccountResult) && false === $ActiveUserVkAccount->isActive())
                {

                    $this->logger->warning(sprintf('Пользователь c идентификатором %s не активен', $VkUserId));

                    return new SelfValidatingPassport(
                        new UserBadge('error', function() {
                            return null;
                        }),
                    );
                }

                /** Если нет аккаунта то создаем новый Vk Account */
                if(false === ($ActiveUserVkAccount instanceof ActiveUserVkAccountResult))
                {
                    /* Создать новый AccountVk */
                    $VkAuthDTO = new VkAuthDTO();  // handler

                    $AccountVkInvariableDTO = new AccountVkInvariableDTO();
                    $AccountVkInvariableDTO->setVkid($VkUserId);

                    $VkAuthDTO->setInvariable($AccountVkInvariableDTO);

                    $AccountVkActiveDTO = new AccountVkActiveDTO();
                    $AccountVkActiveDTO->setActive(true);

                    $VkAuthDTO->setActive($AccountVkActiveDTO);

                    $AccountVk = $this->vkAuthHandler->handle($VkAuthDTO);

                    if(false === ($AccountVk instanceof AccountVk))
                    {
                        $this->logger->critical(
                            message: sprintf(
                                'Ошибка создания Vk аккаунта c user_id: %s',
                                $VkUserId
                            ),
                            context: [self::class.':'.__LINE__,]
                        );

                        return null;
                    }


                    if(true === ($VkUserInfoDTO instanceof VkUserInfoDTO))
                    {
                        /* Синхронно бросить сообщение  для создания профиля UserProfile */
                        $VkUserProfile = $this->messageDispatch->dispatch(
                            message: new VkUserProfileMessage(
                                $AccountVk->getId(),
                                $VkUserInfoDTO
                            ));

                        if(false === $VkUserProfile)
                        {
                            $this->logger->error(sprintf(
                                    'Ошибка при создании профиля для Vk пользователя c идентификатором %s',
                                    $VkUserInfoDTO->getUserId()
                                )
                            );

                            return new SelfValidatingPassport(
                                new UserBadge('error', function() {
                                    return null;
                                }),
                            );
                        }
                    }

                    /** Бросить сообщение для создание auth email аккаунта */
                    if(null !== $VkUserInfoDTO->getEmail())
                    {
                        $AccountEmail = new AccountEmail($VkUserInfoDTO->getEmail());
                        /* Отправляем сообщение в шину */
                        $this->messageDispatch->dispatch(
                            message: new CreateAccountMessage($AccountVk->getId(), $AccountEmail),
                            transport: 'auth-vk'
                        );
                    }
                }

                /** Сбрасываем кеш ролей пользователя */
                $cache = $this->cache->init('UserGroup');
                $cache->clear();

                /** Удаляем авторизацию доверенности пользователя */
                $Session = $request->getSession();
                $Session->remove('Authority');


                /* Получить UserUid */
                $userUid = true === ($ActiveUserVkAccount instanceof ActiveUserVkAccountResult)
                    ? $ActiveUserVkAccount->getAccount() /* если существует */
                    : $AccountVk->getId(); /* если создан новый */

                $User = $this->userById->get($userUid);

                if(false === ($User instanceof User))
                {
                    $this->logger->critical(
                        message: 'Пользователь не найден',
                        context: [self::class.':'.__LINE__,]
                    );

                    return null;
                }

                return $User;

            }),

        );

    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

}
