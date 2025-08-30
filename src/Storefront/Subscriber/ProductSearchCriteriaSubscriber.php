<?php

namespace Myfav\Bonus\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntitySearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class ProductSearchCriteriaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductSearchCriteriaEvent::class => 'onProductSearchCriteria',
            ProductSuggestCriteriaEvent::class => 'onProductSuggestCriteria'
        ];
    }

    public function onProductSearchCriteria(ProductSearchCriteriaEvent $event): void
    {
        $criteria = $event->getCriteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_OR,
                [
                    new EqualsFilter('product.customFields.myfav_is_freebee', NULL),
                    new EqualsFilter('product.customFields.myfav_is_freebee', false)
                ]
            )
        );
    }

    public function onProductSuggestCriteria(ProductSuggestCriteriaEvent $event): void
    {
        $criteria = $event->getCriteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_OR,
                [
                    new EqualsFilter('product.customFields.myfav_is_freebee', NULL),
                    new EqualsFilter('product.customFields.myfav_is_freebee', false)
                ]
            )
        );
    }
}