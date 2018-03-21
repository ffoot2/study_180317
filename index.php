<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Common.php';
use Symfony\Component\HttpFoundation\Request;
$app = new Silex\Application();

$loader = new Twig_Loader_Filesystem(__DIR__. '/views');
$twig = new Twig_Environment($loader);

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

//記事一覧ページ
$app->get('/', function() use($twig) {
  #$airticles = ORM::for_table('airticle')->find_many();
  #$user = ORM::for_table('user')->find_many();
  #return $template->render(['user' => $user]);
  $articles = ORM::for_table('airticle')->find_many();

  $template = $twig->loadTemplate("index.twig");
  // var_dump($articles);

  return $template->render(['articles' => $articles]);
});

//記事詳細
$app->get('/articles/{id}', function($id) use($twig) {
  $template = $twig->loadTemplate("article.twig");
  $article = ORM::for_table('airticle')->where('id', $id)->find_one();
  return $template->render(['article' => $article]);
});

//ログイン画面
$app->get('/login/', function() use($twig) {
  $template = $twig->loadTemplate("login.twig");
  return $template->render([]);
});

$app->post('/login/', function() use($twig){
  count(ORM::for_table('airticle')->find_result_set());

});

//管理側記事一覧
$app->get('/admin/', function() use($twig) {
  //die("x");
  $template = $twig->loadTemplate("admin.twig");
  //die("a");
   $articles = ORM::for_table('airticle')->find_many();
  // var_dump($articles);die();
  return $template->render(['articles' => $articles]);
});

//管理側削除
$app->get('/delete/{id}', function($id) use($twig, $app) {
  #die("x");
  $template = $twig->loadTemplate("admin.twig");
  #die("a");

  $record = ORM::for_table('airticle')->where('id', $id)->find_one();
  $record->delete();
  return $app->redirect('/admin/');
});

//編集画面
$app->get('/edit/{id}', function($id) use($twig) {
  #die("x");
  $template = $twig->loadTemplate("edit.twig");
  #die("a");

  $article = ORM::for_table('airticle')->where('id', $id)->find_one();
  return $template->render(['article' => $article]);
});

//編集処理
$app->post('/edit/{id}', function($id, Request $request) use($twig, $app){
#$app->post('/edit/{id}', function($id) use($twig, $app){
#  die("post");
  $template = $twig->loadTemplate("edit.twig");
  $title = $request->get('title');
  $body = $request->get('body');
  #die("iii");
  $update = ORM::for_table('airticle')->where('id', $id)->find_one()
  ->set('title', $title)
  ->set('body', $body);
  $update->save();
  return $app->redirect('/edit/' . $id);
});


$app->run();
?>
