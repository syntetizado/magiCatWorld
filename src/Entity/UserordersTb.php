<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserordersTb
 *
 * @ORM\Table(name="userorders_tb", indexes={@ORM\Index(name="fk_orders_userid", columns={"id_user"}), @ORM\Index(name="fk_orders_orderid", columns={"id_order"})})
 * @ORM\Entity
 */
class UserordersTb
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
     * @var bool
     *
     * @ORM\Column(name="selected", type="boolean", nullable=false)
     */
    private $selected = '0';

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
     * @var \UserTb
     *
     * @ORM\ManyToOne(targetEntity="UserTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;


}
