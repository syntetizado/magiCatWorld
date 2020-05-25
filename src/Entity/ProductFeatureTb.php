<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductFeatureTb
 *
 * @ORM\Table(name="product_feature_tb", indexes={@ORM\Index(name="fk_productfeature", columns={"id_product"})})
 * @ORM\Entity
 */
class ProductFeatureTb
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="feature", type="string", length=255, nullable=true)
     */
    private $text;

    /**
     * @var \ProductTb
     *
     * @ORM\ManyToOne(targetEntity="ProductTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var \ProductTb
     *
     * @ORM\ManyToOne(targetEntity="ProductTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     * })
     */
    private $idProduct;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->name = $text;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(?ProductTb $product): self
    {
        $this->name = $product;

        return $this;
    }

    public function getIdProduct(): ?ProductTb
    {
        return $this->idProduct;
    }

    public function setIdProduct(?ProductTb $idProduct): self
    {
        $this->idProduct = $idProduct;

        return $this;
    }
}
