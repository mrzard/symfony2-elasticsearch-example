<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Abstracts\AbstractBaseEntity;

/**
 * Class Actor
 * @package SymfonyBcn\ElasticSearchDemoBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table("movies")
 */
class Movie extends AbstractBaseEntity
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
     * @ORM\Column(name="release_date", type="date", nullable=false)
     */
    protected $releaseDate;

    /**
     * @var string
     * @ORM\Column(name="overview", type="text")
     */
    protected $overview = "";

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Director", mappedBy="movies")
     */
    protected $directors;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Genre", mappedBy="movies")
     */
    protected $genres;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Actor", mappedBy="movies")
     */
    protected $actors;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @return string
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * @param $overview
     *
     * @return $this
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;

        return $this;
    }

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
     * @param $releaseDate
     *
     * @return $this
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;
        if (!($releaseDate instanceof \DateTime)) {
            $this->releaseDate = new \DateTime($releaseDate);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }
}