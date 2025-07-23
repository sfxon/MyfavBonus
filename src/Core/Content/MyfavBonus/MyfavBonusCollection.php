<?php declare(strict_types=1);

namespace Myfav\Bonus\Core\Content\MyfavBonus;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(MyfavBonusEntity $entity)
 * @method void                    set(string $key, MyfavBonusEntity $entity)
 * @method MyfavBonusEntity[]    getIterator()
 * @method MyfavBonusEntity[]    getElements()
 * @method MyfavBonusEntity|null get(string $key)
 * @method MyfavBonusEntity|null first()
 * @method MyfavBonusEntity|null last()
 */
class MyfavBonusCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'myfav_bonus';
    }

    protected function getExpectedClass(): string
    {
        return MyfavBonusEntity::class;
    }
}
