<?php
class bitexthai
{
	var $api_key, $nonce, $signature, $twofa;
	var $api_url = 'https://bx.in.th/api/';
	var $msg;
	function __construct($api_key, $api_secret, $twofa=''){
		$this->api_key = $api_key;
		
		$mt = explode(' ', microtime());
		$this->nonce = $mt[1].substr($mt[0], 2, 6);
		
		$this->signature = hash('sha256', $api_key.$this->nonce.$api_secret);
		if($twofa != ''){
			$this->twofa = $twofa;
		}
	}
	
	function curl($data='', $endpoint=''){
		if($ch = curl_init ()){
			$data['key'] = $this->api_key;
			$data['nonce'] = $this->nonce;
			$data['signature'] = $this->signature;
			if($this->twofa != ''){
				$data['twofa'] = $this->twofa;
			}
			
			curl_setopt ( $ch, CURLOPT_URL, $this->api_url.$endpoint.'/');
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false ); 
			curl_setopt ( $ch, CURLOPT_POST, count($data));
			curl_setopt ( $ch, CURLOPT_POSTFIELDS,$data);
			
			$str = curl_exec ( $ch );
			
			curl_close ( $ch );
			return json_decode($str);
		}
		return false;
	}
	
	function order($pairing_id=1, $type='buy', $amount = 0, $rate = 0){
		$order = $this->curl(array('pairing' => $pairing_id,
								   'type' => $type,
								   'amount' => $amount,
								   'rate' => $rate), 'order');
		if(!$order->success){
			$this->msg = $order->error;
		}else{
			$this->msg = $order->order_id;
		}
		return $order->success;
	}
	
	function cancel($pairing_id=1, $order_id=0){
		$order = $this->curl(array('pairing' => $pairing_id,
								   'order_id' => $order_id),
								   'cancel');
		if(!$order->success){
			$this->msg = $order->error;
		}
		return $order->success;
	}
	
	function balance(){
		$balance = $this->curl('','balance');
		if($balance->success){
			return $balance->balance;
		}else{
			$this->msg = $balance->error;
			return false;
		}
	}
	
	function getorders($data=''){
		$orders = $this->curl($data,'getorders');
		if($orders->success){
			return $orders->orders;
		}else{
			$this->msg = $orders->error;
			return false;
		}
	}
	function history($data=''){
		$history = $this->curl($data,'history');
		if($history->success){
			return $history->transactions;
		}else{
			$this->msg = $history->error;
			return false;
		}
	}
	function deposit($currency = 'BTC', $new = false){
		$deposit = $this->curl(array('currency' => $currency, 'new' => $new),'deposit');
		if($deposit->success){
			return $deposit->address;
		}else{
			$this->msg = $deposit->error;
			return false;
		}
	}
	
	function withdraw($currency, $amount, $address){
		$withdraw = $this->curl(array('currency' => $currency, 'amount' => $amount, 'address' => $address),'withdrawal');
		if($withdraw->success){
			return $withdraw->withdrawal_id;
		}else{
			$this->msg = $withdraw->error;
			return false;
		}
	}
	
	function issue_option($strike_price, $option_volume, $ask_price, $pairing_id=1, $option_type='call', $expire_date='', $qty=1){
		if($expire_date==''){
			$expire_date = date("Y-m-d",strtotime("Next Friday"));
		}
		$issue = $this->curl(array('type' => $option_type, 
									  'strike' => $strike_price, 
									  'ask' => $ask_price,
									  'volume' => $option_volume,
									  'pairing' => $pairing_id,
									  'expire' => date("Y-m-d",strtotime($expire_date)),
									  'qty' => $qty
									  ),'option-issue');
		if($issue->success){
			return $issue->option_id;
		}else{
			$this->msg = $issue->error;
			return false;
		}
	}
	
	function cancel_option($option_id){
		$cancel = $this->curl(array('option_id' => $option_id),'option-cancel');
		if($cancel->success){
			return true;
		}else{
			$this->msg = $cancel->error;
			return false;
		}
	}
	
	function get_issues($pairing_id, $expire_date='', $type=''){
		$params = array('pairing' => $pairing_id);
		if($expire_date != ''){
			$params['expire'] = date("Y-m-d",strtotime($expire_date));
		}
		if(in_array($type,array('put','call'))){
			$params['type'] = $type;
		}
		return $this->curl($params,'option-myissue');
	}
	
	function bid_option($option_id, $bid, $qty=1, $fill_kill = false){
		$params = array('option_id' => $option_id,
						'bid' => $bid,
						'qty' => $qty,
						'fill_kill' => ($fill_kill ? 1 : 0));
		$bid = $this->curl($params,'option-bid');
		if($bid->success){
			return $bid;
		}else{
			$this->msg = $bid->error;
			return false;
		}
	}
	
	function get_bids($pairing_id, $expire_date='', $type=''){
		$params = array('pairing' => $pairing_id);
		if($expire_date != ''){
			$params['expire'] = date("Y-m-d",strtotime($expire_date));
		}
		if(in_array($type,array('put','call'))){
			$params['type'] = $type;
		}
		return $this->curl($params,'option-mybid');
	}
	
	function get_options($pairing_id, $expire_date='', $type=''){
		$params = array('pairing' => $pairing_id);
		if($expire_date != ''){
			$params['expire'] = date("Y-m-d",strtotime($expire_date));
		}
		if(in_array($type,array('put','call'))){
			$params['type'] = $type;
		}
		return $this->curl($params,'option-myoptions');
	}
	
	function sell_option($option_id, $ask){
		$params = array('option_id' => $option_id,
						'ask' => $ask);
		$sell = $this->curl($params,'option-sell');
		if($sell->success){
			return $sell;
		}else{
			$this->msg = $sell->error;
			return false;
		}
	}
	
	function exercise_option($option_id){
		$params = array('option_id' => $option_id);
		$exercise = $this->curl($params,'option-exercise');
		if($exercise->success){
			return true;
		}else{
			$this->msg = $exercise->error;
			return false;
		}
	}
	
	function option_history($pairing_id){
		$params = array('pairing' => $pairing_id);
		$history = $this->curl($params,'option-history');
		if($history->success){
			return $history;
		}else{
			$this->msg = $history->error;
			return false;
		}
	}
	function listBillGroups(){
		$billers = $this->curl('','billgroup');
		if(!$billers->success){
			$this->msg = $billers->error;
			return false;
		}
		return $billers->groups;
	}
	
	function listBillers($group_id=1){
		$billers = $this->curl(array('group_id' => 1),'biller');
		if(!$billers->success){
			$this->msg = $billers->error;
			return false;
		}
		return $billers->providers;
	}
	
	function billPayment($provider,$amount, $account){
		$params = array('biller' => (int)$provider,
						'amount' => $amount,
						'account' => $account);
		$bill = $this->curl($params,'billpay');
		if(!$bill->success){
			$this->msg = $bill->error;
			return false;
		}
		return $bill->withdrawal_id;
	}
}