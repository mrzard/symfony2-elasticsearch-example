<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts\AbstractCinemaPerson;

/**
 * Class Actor
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table("actors")
 */
class Actor extends AbstractCinemaPerson
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Movie")
     * @ORM\JoinTable(name="actors_movies",
     *      joinColumns={@ORM\JoinColumn(name="actor_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id")}
     * )
     */
    protected $movies;
}