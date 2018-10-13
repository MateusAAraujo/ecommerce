<?php

use \Hcode\Model\User;

function formatPrice($vlprice){
	
	return number_format($vlprice, 2, ',', '.');
    }
    
function checkLogin($inadmin = true){
    
    return User::checklogin($inadmin);
}

function getUserName(){
    
    $user = User::getFromSession();
    
    return $user->getdesperson();
}

?>