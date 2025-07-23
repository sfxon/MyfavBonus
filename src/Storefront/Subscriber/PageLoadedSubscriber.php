<?php

namespace Myfav\Bonus\Storefront\Subscriber;

use Myfav\Bonus\Services\MyfavBonusService;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly MyfavBonusService $myfavBonusService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenericPageLoadedEvent::class => 'onPageLoaded'
        ];
    }

    /**
     * onPageLoaded
     *
     * @param  GenericPageLoadedEvent $event
     * @return void
     */
    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $context = $event->getContext();
        $page = $event->getPage();
        $salesChannelContext = $event->getSalesChannelContext();

        // Freebees laden.
        $boni = $this->myfavBonusService->loadActivatedBoni($context);
        $cart = $this->cartService->getCart($salesChannelContext->getToken(), $salesChannelContext);

        // Werte wie erreichte Schwellen u.ä. berechnen.
        $bonusData = $this->myfavBonusService->calculateBonusData($boni, $cart);

        // Daten in die Page injecten
        $page->addExtension('myfavBonus', new ArrayStruct($bonusData));
    }
}
