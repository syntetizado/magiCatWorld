<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReviewTb
 *
 * @ORM\Table(name="review_tb", indexes={@ORM\Index(name="reviewtb_productfk", columns={"id_product"}), @ORM\Index(name="reviewtb_userfk", columns={"id_user"})})
 * @ORM\Entity
 */
class ReviewTb
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
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=65, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=65, nullable=true)
     */
    private $slug;

    /**
     * @var int|null
     *
     * @ORM\Column(name="banned", type="integer", nullable=true)
     */
    private $banned;

    /**
     * @var \ProductTb
     *
     * @ORM\ManyToOne(targetEntity="ProductTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     * })
     */
    private $idProduct;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBanned(): ?int
    {
        return $this->banned;
    }

    public function setBanned(?int $banned): self
    {
        $this->banned = $banned;

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
