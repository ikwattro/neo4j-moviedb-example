# NeoClient MovieDB Example

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy) | 1 dyno + 1 GrapheneDB chalk addon (all-free)

[Live App Here](http://neo4j-neoclient-moviedb.herokuapp.com/)

### Intro

Simple Silex App using [NeoClient](https://github.com/neoxygen/neo4j-neoclient).

### Test it locally :

1. Clone the repository and install dependencies

```bash
git clone git@github.com:ikwattro/neo4j-moviedb-example
cd neo4j-moviedb-example

composer install --no-dev
```

2. Modify the neo4j password defined in the `index.php` file line `15` by your neo4j database password

3. Run a webserver

```bash
php -S localhost:8000 -t web/
```

4. Head to the url `http://localhost:8000/importdb` in order to load the movies and actors in your database

!!!! This will erase your database content

If you don't want to erase, comment the line number `91` of the file `src/NeoApp/WebController.php`

5. After this, you should be headed to the application index page

---

Author : Christophe Willemsen ([@ikwattro](https://twitter.com/ikwattro))