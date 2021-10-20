<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Mvo\ContaoContentVariants\MvoContaoContentVariantsBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MvoContaoContentVariantsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
