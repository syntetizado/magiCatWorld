<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactformTb
 *
 * @ORM\Table(name="contactform_tb")})
 * @ORM\Entity
 */
class ContactformTb
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
     * @ORM\Column(name="topic", type="string", length=65, nullable=true)
     */
    private $topic;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="text", length=65, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="text", length=65, nullable=true)
     */
    private $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;

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

    public function getName(): ?String
    {
        return $this->name;
    }

    public function setName(?String $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?String
    {
        return $this->email;
    }

    public function setEmail(?String $email): self
    {
        $this->email = $email;

        return $this;
    }


}
