<?php

function formatPrice(float $vlprice){
	
	return number_format($vlprice, 2, ',', '.');
    }



$app->run();
?>