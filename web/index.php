<?php

require_once './../vendor/autoload.php';


/**
 * Neo4j Connection building
 */
$url = parse_url(getenv('GRAPHENEDB_URL'));
$client = new Neoxygen\NeoClient\Client();
$client->addConnection('default', $url['scheme'], $url['host'], $url['port'], true, $url['user'], $url['pass'])
    ->build();
$formatter = new Neoxygen\NeoClient\Formatter\ResponseFormatter();

/**
 * Silex bootstrap
 */
$app = new Silex\Application();
$app['debug'] = true;
$app['neo'] = $client;
$app['formatter'] = $formatter;
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