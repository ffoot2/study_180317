<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$loader = new Twig_Loader_Filesystem(__DIR__. '/views');
$twig = new Twig_Environment($loader);
$template = $twig->loadTemplate("hello.twig");

ORM::configure('mysql:host=127.0.0.1;dbname=blog');
ORM::configure('username', 'root');
ORM::configure('password', '');
ORM::configure('driver_options', [
    PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8',
    PDO::ATTR_EMULATE_PREPARES         => false,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
]);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get('/hello/{name}', function($name) use($template) {
  #$airticles = ORM::for_table('airticle')->find_many();
  #$user = ORM::for_table('user')->find_many();
  #return $template->render(['user' => $user]);
  $airticles = ORM::for_table('airticle')->find_many();
  return $template->render(['airticle' => $airticles]);
});

$app->run();
?>
