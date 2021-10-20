<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants\Controller;

use Contao\ContentModel;

trait VariantsTrait
{
    /**
     * Returns a mapping of variant names to integer values (bit flags) grouped by selector name.
     *
     * @return array<string, array<string,int>>
     */
    abstract public function getVariants(): array;

    public function getVariantsMap(ContentModel $model): array
    {
        $variants = $this->getVariants();
        $currentVariants = (int) $model->variants;

        foreach ($variants as &$group) {
            foreach ($group as &$value) {
                // set to true/false if value is set/not set
                $value = 0 !== ($currentVariants & $value);
            }
        }

        return $variants;
    }
}
