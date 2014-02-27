<?php
namespace SymfonyBcn\ElasticSearchDemoBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Actor;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Director;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Genre;
use SymfonyBcn\ElasticSearchDemoBundle\Entity\Movie;

class PopulateDatabaseCommand extends Command
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $imdbIds = array();

    /**
     * @var string
     */
    protected $tmdbApiKey = "";

    /**
     * @var string
     */
    private $tmdbApiFindUrl = "https://api.themoviedb.org/3/find/#ID#?api_key=#APIKEY#&external_source=imdb_id";

    private $tmdbApiMovieUrl = "https://api.themoviedb.org/3/movie/#ID#?api_key=#APIKEY#";

    private $tmdbApiMovieCreditsUrl = "https://api.themoviedb.org/3/movie/#ID#/credits?api_key=#APIKEY#";

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this->setName('sfbcn:elasticsearchdemo:populatedb');
        $this->setDescription('Creates a base DB for the ElasticSearch - Symfony integration example');
    }

    /**
     * Constructor
     *
     * @param ObjectManager $manager
     * @param array         $imdbIds    Array of strings with IMDB id's separated in 'movie', 'director' and 'cast'
     * @param string        $tmdbApiKey ApiKey for tmdb
     */
    public function __construct(ObjectManager $manager, $imdbIds, $tmdbApiKey)
    {
        $this->manager = $manager;
        $this->imdbIds = $imdbIds;
        $this->tmdbApiKey = $tmdbApiKey;

        parent::__construct(null);
    }


    /**
     * Execute this command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadMovies($this->imdbIds['movie']);
    }


    protected function loadMovies($movieIds)
    {
        foreach ($movieIds as $movieId) {
            $url = str_replace(array('#ID#', '#APIKEY#'), array($movieId, $this->tmdbApiKey), $this->tmdbApiFindUrl);
            $dataArray = json_decode($this->runCurl($url));
            if (is_object($dataArray) && $dataArray->movie_results && !empty($dataArray->movie_results[0])) {
                //get movie data
                $movieData = $dataArray->movie_results[0];
                $urlExtraData = str_replace(
                    array('#ID#', '#APIKEY#'),
                    array($movieId, $this->tmdbApiKey),
                    $this->tmdbApiMovieUrl
                );
                $movieExtraData = json_decode($this->runCurl($urlExtraData));
                //get movie credits
                $urlCredits = str_replace(
                    array('#ID#', '#APIKEY#'),
                    array($movieData->id, $this->tmdbApiKey),
                    $this->tmdbApiMovieCreditsUrl
                );
                $movieCredits = json_decode($this->runCurl($urlCredits));
                $this->saveMovieToDb($movieExtraData, $movieCredits);
            }
        }
    }

    /**
     * Given an tmdb object, save it as a movie
     *
     * @param stdObject $movieData    Object with basic moovie data
     * @param stdObject $movieCredits Object with movie credits
     */
    private function saveMovieToDb($movieData, $movieCredits)
    {
        $movie = $this->manager->getRepository('ElasticSearchDemoBundle:Movie')->find($movieData->id);
        if (!$movie) {
            $movie = new Movie();
            $movie->setId($movieData->id);
            $movie->setName($movieData->original_title);
            $movie->setReleaseDate($movieData->release_date);
            $movie->setOverview($movieData->overview ? $movieData->overview : "");
            $this->manager->persist($movie);
        }

        foreach ($movieCredits->cast as $castedPerson) {
            $actor = $this->manager->getRepository('ElasticSearchDemoBundle:Actor')->find($castedPerson->id);
            if (!$actor) {
                $actor = new Actor();
                $actor->setId($castedPerson->id);
                $actor->setName($castedPerson->name);
                $this->manager->persist($actor);
            }
            $actor->addMovie($movie);
        }

        foreach ($movieCredits->crew as $crew) {
            if ($crew->job != 'Director') {
                continue;
            }
            $director = $this->manager->getRepository('ElasticSearchDemoBundle:Director')->find($crew->id);
            if (!$director) {
                $director = new Director();
                $director->setId($crew->id);
                $director->setName($crew->name);
                $this->manager->persist($director);
            }
            $director->addMovie($movie);
        }

        foreach ($movieData->genres as $movieGenre) {
            $genre = $this->manager->getRepository('ElasticSearchDemoBundle:Genre')->find($movieGenre->id);
            if (!$genre) {
                $genre = new Genre();
                $genre->setId($movieGenre->id);
                $genre->setName($movieGenre->name);
                $this->manager->persist($genre);
            }
            $genre->addMovie($movie);
        }

        $this->manager->flush();
    }


    /**
     * Runs a simple CURL get on $url
     *
     * @param $url
     *
     * @return mixed
     */
    protected function runCurl($url)
    {
        // Get cURL resource
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

}