<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderTb
 *
 * @ORM\Table(name="order_tb", indexes={@ORM\Index(name="ordertb_userfk", columns={"id_user"})})
 * @ORM\Entity
 */
class OrderTb
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_price", type="decimal", precision=65, scale=2, nullable=true)
     */
    private $totalPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direction", type="string", length=65, nullable=true)
     */
    private $direction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=65, nullable=true)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment", type="string", length=65, nullable=true)
     */
    private $payment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="delivery_status", type="string", length=65, nullable=true)
     */
    private $deliveryStatus;

    /**
     * @var \UserTb
     *
     * @ORM\ManyToOne(targetEntity="UserTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(?string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDeliveryStatus(): ?string
    {
        return $this->deliveryStatus;
    }

    public function setDeliveryStatus(?string $deliveryStatus): self
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    public function getIdUser(): ?UserTb
    {
        return $this->idUser;
    }

    public function setIdUser(?UserTb $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
