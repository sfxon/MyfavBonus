<?php declare(strict_types=1);

namespace Myfav\Bonus\Core\Content\MyfavBonus;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Content\Product\ProductEntity;

class MyfavBonusEntity extends Entity
{
    use EntityIdTrait;

    protected bool $active = false;
    protected string $title;
    protected ?string $subtitle = null;
    protected ?float $fromCartPrice = null;
    protected bool $autoActivation = false;
    protected ?string $productId = null;
    protected ?ProductEntity $product = null;
    protected ?int $sortOrder = null;
    protected ?string $freebeeIconUrl = null;
    protected ?string $freebeeImageUrl = null;

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getFromCartPrice(): ?float
    {
        return $this->fromCartPrice;
    }

    public function setFromCartPrice(?float $fromCartPrice): void
    {
        $this->fromCartPrice = $fromCartPrice;
    }

    public function getAutoActivation(): bool
    {
        return $this->autoActivation;
    }

    public function setAutoActivation(bool $autoActivation): void
    {
        $this->autoActivation = $autoActivation;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    // freebeeIconUrl
    public function getFreebeeIconUrl(): ?string
    {
        return $this->freebeeIconUrl;
    }

    public function setFreebeeIconUrl(?string $freebeeIconUrl): void
    {
        $this->freebeeIconUrl = $freebeeIconUrl;
    }

    //freebeeImageUrl
    public function getFreebeeImageUrl(): ?string
    {
        return $this->freebeeImageUrl;
    }

    public function setFreebeeImageUrl(?string $freebeeImageUrl): void
    {
        $this->freebeeImageUrl = $freebeeImageUrl;
    }
}
