<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Controller;

use Elastica\Facet\Terms;
use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @package SymfonyBcn\ElasticSearchDemoBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Default action, Welcome! :D
     *
     * @return array
     *
     * @Template()
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Search page
     *
     * @Template()
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        $searchType = $request->get('search_type', 'movie');
        $searchQuery = $request->get('query', '');
        $type = $this->get('fos_elastica.index.symfony_bcn_example.'.$searchType);
        $query = new Query();
        $innerQuery = new Query\Match();
        $innerQuery->setField('name', array('query' => $searchQuery));
        $query->setQuery($innerQuery);
        $query->setSize(1000000);
        $query->setExplain(true);
        $finalResults = array();
        foreach ($type->search($query)->getResults() as $result) {
            $finalResults[] = $result->getData();
        }

        return array('results' => $finalResults);
    }


    /**
     * @param Request $request
     *
     * @return array
     *
     * @Route("movie-details/{id}", name="movie_details", defaults={"type":"movie"})
     * @Template("ElasticSearchDemoBundle:Default:movie_data.html.twig")
     */
    public function movieDataAction(Request $request)
    {
        $query = new Query();

        $type = $request->get('type');
        $nestedField = $type.'s'; //nested field is plural
        $queryBool = new Query\Bool();
        $queryNestedTerm = new Query\Nested();
        $queryNestedTerm->setPath($nestedField);
        $queryTerm = new Query\Term();
        $queryTerm->setTerm('id', $request->get('id'));
        $queryNestedTerm->setQuery($queryTerm);
        $queryBool->addMust($queryNestedTerm);
        $query->setQuery($queryTerm);
        $query->setSort(array('releaseDate' => 'desc'));

        $facetActors = new Terms('actors');
        $facetActors->setField("actors.name");
        $facetActors->setSize(100000);
        $query->addFacet($facetActors);

        $facetDirectors = new Terms('directors');
        $facetDirectors->setField("directors.name");
        $facetDirectors->setSize(100000);
        $query->addFacet($facetDirectors);

        $facetsGenres = new Terms('genres');
        $facetsGenres->setField("genres.name");
        $facetsGenres->setSize(100000);
        $query->addFacet($facetsGenres);

        $movieType = $this->get('fos_elastica.index.symfony_bcn_example.movie');
        $results = $movieType->search($query);
        $genres = !empty($results->getFacets()['genres']) ? $results->getFacets()['genres']['terms'] : array();
        $cast = !empty($results->getFacets()['actors']) ? $results->getFacets()['actors']['terms'] : array();
        $directors = !empty($results->getFacets()['directors']) ? $results->getFacets()['directors']['terms'] : array();
        $movie = $movieType->search(new Query\Term(array('id' => $request->get('id'))))->getResults()[0];

        return array('movie' => $movie, 'genres' => $genres, 'cast' => $cast, 'directors' => $directors);
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @Route("actor-details/{id}", name="actor_details", defaults={"type":"actor"})
     * @Route("director-details/{id}", name="director_details", defaults={"type":"director"})
     * @Template("ElasticSearchDemoBundle:Default:person_data.html.twig")
     */
    public function personDataAction(Request $request)
    {
        $query = new Query();

        $type = $request->get('type');
        $nestedField = $type.'s'; //nested field is plural
        $queryBool = new Query\Bool();
        $queryNestedTerm = new Query\Nested();
        $queryNestedTerm->setPath($nestedField);
        $queryTerm = new Query\Term();
        $queryTerm->setTerm($nestedField.'.id', $request->get('id'));
        $queryNestedTerm->setQuery($queryTerm);
        $queryBool->addMust($queryNestedTerm);
        $query->setQuery($queryTerm);
        $query->setSort(array('releaseDate' => 'desc'));

        $facetMovies = new Terms('movies');
        $facetMovies->setField("id");
        $facetMovies->setSize(100000);
        $query->addFacet($facetMovies);

        $facetsGenres = new Terms('genres');
        $facetsGenres->setField("genres.name");
        $facetsGenres->setSize(100000);
        $query->addFacet($facetsGenres);

        $personType = $this->get('fos_elastica.index.symfony_bcn_example.'.$type);
        $movieType = $this->get('fos_elastica.index.symfony_bcn_example.movie');

        $results = $movieType->search($query);
        $personGenres = $results->getFacets()['genres']['terms'];
        $person = $personType->search(new Query\Term(array('id' => $request->get('id'))))->getResults()[0];
        $movies = $results->getResults();

        return array('person' => $person, 'movies' => $movies, 'genres' => $personGenres);
    }
}