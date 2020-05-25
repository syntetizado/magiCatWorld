<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryTb
 *
 * @ORM\Table(name="category_tb")
 * @ORM\Entity
 */
class CategoryTb
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
     * @ORM\Column(name="parent", type="string", length=60, nullable=true)
     */
    private $parent_slg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=60, nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=60, nullable=true)
     */
    private $slug;

    private $child=[];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentSlg(): ?string
    {
        return $this->parent_slg;
    }

    public function setParentSlg(?string $parent_slg): self
    {
        $this->parent_slg = $parent_slg;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChild()
    {
        return $this->child;
    }

    public function addChild(array $child)
    {
        $this->child[] = $child;

        return $this;
    }


}
