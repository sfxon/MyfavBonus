<?php

namespace Myfav\Bonus\Storefront\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntitySearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductSearchCriteriaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductSearchCriteriaEvent::class => 'onProductSearchCriteria'
        ];
    }

    public function onProductSearchCriteria(ProductSearchCriteriaEvent $event): void
    {
        $criteria = $event->getCriteria();

        $criteria->addFilter(new EqualsFilter('product.customFields.myfav_is_freebee', false));
    }
}