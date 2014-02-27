<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Movie;

/**
 * Class AbstractCinemaPerson
 *
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractCinemaPerson extends AbstractBaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=127, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="text", nullable=true)
     */
    protected $bio = "";

    /**
     * @var string
     *
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="date_of_death", type="date", nullable=true)
     */
    protected $dateOfDeath;


    /**
     * This column has to be overwritten in each child class
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $movies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    /**
     * @param string $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param string $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $dateOfDeath
     */
    public function setDateOfDeath($dateOfDeath)
    {
        $this->dateOfDeath = $dateOfDeath;
    }

    /**
     * @return string
     */
    public function getDateOfDeath()
    {
        return $this->dateOfDeath;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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