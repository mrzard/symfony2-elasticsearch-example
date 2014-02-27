<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts\AbstractBaseEntity;

/**
 * Class Actor
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table("genres")
 */
class Genre extends AbstractBaseEntity
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=127, nullable=false)
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Movie")
     * @ORM\JoinTable(name="genres_movies",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id")}
     * )
     */
    protected $movies;

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|ArrayCollection
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param Movie $movie
     */
    public function addMovie(Movie $movie)
    {
        if ($this->movies->contains($movie)) {
            return;
        }
        $this->movies->add($movie);
    }

    /**
     * @param Movie $movie
     */
    public function removeMovie(Movie $movie)
    {
        $this->movies->remove($movie);
    }
}