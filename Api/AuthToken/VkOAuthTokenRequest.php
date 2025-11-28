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

namespace BaksDev\Auth\Vk\Api\AuthToken;

use BaksDev\Auth\Vk\Api\VkOAuth;
use DateInterval;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Получение данных для авторизации
 * https://id.vk.ru/oauth2/auth
 * @see https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/api-description#Poluchenie-tokena-ukazannym-sposobom
 */
final class VkOAuthTokenRequest extends VkOAuth
{

    /** $code - Код подтверждения authorization_code, который можно обменять на токен.  */
    /** $device_id - Уникальный идентификатор устройства, полученный вместе с авторизационным кодом  */
    public function get(string $code, string $device_id): VkOAuthTokenDTO|false
    {
        $cache = $this->getCacheInit('auth-vk');
        $key = 'vk-oauth-'.$code;

        $content = $cache->get($key, function(ItemInterface $item) use ($code, $device_id) {

            $item->expiresAfter(DateInterval::createFromDateString('1 seconds'));

            $redirect_uri = $this->urlGenerator->generate(
                name: self::REDIRECT_URI,
                referenceType: CompiledUrlGenerator::ABSOLUTE_URL
            );

            $body = [
                'client_id' => $this->clientId,
                'grant_type' => 'authorization_code',
                'code_verifier' => $this->codeVerifier, // должно быть таким же как и для генерации ссылки авторизации
                'device_id' => $device_id,
                'code' => $code,
                'redirect_uri' => $redirect_uri,
            ];

            /** Делаем запрос для получения токена Яндекс OAuth */
            $response = $this->TokenHttpClient()
                ->request(
                    'POST',
                    'oauth2/auth',
                    [
                        'body' => $body,
                    ],
                );

            if($response->getStatusCode() !== 200)
            {
                $this->logger->critical(
                    message: 'Ошибка получения токена Vk OAuth',
                    context: [
                        $body,
                        $response->toArray(false),
                        self::class.':'.__LINE__,
                    ]);

                return false;
            }

            $content = $response->toArray(false);

            $item->expiresAfter(DateInterval::createFromDateString($content['expires_in'].' seconds'));

            return $content;

        });

        return false !== $content ? new VkOAuthTokenDTO(...$content) : false;
    }
}
