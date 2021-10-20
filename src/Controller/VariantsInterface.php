<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants\Controller;

interface VariantsInterface
{
    public function getVariants(): array;
}
