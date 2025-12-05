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
 *
 */

declare(strict_types=1);

namespace BaksDev\Auth\Vk\Api\UserInfo;

use BaksDev\Auth\Vk\Api\VkOAuth;
use DateInterval;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Получение данных Vk пользователя
 * https://id.vk.ru/oauth2/user_info
 * @see https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/api-description#Poluchenie-nemaskirovannyh-dannyh
 */

final class VkUserInfoRequest extends VkOAuth
{


    /**
     * Получение данных пользователя Vk по access_token
     */
    public function get(string $access_token): VkUserInfoDTO|false
    {
        $cache = $this->getCacheInit('auth-vk');

        $key = 'vk-user-info'.$access_token;


        $content = $cache->get($key, function(ItemInterface $item) use ($access_token) {

            $item->expiresAfter(DateInterval::createFromDateString('1 seconds'));

            /** Делаем запрос на получение данных пользователя */
            $response = $this
                ->TokenHttpClient()
                ->request(
                    'POST',
                    'oauth2/user_info',
                    [
                        'body' => [
                            'access_token' => $access_token,
                            'client_id' => $this->clientId,
                        ],
                    ]
                );

            if($response->getStatusCode() !== 200)
            {
                $this->logger->critical(
                    message: 'Ошибка получения данных пользователя Vk',
                    context: [
                        $access_token,
                        $response,
                        self::class.':'.__LINE__,
                    ]);

                return false;
            }

            $contentResult = $response->toArray(false);
            $content = $contentResult['user'] ?? false;

            $item->expiresAfter(DateInterval::createFromDateString('1 day'));


            return $content;
        });

        return false !== $content ? new VkUserInfoDTO(...$content) : false;
    }
}
