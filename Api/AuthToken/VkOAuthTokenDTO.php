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

final readonly class VkOAuthTokenDTO
{
    public function __construct(
        private string $access_token,
        private int $expires_in,
        private string $id_token,
        private string $refresh_token,
        private string $token_type,
        private int|string $user_id,
        private string $state,
        private string $scope,
    ) {}

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getIdToken(): string
    {
        return $this->id_token;
    }

    public function getUserId(): string
    {
        return (string) $this->user_id;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function getExpiresIn(): int
    {
        return $this->expires_in;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function getTokenType(): string
    {
        return $this->token_type;
    }
}
