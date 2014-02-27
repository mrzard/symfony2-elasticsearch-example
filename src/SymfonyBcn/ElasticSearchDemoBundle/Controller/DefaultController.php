<?php

namespace SymfonyBcn\ElasticSearchDemoBundle\Controller;

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

        $movieType = $this->get('fos_elastica.index.symfony_bcn_example.movie');
        $movie = $movieType->search(new Query\Term(array('id' => $request->get('id'))))->getResults()[0];

        return array('movie' => $movie);
    }
}