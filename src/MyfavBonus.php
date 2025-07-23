<?php

namespace Myfav\Bonus;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;

/**
 * MyfavBonus
 */
class MyfavBonus extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $customFieldName = 'myfav_is_freebee';

        // Prüfen, ob das Set existiert
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addFilter(
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('name', $customFieldName)
        );

        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $existing = $customFieldSetRepository->search($criteria, $installContext->getContext());

        if ($existing->count() > 0) {
            return; // Set existiert bereits
        }

        $customFieldSetRepository->create([[
            'name' => 'myfav_bonus_article',
            'config' => [
                'label' => [
                    'en-GB' => 'Bonus Artikel',
                    'de-DE' => 'Bonus Artikel',
                ],
            ],
            'customFields' => [[
                'name' => $customFieldName,
                'type' => 'bool',
                'config' => [
                    'label' => [
                        'en-GB' => 'This Article is a bonus article.',
                        'de-DE' => 'Dieser Artikel ist ein Bonus-Artikel',
                    ],
                    'componentName' => 'sw-field',
                    'customFieldType' => 'checkbox',
                    'type' => 'bool',
                ],
            ]],
            'relations' => [[
                'entityName' => 'product',
            ]]
        ]], $installContext->getContext());
    }
}