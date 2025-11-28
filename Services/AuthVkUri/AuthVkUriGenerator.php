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

namespace BaksDev\Auth\Vk\Services\AuthVkUri;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthVkUriGenerator implements AuthVkUriGeneratorInterface
{

    private const string REDIRECT_URI = 'auth-vk:public.auth';
    private const string CODE_CHALLENGE_METHOD = 'S256';
    private const string SCOPE = 'email';
    private const string RESPONSE_TYPE = 'code';
    private const string AUTHORIZE_URI = 'id.vk.ru/authorize';

    public function __construct(
        #[Autowire(env: 'VK_CLIENT_ID')] private string $client_id,
        #[Autowire(env: 'VK_CODE_VERIFIER')] private string $code_verifier,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * Генерация ссылки для авторизации
     * https://id.vk.ru/authorize?response_type=code
     * @see https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/api-description#Zapros-koda-podtverzhdeniya-i-rabota-s-formoj-razresheniya-dostupov-polzovatelya
     */
    public function getVkAutUri(): string
    {
        $code_challenge = $this->generateCodeChallenge($this->code_verifier);

        $redirect_uri = $this->urlGenerator->generate(
            name: self::REDIRECT_URI,
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
        );

        $str_uri_array = [
            'response_type' => self::RESPONSE_TYPE,
            'client_id' => $this->client_id,
            'redirect_uri' => $redirect_uri,
            'state' => $this->generateState(),
            'code_challenge_method' => self::CODE_CHALLENGE_METHOD,
            'code_challenge' => $code_challenge,
            'scope' => self::SCOPE,
        ];

        $get_str = http_build_query($str_uri_array);

        return 'https://'.self::AUTHORIZE_URI.'?'.$get_str;
    }

    private function generateCodeChallenge(string $codeVerifier): string
    {
        /* Хэш-код верификатора с использованием SHA256 */
        $hash = hash('sha256', $codeVerifier, true);
        /* Кодировать хеш с использованием Base64Url */
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /**
     * Генерация code_verifier
     */
    private function generateState(): string
    {
        /* Сгенерировать 32 случайных байта (256 бит) */
        $randomBytes = random_bytes(32);
        /* Base64Url кодирует случайные байты, удаляя заполнение */
        return rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
    }
}