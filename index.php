<?php
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
    $page = new Page();

    $page->setTpl("index");
});

$app->get('/admin', function() {
    
    User::verifyLogin();
    
    $page = new PageAdmin();

    $page->setTpl("index");
});

//Rota login
$app->get('/admin/login', function() {
    
    $page = new PageAdmin([
	'header'=>false,
	'footer'=>false,	
    ]);

    $page->setTpl("login");
});

$app->post('/admin/login', function(){
    
    User::login($_POST['deslogin'], $_POST['despassword']);
    
    header('location: /admin');    
    exit;
});

//Rota logout
$app->get('/admin/logout', function() {
    User::logout();
    
    header('Location: /admin/login');
    exit;
});

//rotas para listar todos os usuários
$app->get('/admin/users', function(){
    
    User::verifyLogin();
    
    $users = User::listAll();
    
    $page = new PageAdmin();
    
    $page->setTpl('users', array(
	'users'=>$users
    ));    
});

//Rota para cadastrar novos usuários
$app->get('/admin/users/create', function(){
    
    User::verifyLogin();
    
    $page = new PageAdmin();
    
    $page->setTpl('users-create');    
});

//Rota para deleta usuários
$app->get('/admin/users/:iduser/delete', function($iduser){
    
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$iduser);
    
    $user->delete();
    
    header('Location: /admin/users');
    exit;
});

//Rota para carregar e atualizar dados de usuários
$app->get('/admin/users/:iduser', function($iduser){
    
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$iduser);
    
    $page = new PageAdmin();
    
    $page->setTpl('users-update', array(
	'user'=>$user->getValues()
    ));    
});

//Rota salvar usuários novos
$app->post('/admin/users/create', function(){
    
    User::verifyLogin();
    
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;
    
    $user->setData($_POST);
 
    $user->save();
    
    header('Location: /admin/users');
    exit;
});

//Salva atualização de dados de usuários
$app->post('/admin/users/:iduser', function($iduser){
    
    User::verifyLogin();
    
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;
    
    $user->get((int)$iduser);
    
    $user->setData($_POST);
    
    $user->update();
    
    header('Location: /admin/users');
    exit;
});

//Rota para recuperar senha
$app->get('/admin/forgot', function() {
    
    $page = new PageAdmin([
	'header'=>false,
	'footer'=>false
    ]);
    
    $page->setTpl('forgot');
});

//Rota para checar email e validar email
$app->post('/admin/forgot', function() {
    
    $user = User::getForgot($_POST['email']);
    
    header('Location: /admin/forgot/sent');
    exit;
});

//Rota envio email recuperação de senha
$app->get('/admin/forgot/sent', function() {
    
    $page = new PageAdmin([
	'header'=>false,
	'footer'=>false
    ]);
    
    $page->setTpl('forgot-sent');
});

//Rota para resetar senha antiga e gerar nova senha
$app->get('/admin/forgot/reset', function() {
    
    $user = User::validForgotDecrypt($_GET['code']);
    
    $page = new PageAdmin([
	'header'=>false,
	'footer'=>false
    ]);
    
    $page->setTpl('forgot-reset', array(
	'name'=>$user['desperson'],
	'code'=>$_GET['code']
    ));
});

//Rota para validar s setar nova senha
$app->post('/admin/forgot/reset', function() {
    
    $forgot = User::validForgotDecrypt($_POST['code']);
    
    User::setForgotUsed($forgot['idrecovery']);
    
    $user = new User();
    
    $user->get((int)$forgot['iduser']);
    
    //Encriptografa a nova senha
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost'=>12]);
    
    //Passa nova senha (hash) criptografada para salvar no bd
    $user->setPassword($password);
    
    $page = new PageAdmin([
	'header'=>false,
	'footer'=>false
    ]);
    
    $page->setTpl('forgot-reset-success');
    
});

$app->run();
?>