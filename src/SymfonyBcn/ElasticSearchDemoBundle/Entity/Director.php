<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts\AbstractCinemaPerson;

/**
 * Class Director
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table("directors")
 */
class Director extends AbstractCinemaPerson
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Movie")
     * @ORM\JoinTable(name="directors_movies",
     *      joinColumns={@ORM\JoinColumn(name="director_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id")}
     * )
     */
    protected $movies;
}