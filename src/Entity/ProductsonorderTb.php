<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsonorderTb
 *
 * @ORM\Table(name="productsonorder_tb", indexes={@ORM\Index(name="pootb_orderfk", columns={"id_order"}), @ORM\Index(name="pootb_productfk", columns={"id_product"})})
 * @ORM\Entity
 */
class ProductsonorderTb
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
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var \OrderTb
     *
     * @ORM\ManyToOne(targetEntity="OrderTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_order", referencedColumnName="id")
     * })
     */
    private $idOrder;

    /**
     * @var \ProductTb
     *
     * @ORM\ManyToOne(targetEntity="ProductTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     * })
     */
    private $idProduct;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getIdOrder(): ?OrderTb
    {
        return $this->idOrder;
    }

    public function setIdOrder(?OrderTb $idOrder): self
    {
        $this->idOrder = $idOrder;

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
