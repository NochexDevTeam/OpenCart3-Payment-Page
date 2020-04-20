<?php
// Nochex via form will work for both simple "Seller" account and "Merchant" account holders
// Nochex via APC maybe only avaiable to "Merchant" account holders only - site docs a bit vague on this point
class ControllerExtensionPaymentNochex extends Controller {
	public function index() {
		$this->load->language('extension/payment/nochex');
		$products = $this->cart->getProducts();
		setlocale(LC_MONETARY, 'en_GB');	
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['action'] = 'https://secure.nochex.com/default.aspx';

		// Nochex minimum requirements
		// The merchant ID is usually your Nochex registered email address but can be altered for "Merchant" accounts see below
		if ($this->config->get('payment_nochex_email') != $this->config->get('payment_nochex_merchant')) {
			// This MUST be changed on your Nochex account!!!!
			$data['merchant_id'] = $this->config->get('payment_nochex_merchant');
		} else {
			$data['merchant_id'] = $this->config->get('payment_nochex_email');
		}
	
		if($this->config->get('payment_nochex_debug')==1){
		
		$logger = new Log('nochex.log');
		$logger->write('Nochex - Log');
		
		}
		// XML Item Collection / Description
		
		if($this->config->get('payment_nochex_xmlcollection') == 1){
		
		$xmlCollection = "<items>";
		
		foreach ($products as $product) {
		
			$xmlCollection .= "<item><id>".$product['product_id']."</id><name>".preg_replace("/[^A-Za-z0-9  ]/", "", $product['name'])."</name><description>".preg_replace("/[^A-Za-z0-9  ]/", "", $product['model'])."</description><quantity>".$product['quantity']."</quantity><price>" . $product['price'] . "</price></item>";
		}
		
		$xmlCollection .= "</items>";
		
		
		$description = "Order :" . $this->session->data['order_id'] ;
		
		}else{
		$xmlCollection = "";
		$description = "Product Details: ";
		
		foreach ($products as $product) {
			$description .= " Product ID: ".$product['product_id'].", Product Name: ".preg_replace("/[^A-Za-z0-9  ]/", "", $product['name']).", Product Description: ".preg_replace("/[^A-Za-z0-9  ]/", "", $product['model']).", Product Quantity: ".$product['quantity'].", Product Price: &pound;" . $product['price'] . "   ";
		}
		
		$description .= ".";
		}
		
		if($this->config->get('payment_nochex_debug')==1){
		
		$logger->write('XMl Collection'.$xmlCollection);
		$logger->write('Description'.$xmlCollection);
		
		}
		
		if($this->config->get('payment_nochex_postage') == 1){
		$data['postage'] = $this->currency->format($this->session->data['shipping_method']['cost'], $order_info['currency_code'], false, false);
		$data['amount']  = $this->currency->format($order_info['total'], $order_info['currency_code'], FALSE, FALSE) - $this->currency->format($this->session->data['shipping_method']['cost'], $order_info['currency_code'], false, false);
		}else{
		$data['postage'] =  "";
		$data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		}
		
		if($this->config->get('payment_nochex_debug')==1){
		
		$logger->write('Amount = '. $data['amount']);
		$logger->write('Postage ='. $data['postage']);
		
		}
			

		$data['order_id'] = $this->session->data['order_id'];
		$data['description'] = $description;
		

		$data['billing_fullname'] = preg_replace("/[^A-Za-z  ]/", "", $order_info['payment_firstname']) . ' ' . preg_replace("/[^A-Za-z  ]/", "", $order_info['payment_lastname']);

		if (isset($order_info['payment_address_2'])) {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_1']) . "\r\n" . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_2']);
		} else {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_1']);
		}
		
		$data['billing_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_city']);
		$data['billing_postcode'] = $order_info['payment_postcode'];

		if ($this->cart->hasShipping()) {
			$data['delivery_fullname'] = preg_replace("/[^A-Za-z ]/", "", $order_info['shipping_firstname']) . ' ' . preg_replace("/[^A-Za-z  ]/", "", $order_info['shipping_lastname']);

			if (isset($order_info['shipping_address_2'])) {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']) . "\r\n" . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_2']);
			} else {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']);
			}
			$data['delivery_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['shipping_city']);
			$data['delivery_postcode'] = $order_info['shipping_postcode'];
		} else {
			$data['delivery_fullname'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_firstname']) . ' ' . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_lastname']);

			if (isset($order_info['payment_address_2'])) {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['payment_address_1']) . "\r\n" . preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['payment_address_2']);
			} else {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['shipping_address_1']);
			}
			$data['delivery_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_city']);
			$data['delivery_postcode'] = $order_info['payment_postcode'];
		}
		
		if($this->config->get('payment_nochex_hide') == 1){
		$data['hide_billing_details'] = "true";		
		}
		$data['xmlcollection'] = $xmlCollection;
		
		 
		$data['email_address'] = $order_info['email'];
		$data['customer_phone_number']= preg_replace("/[^0-9]/", "",$order_info['telephone']);
		$data['test'] = $this->config->get('payment_nochex_test');
		$data['success_url'] = $this->url->link('checkout/success', '', true);
		$data['cancel_url'] = $this->url->link('checkout/checkout', '', true);
		$data['declined_url'] = $this->url->link('extension/payment/nochex/callback', 'method=decline', true);
		$data['callback_url'] = $this->url->link('extension/payment/nochex/callback', 'order=' . $this->session->data['order_id'], true);
		
		if($this->config->get('payment_nochex_callback')==1){
		
		$data['optional_1'] = "Enabled";
		
		}else{		
		
		$data['optional_1'] = "Disabled";
		
		}
		
		if($this->config->get('payment_nochex_debug')==1){
		
		$logger->write('Success URL: '. $data['success_url']);
		$logger->write('Cancel URL: '. $data['cancel_url']);
		$logger->write('Declined URL: '. $data['declined_url']);
		$logger->write('APC / Callback URL: '. $data['callback_url']);
		
		}
		
		return $this->load->view('extension/payment/nochex', $data);
	}

	public function callback() {
		
		$logger = new Log('nochex.log');
		
		$this->load->language('extension/payment/nochex');

		if (isset($this->request->get['method']) && $this->request->get['method'] == 'decline') {
			$this->session->data['error'] = $this->language->get('error_declined');

			$this->response->redirect($this->url->link('checkout/cart'));
		}

		if (isset($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$this->session->data['error'] = $this->language->get('error_no_order');

			$this->response->redirect($this->url->link('checkout/cart'));
		}

		// Fraud Verification Step.
		$request = '';

		foreach ($this->request->post as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(stripslashes($value));
		}

			
		if(isset($this->request->post['optional_1']) == "Enabled"){

		$url = "https://secure.nochex.com/callback/callback.aspx";
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		if($_POST["transaction_status"] == "100"){
		$testStatus = "Test";
		}else{
		$testStatus = "Live";
		}
		
		if ($response=="AUTHORISED") {
			/*$Msg = "Callback was " . $response. ", and this was a " . $testStatus . " transaction. <br/> The transaction id for this payment is: ".$_POST["transaction_id"];
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('nochex_order_status_id'), $Msg, false);*/
			
			$Msg = "<ul style=\"list-style:none;\"><li>Callback: " . $response . "</li>";			
			$Msg .= "<li>Transaction Status: " . $testStatus . "</li>";			
			$Msg .= "<li>Transaction ID: ".$_POST["transaction_id"] . "</li>";
			$Msg .= "<li>Payment Received From: ".$_POST["email_address"] . "</li>";			
			$Msg .= "<li>Total Paid: ".$_POST["gross_amount"] . "</li></ul>";	
		
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_nochex_order_status_id'), $Msg, false);
			
			
		} else {
				
			$Msg = "<ul style=\"list-style:none;\"><li>Callback: " . $response . "</li>";			
			$Msg .= "<li>Transaction Status: " . $testStatus . "</li>";			
			$Msg .= "<li>Transaction ID: ".$_POST["transaction_id"] . "</li>";
			$Msg .= "<li>Payment Received From: ".$_POST["email_address"] . "</li>";			
			$Msg .= "<li>Total Paid: ".$_POST["gross_amount"] . "</li></ul>";	
			
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), $Msg, false);
		}

		if($this->config->get('payment_nochex_debug')==1){
		
		$logger->write('Callback Response: '. $response);
		$logger->write('Callback Message: '. $Msg);
		$logger->write('Order ID: '. $_POST["order_id"]);

		}
		// Since it returned, the customer should see success.
		// It's up to the store owner to manually verify payment.
		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));


}else{
	
		$url = "https://www.nochex.com/apcnet/apc.aspx";

		// Curl code to post variables back
		$ch = curl_init(); // Initialise the curl tranfer
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, trim($request, '&')); // Set POST fields
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: www.nochex.com"));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); // set connection time out variable - 60 seconds	
		//curl_setopt ($ch, CURLOPT_SSLVERSION, 6); // set openSSL version variable to CURL_SSLVERSION_TLSv1_2
		$output = curl_exec($ch); // Post back
		curl_close($ch);

		if (strcmp($output, 'AUTHORISED') == 0) {
		$Msg = "APC was " . $output. ", and this was a " . $_POST['status'] . " transaction.";
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_nochex_order_status_id'), $Msg, false);
			
		} else {
		$Msg = "APC was " . $output. ", and this was a " . $_POST['status'] . " transaction.";
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), $Msg, false);
		}

		if($this->config->get('payment_nochex_debug')==1){
		
		$logger->write('APC Response: '. $output);
		$logger->write('APC Message: '. $Msg);
		$logger->write('Order ID: '. $_POST["order_id"]);

		}
		// Since it returned, the customer should see success.
		// It's up to the store owner to manually verify payment.
		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));


}
	}
}
