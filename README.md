# NeoClient MovieDB Example

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy) | 1 dyno + 1 GrapheneDB chalk addon (all-free)

[Live App Here](http://neo4j-neoclient-moviedb.herokuapp.com/)

## Intro

Simple Silex App using [NeoClient](https://github.com/neoxygen/neo4j-neoclient).

The source code show how easy it is to set up the library and use it. It show also how handy is the response Formatter.

Handling queries responses from the ReST API is now funny :

In your controllers

```php
$result = $formatter->format($response);

$result->getNodes();

$result->getNodesByLabel('Actor');

$result->getInboundRelationships();
```

In the Twig template engine

```twig
{% for act in actor.outboundRelationships %}
    <li>In movie {{ act.endNode.properties.title }} as role {{ act.role }}</li>
{% endfor %}
```

---

Author : Christophe Willemsen ([@ikwattro](https://twitter.com/ikwattro))