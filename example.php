<?php

include_once('bitexthai.php');

$bitexthai = new bitexthai('your-api-key','your-api-secret','2FA code is enabled');

$test = 'option_history';

switch($test){
	case 'order':
		if($bitexthai->order(1, 'buy', .0589, 0.0024)){  // Currency Pairing ID, Buy/Sell, Amount, Rate
			$order_id = $bitexthai->msg;
			echo 'Order has been placed! Order ID: '.$order_id;
		}else{
			echo 'Order failed: '.$bitexthai->msg;
		}
	break;
	case 'cancel':
		if($bitexthai->cancel(1, 11)){ // Currency Pairing ID, Order ID
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
		if($withdraw = $bitexthai->withdraw('BTC',.59546, '14oPLjoUQ2NrKgKbnaZZkLcky2d5UuhBKd')){ // Currency, Amount, Address
			echo 'Done, withdrawal ID: '.$withdraw;
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'issue':
		if($issue = $bitexthai->issue_option(10000, 1, 100, 1, 'call', date("Y-m-d",strtotime("Next Friday")),3)){ // Issue a call for 10,000THB per 1BTC with a price of 100THB
			echo 'Option IDs: ';
			print_r($issue);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'cancel_option':
		if($bitexthai->cancel_option(32)){
			echo 'Option Cancelled ';
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'get_issues':
		$issues = $bitexthai->get_issues(1,'2014-11-07',''); // Get issues for pairing ID 1 (BTC/THB)
		print_r($issues);
	break;
	case 'bid_option':
		if($bid = $bitexthai->bid_option(33,100,3)){ // Bid on 3 options like option_id 33 for a price of 100 (THB)
			echo 'Bid entered';
			print_r($bid);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'get_bids':
		$bids = $bitexthai->get_bids(1,'2014-11-07',''); // Get bids for pairing ID 1 (BTC/THB)
		print_r($bids);
	break;
	case 'get_options':
		$options = $bitexthai->get_options(1,'2014-11-07',''); // Get bids for pairing ID 1 (BTC/THB)
		print_r($options);
	break;
	case 'sell_option':
		if($sell = $bitexthai->sell_option(32, 150)){ // Sell option 32 for 150THB
			echo 'Sell success:';
			print_r($sell);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'exercise_option':
		if($bitexthai->exercise_option(32)){ // Exercise option ID 32
			echo 'Option was successfully exercised';
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
	case 'option_history':
		if($history = $bitexthai->option_history(2)){ // Get history for pairing ID 1 (THB/BTC)
			echo 'History:';
			print_r($history);
		}else{
			echo 'Failed: '.$bitexthai->msg;
		}
	break;
}
?>