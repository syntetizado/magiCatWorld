<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTb
 *
 * @ORM\Table(name="user_tb")
 * @ORM\Entity
 */
class UserTb
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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=65, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=65, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=65, nullable=false)
     */
    private $nick;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=65, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname1", type="string", length=65, nullable=true)
     */
    private $surname1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname2", type="string", length=65, nullable=true)
     */
    private $surname2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direction", type="string", length=65, nullable=true)
     */
    private $direction;

    /**
     * @var int|null
     *
     * @ORM\Column(name="phone", type="integer", nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=65, nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=65, nullable=true)
     */
    private $slug;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="register_date", type="date", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $registerDate = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): self
    {
        $this->nick = $nick;

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

    public function getSurname1(): ?string
    {
        return $this->surname1;
    }

    public function setSurname1(?string $surname1): self
    {
        $this->surname1 = $surname1;

        return $this;
    }

    public function getSurname2(): ?string
    {
        return $this->surname2;
    }

    public function setSurname2(?string $surname2): self
    {
        $this->surname2 = $surname2;

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

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

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

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
    }

    public function setRegisterDate(?\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }


}
