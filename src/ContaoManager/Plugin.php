<?php

/**
 * Doccheck Login Bundle for Contao Open Source CMS
 * @copyright (c) 2021 Tastaturberuf
 * @author    Daniel Jahnsmüller <https://tastaturberuf.de>
 * @license   LGPL-3.0-or-later
 */

declare(strict_types=1);


namespace Tastaturberuf\ContaoDoccheckLoginBundle\ContaoManager;


use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Tastaturberuf\ContaoDoccheckLoginBundle\TastaturberufContaoDoccheckLoginBundle;


class Plugin implements BundlePluginInterface
{

    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(TastaturberufContaoDoccheckLoginBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

}
