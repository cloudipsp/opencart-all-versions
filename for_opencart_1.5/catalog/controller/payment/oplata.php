<?php
class ControllerPaymentOplata extends Controller {


    protected $RESPONCE_SUCCESS='success';
    protected $RESPONCE_FAIL = 'failure';
    protected $ORDER_SEPARATOR = '_';
    protected $SIGNATURE_SEPARATOR = '|';
    protected $ORDER_APPROVED = 'approved';
    protected $ORDER_DECLINED = 'declined';


	protected function index() {

        $this->language->load('payment/oplata');
		$order_id = $this->session->data['order_id'];
		$this->load->model('checkout/order');
        $products=$this->cart->getProducts();
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$backref = $this->url->link('payment/oplata/response');
        $callback = $this->url->link('payment/oplata/callback');
        $desc='';
        foreach ($products as $product){
            if (!next($products)) {
                $desc.=$product['name'];
            }
            else {
                $desc.=$product['name'].', ';
            }

        }
        if (($this->config->get('oplata_currency'))) {
            $oplata_currency=$this->config->get('oplata_currency');
        }else {
            $oplata_currency=$this->currency->getCode();
        }

        $oplata_args = array('order_id' => $order_id . $this->ORDER_SEPARATOR . time(),
            'merchant_id' => $this->config->get('oplata_merchant'),
            'order_desc' => $desc,
            'amount' => $order_info['total'],
            'currency' => $oplata_currency,
            'response_url' => $backref,
            'server_callback_url' => $callback,
            'lang' => $this->config->get('oplata_language'),
            'sender_email' => $order_info['email']
        );

        $oplata_args['signature'] = $this->getSignature($oplata_args, $this->config->get('oplata_secretkey'));
        $this->data['oplata_args'] = $oplata_args;
        //$this->data['action'] = $this->url->link('payment/oplata');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oplata.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/oplata.tpl';
		} else {
			$this->template = 'default/template/payment/oplata.tpl';
		}
        
		$this->render();
	}

    public function confirm() {

        $this->load->model('checkout/order');

        $order_id = $this->session->data['order_id'];

        $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_process_status_id'), $comment = '', $notify = false, $value='');
       
        $this->redirect($this->url->link('checkout/success'));
    }

    public function response() {
        $this->language->load('payment/oplata');

        $options = array(
            'merchant' => $this->config->get('oplata_merchant'),
            'secretkey' => $this->config->get('oplata_secretkey')
        );

        $paymentInfo = $this->isPaymentValid($options, $_POST);
        
        if ($paymentInfo === true && $_POST['order_status'] != $this->ORDER_DECLINED) {
            list($order_id,) = explode($this->ORDER_SEPARATOR, $_POST['order_id']);

            $this->load->model('checkout/order');
            $value=serialize($_POST);
            if ($_POST['order_status'] == $this->ORDER_APPROVED) {
                $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_id'), $comment = '', $notify = false, $value);
                $this->redirect($this->url->link('checkout/success'));
            }else{
            $this->session->data ['oplata_error'] = $this->language->get('error_oplata').' '. $_POST['response_description'].'. '.$this->language->get('error_kod'). $_POST['response_code'] ;
            $this->redirect($this->url->link('checkout/checkout'));
            }
          
        } else {
            if ($_POST['order_status'] == $this->ORDER_DECLINED) {
                $this->session->data ['oplata_error'] = $this->language->get('error_oplata').' '. $_POST['response_description'].'. '.$this->language->get('error_kod'). $_POST['response_code'] ;
            $this->redirect($this->url->link('checkout/checkout'));
            }
            $this->session->data ['oplata_error'] = $this->language->get('error_oplata').' '. $_POST['response_description'].'. '.$this->language->get('error_kod'). $_POST['response_code'] ;
            $this->redirect($this->url->link('checkout/checkout'));
        }
    }

    public function callback() {
        
        $fap = json_decode(file_get_contents("php://input"));
        $_POST=array();
        foreach($fap as $key=>$val)
        {
          $_POST[$key] =  $val ;
        }
        //print_r ($_POST);
       
        
        $this->language->load('payment/oplata');

        $options = array(
            'merchant' => $this->config->get('oplata_merchant'),
            'secretkey' => $this->config->get('oplata_secretkey')
        );

        $paymentInfo = $this->isPaymentValid($options, $_POST);
        
        if ($paymentInfo === true && $_POST['order_status'] != $this->ORDER_DECLINED) {
            list($order_id,) = explode($this->ORDER_SEPARATOR, $_POST['order_id']);
            
            $this->load->model('checkout/order');
            $value=serialize($_POST);
            if ($_POST['order_status'] == $this->ORDER_APPROVED) {
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_id'), $comment = '', $notify = false, $value);
                echo 'done';
            }else{
            echo $paymentInfo;
           
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
            //echo 1;
            return $this->language->get('error_signature');
        }
        return true;
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