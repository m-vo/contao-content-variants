# Contao Content Variants

This DX package eases working with binary variant options for content elements.
The selected variants will be stored as bit flags in a **single integer
column** in `tl_content`. The available values can be defined within each
content element controller itself. 

A typical usage scenario are predefined options like different layouts/designs/colors/…
that an editor should be able to choose from via a select menu.

## Usage

1. Add a `variantSelect` field for each variant group to your `tl_content`
   field section. Do not include SQL definitions:
   ```php
    $GLOBALS['TL_DCA']['tl_content']['fields']['effect'] = [
            'inputType' => 'variantSelect',
            'eval' => [
                'tl_class' => 'w50',
            ],
    ];
   
    $GLOBALS['TL_DCA']['tl_content']['fields']['layout'] = [
            'inputType' => 'variantSelect',
            'eval' => [
                'tl_class' => 'w50',
            ],
    ];
    ```
   Then add the fields to your palettes like usual. The `variantSelect` is
   basically a select menu - we're automatically populating it with the
   respective options once you access the DCA in the back end. 


2. Define labels in your language files. By default, an `_` will be appended to
   the field name for the options reference:

    ```php
    $GLOBALS['TL_LANG']['tl_content']['effect'] = ['Effect', 'Visual variant'];
    $GLOBALS['TL_LANG']['tl_content']['effect_']['default'] = ['Default'];
    $GLOBALS['TL_LANG']['tl_content']['effect_']['popup'] = ['Popup'];
    $GLOBALS['TL_LANG']['tl_content']['effect_']['highlight'] = ['Highlight'];
    
    // …
    ``` 


3. Implement the `VariantsInterface` and use the `VariantsTrait` in your
   fragment controller. You can then define the variant groups and their values
   as bit flags like so:

    ```php
    /**
     * @ContentElement(category="texts")
     */
    class MyTextFragment extends AbstractContentElementController implements VariantsInterface
    {
        use VariantsTrait;
    
        public function getVariants(): array
        {
            return [
                'effect' => [
                    'default'     => (1<<0),
                    'popup'       => (1<<1),
                    'highlight'   => (1<<2),
                ],
                'layout' => [
                    'default'     => (1<<3),
                    'centered'    => (1<<4),
                    'align_left'  => (1<<5),
                    'align_right' => (1<<6),
                ],
            ];
        }
    
        protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
        {
            $context = [
                'text' => $model->text,
                'variants' => $this->getVariantsMap($model),
            ];
    
            return $this->render('@Contao/my_text.html.twig', $context);
        }
    }
    ```
    You have control over the bit flags. If you want to get rid of an option at
    one point, simply delete the line and keep the other fields untouched. 


4. Handle variants in your template:
    ```twig
    <div>
        {% if variants.effect.highlight %}
            <b>Wow!</b>
        {% endif %}
        
        […]
    </div>
    ```
