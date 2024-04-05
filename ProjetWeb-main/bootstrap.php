<?php

// bootstrap.php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';
$settings = require_once __DIR__ . '/settings.php';

$container = new \DI\Container();

$container->set(EntityManager::class, function () use ($settings): EntityManager {
    $config = ORMSetup::createAttributeMetadataConfiguration($settings['settings']['doctrine']['metadata_dirs'], $settings['settings']['doctrine']['dev_mode']);
    $connection = DriverManager::getConnection($settings['settings']['doctrine']['connection'], $config);
    return new EntityManager($connection, $config);
});

return $container;