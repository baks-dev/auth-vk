<?php

namespace BaksDev\Auth\Vk\Repository\AllAccountVk\Tests;

use BaksDev\Auth\Vk\Repository\AllAccountVk\AllAccountVkInterface;
use BaksDev\Auth\Vk\Repository\AllAccountVk\AllAccountVkResult;
use PHPUnit\Framework\Attributes\Group;
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

        foreach($results as $result)
        {
            $this->assertInstanceOf(AllAccountVkResult::class, $result);
        }

        //        dd($results);

        self::assertTrue(true);
    }
}