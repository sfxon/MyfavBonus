<?php

namespace Myfav\Bonus\Storefront\Subscriber;

use Myfav\Bonus\Services\MyfavBonusService;
use Shopware\Core\Checkout\Cart\Event\CartChangedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CartChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly MyfavBonusService $myfavBonusService)
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
            CartChangedEvent::class => 'onCartChanged',
        ];
    }

    /**
     * onCartChanged
     *
     * @param  mixed $event
     * @return void
     */
    public function onCartChanged(CartChangedEvent $event): void
    {
        $salesChannelContext = $event->getContext();
        $context = $salesChannelContext->getContext();
        $boni = $this->myfavBonusService->loadActivatedBoni($context);
        $cart = $event->getCart();
        $bonusData = $this->myfavBonusService->calculateBonusData($boni, $cart);

        if($bonusData['boni']->getTotal() === 0) {
            return;
        }

        $this->removeBonusArticlesWhenGoalNotReached($salesChannelContext, $context, $cart, $bonusData);
        $this->addAutoBonusArticlesWhenGoalReached($salesChannelContext, $context, $cart, $bonusData);
    }

    /**
     * removeBonusArticlesWhenGoalNotReached
     *
     * @param  mixed $salesChannelContext
     * @param  mixed $context
     * @param  mixed $cart
     * @param  mixed $bonusData
     * @return void
     */
    private function removeBonusArticlesWhenGoalNotReached($salesChannelContext, $context, $cart, $bonusData): void
    {
        $bonis = [];
        
        foreach($bonusData['boni'] as $bonusItem) {
            if(
                $bonusItem->getProductId() !== null
            ) {
                $bonis[] = $bonusItem;
            }
        }

        if(count($bonis) === 0) {
            return;
        }

        // Prüfen, welche Artikel den Wert nicht erreicht haben.
        $lineItems = $cart->getLineItems();
        $notReachedBonis = [];

        foreach($bonis as $bonus) {
            if(isset($bonus->getExtensions()['myfavBonus']) && isset($bonus->getExtensions()['myfavBonus']['goalReached'])) {
                $goalReached = $bonus->getExtensions()['myfavBonus']['goalReached'];

                if(!$goalReached) {
                    $notReachedBonis[] = $bonus;
                }
            }
        }

        // Prüfen, ob die Bonus-Artikel im Warenkorb liegen.
        $removeItems = [];
        
        foreach($notReachedBonis as $bonus) {
            $foundInCart = false;
            $lineItemIdentifier = null;

            foreach($lineItems as $lineItem) {
                if($lineItem->getType() === 'promotion') {
                    continue;
                }

                if($lineItem->getPayload()['productNumber'] === $bonus->getProduct()->getProductNumber()) {
                    $foundInCart = true;
                    $lineItemIdentifier = $lineItem->getId();
                    break;
                }
            }

            if($foundInCart === true) {
                $removeItems[] = $lineItemIdentifier;
            }
        }

        // Ersten Artikel hinzufügen, der noch gefehlt hat. (das führt zu einer gewollten Schleife, in der ggf. weitere Artikel hinzugefügt werden.)
        if(count($removeItems) > 0) {
            $this->cartService->remove($cart, $removeItems[0], $salesChannelContext);
        }
    }

    /**
     * addAutoBonusArticlesWhenGoalReached
     *
     * @param  mixed $salesChannelContext
     * @param  mixed $context
     * @param  mixed $cart
     * @param  mixed $bonusData
     * @return void
     */
    private function addAutoBonusArticlesWhenGoalReached($salesChannelContext, $context, $cart, $bonusData): void
    {
        $autoBonis = [];
        
        foreach($bonusData['boni'] as $bonusItem) {
            if(
                $bonusItem->getProductId() !== null && // Nur, wenn der Artikel geladen werden konnte.
                ($bonusItem->getProduct()->getChildCount() === null || $bonusItem->getProduct()->getChildCount() === 0) && // Nur, wenn der Artikel kein Main-Article mit Varianten ist.
                $bonusItem->getProduct()->getActive() === true && // Nur, wenn der Artikel aktiv ist.
                $bonusItem->getProduct()->getStock() > 0 // Nur, wenn der Artikel noch verfügbar ist.
            ) {
                if($bonusItem->getAutoActivation() === true) {
                    $autoBonis[] = $bonusItem;
                }
            }
        }

        if(count($autoBonis) === 0) {
            return;
        }

        // Prüfen, ob die Bonus-Artikel schon im Warenkorb liegen.
        $lineItems = $cart->getLineItems();
        $addBonusProductIds = [];

        foreach($autoBonis as $autoBonus) {
            // Nicht hinzufügen, wenn der Wert noch nicht erreicht wurde.
            if(isset($autoBonus->getExtensions()['myfavBonus']) && isset($autoBonus->getExtensions()['myfavBonus']['goalReached'])) {
                $goalReached = $autoBonus->getExtensions()['myfavBonus']['goalReached'];

                if(!$goalReached) {
                    continue;
                }
            } else {
                continue;
            }

            $foundInCart = false;

            foreach($lineItems as $lineItem) {
                if($lineItem->getType() === 'promotion') {
                    continue;
                }

                if($lineItem->getPayload()['productNumber'] === $autoBonus->getProduct()->getProductNumber()) {
                    $foundInCart = true;
                    break;
                }
            }

            if($foundInCart === false) {
                $addBonusProductIds[] = $autoBonus->getProductId();
            }
        }

        // Ersten Artikel hinzufügen, der noch gefehlt hat. (das führt zu einer gewollten Schleife, in der ggf. weitere Artikel hinzugefügt werden.)
        if(count($addBonusProductIds) > 0) {
            $productId = $addBonusProductIds[0];

            $freeItem = (new LineItem($productId, LineItem::PRODUCT_LINE_ITEM_TYPE, $productId))
                ->setStackable(false)
                ->setRemovable(true);

            // ACHTUNG: NICHT direkt dem Cart hinzufügen – verwende CartService!
            $this->cartService->add($cart, [$freeItem], $salesChannelContext);
        }
    }
}