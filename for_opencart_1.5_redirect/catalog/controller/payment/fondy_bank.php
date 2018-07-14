<?php

class ControllerPaymentFondyBank extends Controller
{

    protected $RESPONCE_SUCCESS = 'success';
    protected $RESPONCE_FAIL = 'failure';
    protected $ORDER_SEPARATOR = '_';
    protected $SIGNATURE_SEPARATOR = '|';
    protected $ORDER_APPROVED = 'approved';
    protected $ORDER_DECLINED = 'declined';
    protected $ORDER_PROCESSING = 'processing';
    protected $ORDER_EXPIRED = 'expired';

    protected function index()
    {

        $this->language->load('payment/fondy_bank');
        $order_id = $this->session->data['order_id'];
        $this->load->model('checkout/order');
        $products = $this->cart->getProducts();
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $backref = $this->url->link('payment/fondy_bank/response', '', 'SSL');
        $callback = $this->url->link('payment/fondy_bank/callback', '', 'SSL');
        $desc = $this->language->get('order_desq') . $order_id;
        if (($this->config->get('fondy_bank_currency') != 'shop')) {
            $fondy_bank_currency = $this->config->get('fondy_bank_currency');
        } else {
            $fondy_bank_currency = $this->currency->getCode();
        }

        $fondy_bank_args = array(
            'order_id' => $order_id . $this->ORDER_SEPARATOR . time(),
            'merchant_id' => $this->config->get('fondy_bank_merchant'),
            'order_desc' => $desc,
            'amount' => round($order_info['total'] * $order_info['currency_value'] * 100),
            'currency' => $fondy_bank_currency,
            'response_url' => $backref,
            'server_callback_url' => $callback,
            'default_payment_system' => 'trustly',
            'lang' => $this->config->get('fondy_bank_language'),
            'sender_email' => $order_info['email']
        );

        $fondy_bank_args['signature'] = $this->getSignature($fondy_bank_args, $this->config->get('fondy_bank_secretkey'));
        $fondy_bank_args['url'] = 'https://api.fondy.eu/api/checkout/redirect/';
        $this->data['fondy_bank_args'] = $fondy_bank_args;
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/fondy_bank.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/fondy_bank.tpl';
        } else {
            $this->template = 'default/template/payment/fondy_bank.tpl';
        }
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];

        $this->render();
    }

    public function response()
    {

        $this->language->load('payment/fondy_bank');
        $this->load->model('checkout/order');

        $options = array(
            'merchant' => $this->config->get('fondy_bank_merchant'),
            'secretkey' => $this->config->get('fondy_bank_secretkey')
        );
        $order_id = $this->session->data['order_id'];
        $this->model_checkout_order->confirm($order_id, $this->config->get('fondy_bank_order_process_status_id'), $comment = '', $notify = false, $value = '');
        unset($this->session->data['cart']);
        $paymentInfo = $this->isPaymentValid($options, $this->request->post);
        if ($paymentInfo === true && ($this->request->post['order_status'] != $this->ORDER_DECLINED or $this->request->post['order_status'] != $this->ORDER_EXPIRED)) {
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->session->data ['fondy_bank_error'] = $this->language->get('error_fondy_bank') . ' ' . $this->request->post['response_description'] . '. ' . $this->language->get('error_kod') . $this->request->post['response_code'];
            $this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }
    }

    public function callback()
    {

        if (empty($this->request->post)) {
            $fap = json_decode(file_get_contents("php://input"));
            $this->request->post = array();
            if (empty($fap)) {
                die();
            }
            foreach ($fap as $key => $val) {
                $this->request->post[$key] = $val;
            }
        }

        $this->language->load('payment/fondy_bank');

        $options = array(
            'merchant' => $this->config->get('fondy_bank_merchant'),
            'secretkey' => $this->config->get('fondy_bank_secretkey')
        );

        $paymentInfo = $this->isPaymentValid($options, $this->request->post);
        list($order_id,) = explode($this->ORDER_SEPARATOR, $this->request->post['order_id']);
        $this->load->model('checkout/order');
        if ($paymentInfo === true) {

            $value = serialize($this->request->post);
            if ($this->request->post['order_status'] == $this->ORDER_APPROVED) {
                $comment = "FondyBank payment id : " . $this->request->post['payment_id'];
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $this->model_checkout_order->update($order_id, $this->config->get('fondy_bank_order_status_id'), $comment, $notify = true, $value);
                die('Ok');
            } else if ($this->request->post['order_status'] == $this->ORDER_PROCESSING) {
                $this->model_checkout_order->update($order_id, $this->config->get('fondy_bank_order_process_status_id'), $comment = '', $notify = true, $value = '');
                die($paymentInfo);
            } else if ($this->request->post['order_status'] == $this->ORDER_DECLINED or $this->request->post['order_status'] == $this->ORDER_EXPIRED) {
                $comment = "Payment cancelled";
                $this->model_checkout_order->update($order_id, $this->config->get('fondy_bank_order_cancelled_status_id'), $comment, $notify = false, $override = false);
                die;
            }
        }
    }

    public function isPaymentValid($fondy_bankSettings, $response)
    {
        $this->language->load('payment/fondy_bank');
        if ($fondy_bankSettings['merchant'] != $response['merchant_id']) {
            return $this->language->get('error_merchant');
        }

        $responseSignature = $response['signature'];
        if (isset($response['response_signature_string'])) {
            unset($response['response_signature_string']);
        }
        if (isset($response['signature'])) {
            unset($response['signature']);
        }
        if (self::getSignature($response, $fondy_bankSettings['secretkey']) != $responseSignature) {

            return $this->language->get('error_signature');
        }
        return true;
    }

    public function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function ($var) {
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