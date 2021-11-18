<?php

class ControllerExtensionPaymentOplata extends Controller
{
    protected $ORDER_SEPARATOR = '_';
    protected $ORDER_APPROVED = 'approved';
    protected $ORDER_DECLINED = 'declined';
    protected $ORDER_EXPIRED = 'expired';
    protected $ORDER_PROCESSING = 'processing';
    protected $ORDER_CREATED = 'created';
    protected $ORDER_REVERSED = 'reversed';

    public function index()
    {
        $this->language->load('extension/payment/oplata');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/oplata');

        $orderID = $this->session->data['order_id'];
        $orderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $lang = substr($this->session->data['language'], 0, 2);

        try {
            $fondyParams = [
                'order_id' => $orderID . $this->ORDER_SEPARATOR . time(),
                'merchant_id' => (int)$this->config->get('oplata_merchant'),
                'order_desc' => $this->language->get('order_desq') . $orderID,
                'amount' => (int)round($orderInfo['total'] * $orderInfo['currency_value'] * 100),
                'currency' => $this->session->data['currency'],
                'response_url' => $this->url->link('extension/payment/oplata/response', '', true),
                'server_callback_url' => $this->url->link('extension/payment/oplata/callback', '', true),
                'lang' => $lang,
                'sender_email' => $orderInfo['email'],
                'preauth' => $this->config->get('oplata_type') == 'preauth' ? 'Y' : 'N',
                'reservation_data' => $this->model_extension_payment_oplata->getReservationData($orderInfo),
            ];

            $this->model_extension_payment_oplata->addFondyOrder($fondyParams);

            if ($this->config->get('oplata_process_payment_type') == 'built_in_checkout') {
                $checkoutToken = $this->model_extension_payment_oplata->getCheckoutToken($fondyParams);
                $data['fondy_options'] = json_encode([
                    'options' => [
                        'methods' => ['card'],
                        'methods_disabled' => [],
                        'active_tab' => "card",
                        'full_screen' => false,
                        'locales' => [$lang],
                        'email' => true,
                        'theme' => [
                            'type' => $this->config->get('oplata_style_type') ?: 'light',
                            'preset' => $this->config->get('oplata_style_preset') ?: 'black',
                        ]
                    ],
                    'params' => ['token' => $checkoutToken],
                ]);
            } else {
                $data['button_confirm'] = $this->language->get('button_confirm');
                $data['checkout_url'] = $this->model_extension_payment_oplata->getCheckoutUrl($fondyParams);
            }
        } catch (Exception $e) {
            $data['error_message'] = $e->getMessage();
            $this->log->write('FondyError: ' . $e->getMessage());
        }

        return $this->load->view('/extension/payment/oplata', $data);
    }

    public function response()
    {
        $this->language->load('extension/payment/oplata');

        if (($paymentValidation = $this->validate($this->request->post)) !== true) {
            $this->session->data['error'] = $paymentValidation;
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        if ($this->request->post['order_status'] === $this->ORDER_DECLINED)
            $this->response->redirect($this->url->link('checkout/failure', '', true));

        list($order_id,) = explode($this->ORDER_SEPARATOR, $this->request->post['order_id']);
        $this->session->data['order_id'] = $order_id;

        $this->response->redirect($this->url->link('checkout/success', '', true));
    }

    public function callback()
    {
        if (empty($this->request->post)) {
            $callback = json_decode(file_get_contents("php://input"));
            if (empty($callback)) {
                die();
            }
            $this->request->post = [];
            foreach ($callback as $key => $val) {
                $this->request->post[$key] = $val;
            }
        }

        if (($paymentValidation = $this->validate($this->request->post)) !== true) {
            http_response_code(400);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['error' => $paymentValidation]));
            $this->response->output();
            exit;
        }

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/oplata');

        $fondyOrderID = $this->request->post['order_id'];
        $fondyOrderInfo = $this->model_extension_payment_oplata->getFondyOrder($fondyOrderID);
        $orderID = strstr($fondyOrderID, $this->ORDER_SEPARATOR, true);
        $orderInfo = $this->model_checkout_order->getOrder($orderID);

        if (!empty($this->request->post['reversal_amount'])) { // reverse callback
            $comment = 'Fondy refund successful! Refund amount: ' .
                $this->request->post['reversal_amount'] / 100 . ' ' . $fondyOrderInfo['currency_code'];
            $this->model_checkout_order->addOrderHistory($orderID, $this->config->get('oplata_order_reverse_status_id'), $comment);
            exit;
        }

        if ($fondyOrderInfo['last_tran_type'] === 'capture') // just exit if order already captured
            exit;

        $comment = "Fondy payment id: {$this->request->post['payment_id']}.";
        if (!empty($this->request->post['response_description']) && !empty($this->request->post['response_code'])) {
            $comment .= $this->language->get('error_payment') . $this->request->post['response_description'] . '. ' . $this->language->get('error_code') . $this->request->post['response_code'];
        }

        switch ($this->request->post['order_status']) {
            case $this->ORDER_APPROVED: //we recive with this status in 3 type transaction callback - purchase, capture, reverse
                $this->model_checkout_order->addOrderHistory($orderID, $this->config->get('oplata_order_success_status_id'), $comment, $notify = true);
                break;
            case $this->ORDER_CREATED:
            case $this->ORDER_PROCESSING:
                $this->model_checkout_order->addOrderHistory($orderID, $this->config->get('oplata_order_process_status_id'), $comment);
                break;
            case $this->ORDER_DECLINED:
                $this->model_checkout_order->addOrderHistory($orderID, $this->config->get('oplata_order_cancelled_status_id'), $comment);
                break;
            case $this->ORDER_EXPIRED:
                if ($orderInfo['order_status_id'] === $this->config->get('oplata_order_success_status_id'))
                    exit('order status already successful!');
                $this->model_checkout_order->addOrderHistory($orderID, $this->config->get('oplata_order_cancelled_status_id'), $comment);
                break;
            default:
                exit('undefined fondy order status');
        }

        $this->model_extension_payment_oplata->updateFondyOrder($fondyOrderID, $this->request->post);
    }

    public function validate($request)
    {
        $this->language->load('extension/payment/oplata');
        $this->load->model('extension/payment/oplata');

        $merchantID = $this->config->get('oplata_merchant');
        $secretKey = $this->config->get('oplata_secretkey');

        if ($this->config->get('oplata_environment') == 'test') {
            $merchantID = $this->model_extension_payment_oplata->getTestMerchantID();
            $secretKey = $this->model_extension_payment_oplata->getTestMerchantSecretKey();
        }

        if ($merchantID != $request['merchant_id'])
            return $this->language->get('error_payment') . $this->language->get('error_merchant');

        $responseSignature = $request['signature'];
        unset($request['response_signature_string']);
        unset($request['signature']);
        $orderSignature = $this->model_extension_payment_oplata->getSignature($request, $secretKey);

        if ($orderSignature != $responseSignature)
            return $this->language->get('error_payment') . $this->language->get('error_signature');

        return true;
    }
}
