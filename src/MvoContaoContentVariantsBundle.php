<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MvoContaoContentVariantsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
