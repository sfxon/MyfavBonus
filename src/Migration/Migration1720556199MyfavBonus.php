<?php declare(strict_types=1);

namespace Myfav\Bonus\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1720556199MyfavBonus extends MigrationStep
{
    /**
     * getCreationTimestamp
     *
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1720556199;
    }

    /**
     * update
     *
     * @param  Connection $connection
     * @return void
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            'CREATE TABLE IF NOT EXISTS `myfav_bonus` (
                `id` BINARY(16) NOT NULL,
                `active` TINYINT(1) DEFAULT 0,
                `title` VARCHAR(255) NOT NULL,
                `subtitle` VARCHAR(255) NULL,
                `from_cart_price` DECIMAL(10,2) NULL,
                `auto_activation` TINYINT(1) DEFAULT 0,
                `product_id` BINARY(16) NULL,
                `sort_order` INT NULL,
                `freebee_icon_url` VARCHAR(255) NOT NULL,
                `freebee_image_url` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.myfav_bonus.product_id` 
                    FOREIGN KEY (`product_id`) 
                    REFERENCES `product` (`id`) 
                    ON DELETE SET NULL 
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
