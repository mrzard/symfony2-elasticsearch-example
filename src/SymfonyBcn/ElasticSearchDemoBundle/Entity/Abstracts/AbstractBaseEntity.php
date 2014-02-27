<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractCinemaPerson
 *
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts
 *
 * @ORM\MappedSuperclass
 */
class AbstractBaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}