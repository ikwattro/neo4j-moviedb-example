<?php

namespace NeoApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WebController
{
    public function home(Request $request, Application $application)
    {
        return $application['twig']->render('index.twig', array(

        ));
    }

    public function getActor($name, Request $request, Application $application)
    {
        $client = $application['neo'];

        $q = 'MATCH p=(a:Actor { name: {props}.name })-[:ACTS_IN]-() RETURN p';
        $p = array(
            'props' => array(
                'name' => $name
            )
        );

        $result = $client->sendCypherQuery($q, $p)->getResult();

        $actors = $result->getNodesByLabel('Actor');
        $actors = array_shift($actors);

        return $application['twig']->render('actorShow.twig', array(
            'actor' => $actors
        ));
    }

    public function getMovie($title, Request $request, Application $application)
    {
        $client = $application['neo'];

        $q = 'MATCH p=(m:Movie {title: {props}.title })-[:ACTS_IN]-() RETURN p';
        $p = array('props' => array(
            'title' => $title
        ));

        $result = $client->sendCypherQuery($q, $p)->getResult();

        $movies = $result->getNodesByLabel('Movie');
        $movie = array_shift($movies);
        $actors = $movie->getInboundRelationships();


        return $application['twig']->render('movieShow.twig', array(
            'movie' => $movie,
            'actors' => $actors
        ));
    }

    public function actorsListMenu(Request $request, Application $app)
    {
        $client = $app['neo'];
        $query = 'MATCH (n:Actor) RETURN n';
        $result = $client->sendCypherQuery($query, array())->getResult();

        $actors = $result->get('n');

        return $app['twig']->render('menu_actors.twig', array(
            'actors' => $actors
        ));
    }

    public function moviesListMenu(Request $request, Application $application)
    {
        $client = $application['neo'];
        $query = 'MATCH (m:Movie) RETURN m';

        $result = $client->sendCypherQuery($query)->getResult();

        $movies = $result->get('m');

        return $application['twig']->render('menu_movies.twig', array(
            'movies' => $movies
        ));
    }

    public function importDB(Request $request, Application $application)
    {
        $client = $application['neo'];

        $this->resetDB($client);

        $q = "CREATE (matrix1:Movie { title : 'The Matrix', year : '1999-03-31' })
CREATE (matrix2:Movie { title : 'The Matrix Reloaded', year : '2003-05-07' })
CREATE (matrix3:Movie { title : 'The Matrix Revolutions', year : '2003-10-27' })
CREATE (keanu:Actor { name:'Keanu Reeves' })
CREATE (laurence:Actor { name:'Laurence Fishburne' })
CREATE (carrieanne:Actor { name:'Carrie-Anne Moss' })
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix1)
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix2)
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix3)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix1)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix2)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix3)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix1)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix2)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix3)";

        $client->sendCypherQuery($q);

        return $application->redirect('/');


    }

    private function resetDB($client)
    {
        $q = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n';

        $client->sendCypherQuery($q);
    }
}