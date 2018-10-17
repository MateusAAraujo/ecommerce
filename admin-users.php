<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//rotas para listar todos os usuários
$app->get('/admin/users', function(){
    
    User::verifyLogin();
    
    $search = (isset($_GET['search'])) ? $_GET['search'] : '';
    
    $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
    
    if ($search != ''):
	$pagination = User::getPageSearch($search, $page);
    else:
	$pagination = User::getPage($page);
    endif;

    $pages = [];
    
    for($x = 0; $x < $pagination['pages']; $x++):
	
	array_push($pages, [
	    'href'=>'/admin/users?'.http_build_query([
	    'page'=>$x+1,
	    'search'=>$search
	]),
	    'text'=>$x+1
	
	]);    
	
    endfor;
    
    $page = new PageAdmin();
    
    $page->setTpl('users', array(
	'users'=>$pagination['data'],
	'search'=>$search,
	'pages'=>$pages
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


?>