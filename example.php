<?php

include_once('bitexthai.php');

$bitexthai = new bitexthai('your-api-key','your-api-secret','2 factor auth (if required)');

$test = 'deposit';

switch($test){
	case 'order':
		if($bitexthai->order(1, 'buy', .0589, 0.0024)){
			$order_id = $bitexthai->msg;
			echo 'Order has been placed! Order ID: '.$order_id;
		}else{
			echo 'Order failed: '.$bitexthai->msg;
		}
	break;
	case 'cancel':
		if($bitexthai->cancel(1, 11)){
			echo 'Order Cancel';
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'balance':
		if($balance = $bitexthai->balance()){
			print_r($balance);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'getorders':
		if($orders = $bitexthai->getorders(array('type' => 'buy'))){
			print_r($orders);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'history':
		if($history = $bitexthai->history()){
			print_r($history);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'deposit':
		if($address = $bitexthai->deposit('BTC',true)){
			echo $address;
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'withdraw':
		if($withdraw = $bitexthai->withdraw('BTC',.59546, '14oPLjoUQ2NrKgKbnaZZkLcky2d5UuhBKd')){
			echo 'Done, withdrawal ID: '.$withdraw;
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
}
?>