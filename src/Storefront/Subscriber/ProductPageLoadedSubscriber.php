<?php

namespace Myfav\Bonus\Storefront\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;

class ProductPageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SystemConfigService $configService,
        private readonly RouterInterface $router)
    {
    }

    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    /**
     * onProductPageLoaded
     *
     * @param  mixed $event
     * @return void
     */
    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();

        // Prüfen, ob das Custom Field gesetzt ist
        $customFields = $product->getCustomFields();
        if (!empty($customFields['myfav_is_freebee'])) {
            $redirectUrl = $this->configService->get('MyfavBonus.config.freebeeRedirectPage') ?? null;
            header('Location: ' . $redirectUrl, true, 302);
            exit; // Wichtig: Sofort abbrechen!
        }
    }
}
