<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license LGPL-3.0-or-later
 */

namespace Mvo\ContaoContentVariants\EventListener;

use Contao\ContentModel;
use Contao\CoreBundle\Fragment\FragmentRegistryInterface;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Mvo\ContaoContentVariants\Controller\VariantsInterface;
use Psr\Container\ContainerInterface;

class ContentVariants
{
    public function __construct(private FragmentRegistryInterface $fragmentRegistry, private ContainerInterface $locator, private Connection $connection)
    {
    }

    /**
     * @Hook("loadDataContainer")
     */
    public function __invoke(string $table): void
    {
        if ('tl_content' !== $table) {
            return;
        }

        foreach ($GLOBALS['TL_DCA']['tl_content']['fields'] ?? [] as $field => $definition) {
            if ('variantSelect' !== ($definition['inputType'] ?? '')) {
                continue;
            }

            // Add load/save/list options
            $defaultOptionsReference = &$GLOBALS['TL_LANG']['tl_content'][$field.'_'];

            $GLOBALS['TL_DCA']['tl_content']['fields'][$field] = array_merge(
                $definition,
                [
                    'load_callback' => [['mvo.contao_content_variants.dca_listener', 'loadVariants']],
                    'save_callback' => [['mvo.contao_content_variants.dca_listener', 'storeVariants']],
                    'options_callback' => ['mvo.contao_content_variants.dca_listener', 'getVariantOptions'],
                    'eval' => array_merge(
                        $definition['eval'] ?? [],
                        [
                            'isAssociative' => true,
                            'doNotSaveEmpty' => true,
                        ]
                    ),
                    'reference' => \array_key_exists('reference', $definition) ?
                        $definition['reference'] : $defaultOptionsReference,
                ]
            );
        }
    }

    public function getVariantOptions(DataContainer $dc): array
    {
        return array_flip($this->getVariants($dc));
    }

    public function loadVariants($_, DataContainer $dc): int
    {
        $values = array_values($this->getVariants($dc));

        return $dc->activeRecord->variants & array_sum($values);
    }

    public function storeVariants(int $value, DataContainer $dc): ?int
    {
        $values = array_values($this->getVariants($dc));

        if (!\in_array($value, $values, true)) {
            return null;
        }

        $combinedValue = (int) $this->connection->fetchOne(
            'SELECT variants from tl_content WHERE id = ?',
            [(int) $dc->id]
        );

        $combinedValue = $combinedValue & ~array_sum($values) | $value;

        $this->connection->update(
            'tl_content',
            ['variants' => $combinedValue],
            ['id' => (int) $dc->id]
        );

        return null;
    }

    private function getVariants(DataContainer $dc): array
    {
        /** @var ContentModel $activeRecord */
        $activeRecord = $dc->activeRecord;

        $type = $activeRecord->type;
        $field = $dc->field;

        $fragment = $this->fragmentRegistry->get('contao.content_element.'.$type);

        if (null === $fragment) {
            throw new \RuntimeException("No fragment was found to resolve variants of type '$type'");
        }

        $controller = $this->locator->get($fragment->getController());

        if (!$controller instanceof VariantsInterface) {
            throw new \RuntimeException("Fragment for type '$type' does not support variants.");
        }

        return $controller->getVariants()[$field] ?? [];
    }
}
