<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactformTb
 *
 * @ORM\Table(name="contactform_tb", indexes={@ORM\Index(name="contactformtb_userfk", columns={"id_user"})})
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
     * @ORM\Column(name="date", type="date", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

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
