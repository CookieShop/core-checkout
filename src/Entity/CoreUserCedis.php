<?php

namespace Adteam\Core\Checkout\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoreUserCedis
 *
 * @ORM\Table(name="core_user_cedis", indexes={@ORM\Index(name="core_user_cedis_ibfk_1", columns={"user_id"}), @ORM\Index(name="core_user_cedis_ibfk_2", columns={"cedis_id"})})
 * @ORM\Entity(repositoryClass="Adteam\Core\Checkout\Repository\CoreUserCedisRepository")
 */
class CoreUserCedis
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Adteam\Core\Admin\Checkout\Entity\OauthUsers
     *
     * @ORM\ManyToOne(targetEntity="Adteam\Core\Checkout\Entity\OauthUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;

    /**
     * @var \Adteam\Core\Admin\Checkout\Entity\CoreCedis
     *
     * @ORM\ManyToOne(targetEntity="Adteam\Core\Checkout\Entity\CoreCedis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cedis_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $cedis;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \Adteam\Core\Checkout\Entity\OauthUsers $user
     *
     * @return CoreUserCedis
     */
    public function setUser(\Adteam\Core\Checkout\Entity\OauthUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Adteam\Core\Checkout\Entity\OauthUsers
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set cedis
     *
     * @param \Adteam\Core\Checkout\Entity\CoreCedis $cedis
     *
     * @return CoreUserCedis
     */
    public function setCedis(\Adteam\Core\Checkout\Entity\CoreCedis $cedis = null)
    {
        $this->cedis = $cedis;

        return $this;
    }

    /**
     * Get cedis
     *
     * @return \Adteam\Core\Checkout\Entity\CoreCedis
     */
    public function getCedis()
    {
        return $this->cedis;
    }
}

