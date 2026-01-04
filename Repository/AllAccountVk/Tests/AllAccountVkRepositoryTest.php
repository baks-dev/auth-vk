<?php

namespace BaksDev\Auth\Vk\Repository\AllAccountVk\Tests;

use BaksDev\Auth\Vk\Repository\AllAccountVk\AllAccountVkInterface;
use BaksDev\Auth\Vk\Repository\AllAccountVk\AllAccountVkResult;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('auth-vk')]
#[When(env: 'test')]
class AllAccountVkRepositoryTest extends KernelTestCase
{
    public function testRepository(): void
    {
        /** @var AllAccountVkInterface $AllAccountVkInterface */
        $AllAccountVkInterface = self::getContainer()->get(AllAccountVkInterface::class);

        $results = $AllAccountVkInterface->findAll();

        foreach($results->getData() as $AllAccountVkResult)
        {
            // Вызываем все геттеры
            $reflectionClass = new ReflectionClass(AllAccountVkResult::class);
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($methods as $method)
            {
                // Методы без аргументов
                if($method->getNumberOfParameters() === 0)
                {
                    // Вызываем метод
                    $data = $method->invoke($AllAccountVkResult);
                    // dump($data);
                }
            }
        }

        self::assertTrue(true);
    }
}