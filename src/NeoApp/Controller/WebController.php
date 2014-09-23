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
        $formatter = $application['formatter'];

        $q = 'MATCH p=(a:Actor { name: {props}.name })-[:ACTS_IN]-() RETURN p';
        $p = array(
            'props' => array(
                'name' => $name
            )
        );

        $response = $client->sendCypherQuery($q, $p, null, array('graph'));
        $result = $formatter->format($response);

        $actors = $result->getNodesByLabel('Actor');
        $actors = array_shift($actors);



        return $application['twig']->render('actorShow.twig', array(
            'actor' => $actors
        ));
    }

    public function getMovie($title, Request $request, Application $application)
    {
        $client = $application['neo'];
        $formatter = $application['formatter'];

        $q = 'MATCH p=(m:Movie {title: {props}.title })-[:ACTS_IN]-() RETURN p';
        $p = array('props' => array(
            'title' => $title
        ));

        $response = $client->sendCypherQuery($q, $p, null, array('graph'));
        $result = $formatter->format($response);

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
        $response = $client->sendCypherQuery($query, array(), null, array('graph'));
        $result = $app['formatter']->format($response);

        $actors = $result->getNodesByLabel('Actor');

        return $app['twig']->render('menu_actors.twig', array(
            'actors' => $actors
        ));
    }

    public function moviesListMenu(Request $request, Application $application)
    {
        $client = $application['neo'];
        $query = 'MATCH (m:Movie) RETURN m';

        $response = $client->sendCypherQuery($query, array(), null, array('graph'));
        $result = $application['formatter']->format($response);

        $movies = $result->getNodesByLabel('Movie');

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

        $response = $client->sendCypherQuery($q);
        $result = json_decode($response, true);

        if (empty($result['errors'])) {
            return $application->redirect('/');
        }

        $application->abort('500', 'Impossible to import DB');


    }

    private function resetDB($client)
    {
        $q = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n';

        $client->sendCypherQuery($q);
    }
}