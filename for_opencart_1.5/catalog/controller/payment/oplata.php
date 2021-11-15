<?php
	class ControllerPaymentOplata extends Controller {

		protected $RESPONSE_SUCCESS = 'success';
		protected $RESPONSE_FAIL = 'failure';
		protected $ORDER_SEPARATOR = '_';
		protected $SIGNATURE_SEPARATOR = '|';
		protected $ORDER_APPROVED = 'approved';
		protected $ORDER_DECLINED = 'declined';
		protected $ORDER_PROCESSING = 'processing';
		protected $ORDER_EXPIRED = 'expired';

		protected function index() {
			$this->language->load('payment/oplata');
			$order_id = $this->session->data['order_id'];
			$this->load->model('checkout/order');
//			//$products=$this->cart->getProducts();
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			$backref = $this->url->link('payment/oplata/response', '', 'SSL');
			$callback = $this->url->link('payment/oplata/callback', '', 'SSL');
			$desc = $this->language->get('order_desq') . $order_id;
			if (($this->config->get('oplata_currency') != 'shop')) {
				$oplata_currency = $this->config->get('oplata_currency');
				}else {
				$oplata_currency = $this->currency->getCode();
			}

			$oplata_args = array(
                'order_id' => $order_id . $this->ORDER_SEPARATOR . time(),
                'merchant_id' => $this->config->get('oplata_merchant'),
                'order_desc' => $desc,
                'amount' => round($order_info['total']*$order_info['currency_value']*100),
                'currency' => $oplata_currency,
                'response_url' => $backref,
                'server_callback_url' => $callback,
                'lang' => strtolower($this->config->get('oplata_language')),
                'sender_email' => $order_info['email']
			);

            $oplata_args['signature'] = $this->getSignature($oplata_args, $this->config->get('oplata_secretkey'));

            if ($this->config->get('oplata_process_payment_type') == 'built_in_checkout'){
                $this->data['fondy_options'] = $this->getFondyOptions($oplata_args);
            } else {
                $oplata_args['url'] = 'https://api.fondy.eu/api/checkout/redirect/';
                $this->data['oplata_args'] = $oplata_args;
            }

			$this->data['button_confirm'] = $this->language->get('button_confirm');
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oplata.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/oplata.tpl';
				} else {
				$this->template = 'default/template/payment/oplata.tpl';
			}
			
			$this->render();
		}
		
		public function response() {
			$this->language->load('payment/oplata');
			$this->load->model('checkout/order');
            list($order_id,) = explode($this->ORDER_SEPARATOR, $this->request->post['order_id']);
            $this->session->data['order_id'] = $order_id;

            $options = array(
				'merchant' => $this->config->get('oplata_merchant'),
				'secretkey' => $this->config->get('oplata_secretkey')
			);

            $paymentInfo = $this->isPaymentValid($options, $this->request->post);
            if ($paymentInfo === true && ($this->request->post['order_status'] != $this->ORDER_DECLINED or $this->request->post['order_status'] != $this->ORDER_EXPIRED)) {
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
			} else {
				$this->session->data ['oplata_error'] = $this->language->get('error_oplata').' '. $this->request->post['response_description'].'. '.$this->language->get('error_kod'). $this->request->post['response_code'] ;
				$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
		
		public function callback() {
			if(empty($this->request->post)){
				$fap = json_decode(file_get_contents("php://input"));
				$this->request->post = array();
				if (empty($fap)) {
					die();
				}
				foreach($fap as $key=>$val)
				{
					$this->request->post[$key] =  $val ;
				}
			}		
						
			$this->language->load('payment/oplata');
			
			$options = array(
                'merchant' => $this->config->get('oplata_merchant'),
                'secretkey' => $this->config->get('oplata_secretkey')
			);
			
			$paymentInfo = $this->isPaymentValid($options, $this->request->post);
			list($order_id,) = explode($this->ORDER_SEPARATOR, $this->request->post['order_id']);
			$this->load->model('checkout/order');
			if ($paymentInfo === true) {
				$value = serialize($this->request->post);
				if ($this->request->post['order_status'] == $this->ORDER_APPROVED) {
					$comment = "Fondy payment id : " . $this->request->post['payment_id'];
					$order_info = $this->model_checkout_order->getOrder($order_id);
					$this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_id'), $comment, $notify = true, $value);
					die('Ok');
				}else if($this->request->post['order_status'] == $this->ORDER_PROCESSING){
					$this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_process_status_id'), $comment = '', $notify = true, $value='');
					die($paymentInfo);
				}else if($this->request->post['order_status'] == $this->ORDER_DECLINED or $this->request->post['order_status'] == $this->ORDER_EXPIRED){
					$comment = "Payment cancelled";
					$this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_cancelled_status_id'), $comment, $notify = false, $override = false);
					die;
				}
			}
		}
		
		public function isPaymentValid($oplataSettings, $response){
			$this->language->load('payment/oplata');
			if ($oplataSettings['merchant'] != $response['merchant_id']) {
				return $this->language->get('error_merchant');
			}
			
			$responseSignature = $response['signature'];
			if (isset($response['response_signature_string'])){
				unset($response['response_signature_string']);
			}
			if (isset($response['signature'])){
				unset($response['signature']);
			}
			if (self::getSignature($response, $oplataSettings['secretkey']) != $responseSignature) {
				
				return $this->language->get('error_signature');
			}
			return true;
		}

        public function getFondyOptions($fondyParams){
            try {
                $checkoutToken = $this->getCheckoutToken($fondyParams);
            } catch (Exception $e){
                $checkoutToken = '';
                $this->data['error_message'] = $e->getMessage();
            }

            return json_encode(array(
                'options' => array(
                    'methods' => array('card'),
                    'methods_disabled' => array(),
                    'active_tab' => "card",
                    'full_screen' => false,
                    'locales' => array($fondyParams['lang']),
                    'email' => true,
                ),
                'params' => array('token' => $checkoutToken),
            ));
        }

        public function getCheckoutToken($fields){
            $curl = curl_init('https://api.fondy.eu/api/checkout/token');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('request' => $fields)));
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response);

            if (empty($result->response) && empty($result->response->response_status))
                throw new \Exception('Unknown Fondy API answer.');

            if ($result->response->response_status != 'success')
                throw new \Exception($result->response->error_message);

            return $result->response->token;
        }

		public function getSignature($data, $password, $encoded = true){
			$data = array_filter($data, function($var) {
				return $var !== '' && $var !== null;
			});
			ksort($data);
			
			$str = $password;
			foreach ($data as $k => $v) {
				$str .= $this->SIGNATURE_SEPARATOR . $v;
			}
			
			if ($encoded) {
				return sha1($str);
				} else {
				return $str;
			}
		}
		
	}
?>