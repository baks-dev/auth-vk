<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Auth\Vk\BaksDevAuthVkBundle;
use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifier;
use BaksDev\Auth\Vk\Type\AuthVkIdentifier\VkIdentifierType;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventType;
use BaksDev\Auth\Vk\Type\Event\AccountVkEventUid;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {

    $doctrine->dbal()->type(AccountVkEventUid::TYPE)->class(AccountVkEventType::class);

    $doctrine->dbal()->type(VkIdentifier::TYPE)->class(VkIdentifierType::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault->mapping('auth-vk')
        ->type('attribute')
        ->dir(BaksDevAuthVkBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevAuthVkBundle::NAMESPACE.'\\Entity')
        ->alias('auth-vk');
};
