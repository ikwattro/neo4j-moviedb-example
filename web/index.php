<?php

require_once './../vendor/autoload.php';

use Neoxygen\NeoClient\ClientBuilder;

/**
 * Neo4j Connection building
 */
$url = array(
    'scheme' => 'http',
    'host' => 'localhost',
    'port' => 7474,
    'user' => 'neo4j',
    'password' => 'neo4j'
);
if (false !== getenv('GRAPHENEDB_URL' || false !== 'GRAPHSTORY_URL')) {
    $url = parse_url('http://neo4j:error@localhost:7676');
}
$client = ClientBuilder::create()
    ->addConnection('default', $url['scheme'], $url['host'], $url['port'], true, $url['user'], $url['pass'])
    ->setAutoFormatResponse(true)
    ->build();

/**
 * Silex bootstrap
 */
$app = new Silex\Application();
$app['debug'] = true;
$app['neo'] = $client;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../src/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', 'NeoApp\\Controller\\WebController::home')
    ->bind('homepage');
$app->get('/actor/{name}', 'NeoApp\\Controller\\WebController::getActor')
    ->bind('actor');
$app->get('/menu_actors', 'NeoApp\\Controller\\WebController::actorsListMenu')
    ->bind('actors_list_menu');
$app->get('/movie/{title}', 'NeoApp\\Controller\\WebController::getMovie')
    ->bind('movie');
$app->get('/menu_movies', 'NeoApp\\Controller\\WebController::moviesListMenu')
    ->bind('movies_list_menu');
$app->get('/importdb', 'NeoApp\\Controller\\WebController::importDB');

$app->run();