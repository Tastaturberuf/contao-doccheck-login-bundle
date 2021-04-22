<?php

/**
 * Doccheck Login Bundle for Contao Open Source CMS
 * @copyright (c) 2021 Tastaturberuf
 * @author    Daniel Jahnsmüller <https://tastaturberuf.de>
 * @license   LGPL-3.0-or-later
 */


declare(strict_types=1);


namespace Tastaturberuf\ContaoDoccheckLoginBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class TastaturberufContaoDoccheckLoginExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

}
