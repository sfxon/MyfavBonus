<?php declare(strict_types=1);

namespace Myfav\Bonus\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MyfavBonusService
{
    /**
     * __construct
     */
    public function __construct(
        private readonly EntityRepository $myfavBonusRepository,
    )
    {
    }

    /**
     * calculateBonusData
     *
     * @param  mixed $boni
     * @param  mixed $cart
     * @return array
     */
    public function calculateBonusData($boni, $cart): array
    {
        $cartTotalPrice = $cart->getPrice()->getPositionPrice();

        $nextBonus = false;
        $nextBonusPrice = false;
        $maxBonusPrice = 0.0;
        $gapTillNextBonus = false;
        $reachedMaximumBonus = false;

        if($boni->getTotal() > 0) {
            foreach($boni as $bonus) {
                $fromCartPrice = $bonus->getFromCartPrice();

                if($cartTotalPrice < $fromCartPrice) {
                    if($nextBonus === false) {
                        $nextBonus = $bonus;
                        $nextBonusPrice = $fromCartPrice;
                    }elseif($fromCartPrice < $nextBonusPrice) {
                        $nextBonus = $bonus;
                        $nextBonusPrice = $fromCartPrice;
                    }
                }

                if($maxBonusPrice < $fromCartPrice) {
                    $maxBonusPrice = $fromCartPrice;
                }
            }
        }

        if($nextBonus !== false) {
            $gapTillNextBonus = abs($cartTotalPrice - $nextBonusPrice);
        } else {
            $reachedMaximumBonus = true;
        }

        // Fortschrift bis zum Maximum Preis berechnen.
        $bonusProgressInPercent = 0;

        if($maxBonusPrice < $cartTotalPrice || $maxBonusPrice === 0.0) {
            $bonusProgressInPercent = 100;
        } else if($cartTotalPrice === 0) {
            $bonusProgressInPercent = 0;
        } else {
            $bonusProgressInPercent = 100 / $maxBonusPrice * $cartTotalPrice;
        }

        $goalsReached = 0;

        // Position der Freebees auf dem Fortschrittsbalken berechnen.
        if($boni->getTotal() > 0 && $maxBonusPrice !== false && $maxBonusPrice > 0) {
            foreach($boni as $bonus) {
                // Calculate percent
                $progressInPercent = 100 / $maxBonusPrice * $bonus->getFromCartPrice();

                // Calculate, if goal is reached.
                $goalReached = false;

                if($bonus->getFromCartPrice() <= $cartTotalPrice) {
                    $goalReached = true;
                    $goalsReached++;
                }

                $bonus->addExtension('myfavBonus', new ArrayStruct([
                    'goalReached' => $goalReached,
                    'progressInPercent' => $progressInPercent
                ]));
            }
        }

        // Array zur Übergabe ans Storefront vorbereiten.
        $customData = [
            'boni' => $boni,
            'bonusProgressInPercent' => $bonusProgressInPercent,
            'cartTotalPrice' => $cartTotalPrice,
            'gapTillNextBonus' => $gapTillNextBonus,
            'goalsReached' => $goalsReached,
            'maxBonusPrice' => $maxBonusPrice,
            'reachedMaximumBonus' => $reachedMaximumBonus
        ];

        return $customData;
    }

    /**
     * loadActivatedBoni
     *
     * @return mixed
     */
    public function loadActivatedBoni(
        Context $context): mixed
    {
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addAssociation('product.cover');
        $criteria->addAssociation('product.cover.media');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addSorting(new FieldSorting('sortOrder', 'ASC'));

        //get and return one template
        return $this->myfavBonusRepository->search($criteria, $context);
    }
}