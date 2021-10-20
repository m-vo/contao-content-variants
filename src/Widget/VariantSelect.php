<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants\Widget;

use Contao\SelectMenu;

class VariantSelect extends SelectMenu
{
    /**
     * Coerce values instead of failing or displaying an unknown option.
     */
    protected function isValidOption($varInput)
    {
        $this->unknownOption = [];

        if (!parent::isValidOption($varInput)) {
            $this->varValue = $this->arrOptions[0]['value'] ?? 0;
        }

        return true;
    }
}
