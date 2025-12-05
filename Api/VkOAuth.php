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

namespace BaksDev\Auth\Vk\Api;

use BaksDev\Core\Cache\AppCacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;

abstract class VkOAuth
{

    const string BASE_URI = 'id.vk.ru';

    protected const string REDIRECT_URI = 'auth-vk:public.auth';

    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $environment,
        #[Autowire(env: 'VK_CLIENT_ID')] protected readonly string $clientId,
        #[Autowire(env: 'VK_CODE_VERIFIER')] protected readonly string $codeVerifier,
        #[Target('authVkLogger')] protected readonly LoggerInterface $logger,
        private readonly AppCacheInterface $cache,
        protected readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    public function TokenHttpClient(): RetryableHttpClient
    {
        return new RetryableHttpClient(
            HttpClient::create([
                'headers' =>
                    [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ]
            ])
                ->withOptions([
                    'base_uri' => 'https://'.self::BASE_URI,
                    'verify_host' => false,
                ]),
        );

    }

    /**
     * Метод проверяет что окружение является PROD,
     * тем самым позволяет выполнять операции запроса на сторонний сервис
     * ТОЛЬКО в PROD окружении
     */
    protected function isExecuteEnvironment(): bool
    {
        return $this->environment === 'prod';
    }

    protected function getCacheInit(string $namespace): CacheInterface
    {
        return $this->cache->init($namespace);
    }

}
