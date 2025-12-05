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

namespace BaksDev\Auth\Vk\Twig;

use BaksDev\Auth\Vk\Services\AuthVkUri\AuthVkUriGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthVkButton extends AbstractExtension
{

    public function __construct(
        #[Autowire(env: 'APP_VERSION')] private readonly string $version,
        private readonly AuthVkUriGeneratorInterface $authVkUriGenerator
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'vk_auth_button',
                [$this, 'vkButton'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'vk_auth_button_template',
                [$this, 'vkButtonTemplate'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function vkButton(Environment $twig): string
    {

        $uri = $this->authVkUriGenerator->getVkAutUri();

        return $twig->render('@auth-vk/twig/auth_uri/auth-button.html.twig', ['uri' => $uri]);
    }

    public function vkButtonTemplate(Environment $twig): string
    {
        $url = $this->authVkUriGenerator->getVkAutUri() ?? '';

        try
        {
            return $twig->render('@Template/auth-vk/twig/auth_uri/auth-button.html.twig', [
                'url' => $url,
                'version' => $this->version,
            ]);
        }
        catch(LoaderError)
        {
            return $twig->render('@auth-vk/twig/auth_uri/auth-button.html.twig', [
                'url' => $url,
                'version' => $this->version,
            ]);
        }
    }
}