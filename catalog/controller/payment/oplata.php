<?php
class ControllerPaymentOplata extends Controller {
	protected function index() {

		$order_id = $this->session->data['order_id'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $oplata_args = array('order_id' => $order_id . Oplata::ORDER_SEPARATOR . time(),
            'merchant_id' => $this->config->get('oplata_merchant'),
            'order_desc' => 'Order from opencart',
            'amount' => Oplata::getAmount($order_info),
            'currency' => $this->config->get('oplata_currency'),
            'server_callback_url' => $this->config->get('oplata_server_back'),
            'response_url' => $this->config->get('oplata_backref'),
            'lang' => $this->config->get('oplata_language'),
            'sender_email' => $order_info['email']
        );

        $oplata_args['signature'] = Oplata::getSignature($oplata_args, $this->config->get('oplata_secretkey'));

        $this->data['oplata_args'] = $oplata_args;
        $this->data['action'] = Oplata::URL;
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

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if (!$order_info) return;

        $order_id = $this->session->data['order_id'];

        if ($order_info['order_status_id'] == 0) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_progress_id'), 'Oplata');
            return;
        }

        if ($order_info['order_status_id'] != $this->config->get('oplata_order_status_progress_id')) {
            $this->model_checkout_order->update($order_id, $this->config->get('oplata_order_status_progress_id'), 'Oplata' ,true);
        }
    }

    public function response() {

        $options = array(
            'merchant' => $this->config->get('oplata_merchant'),
            'secretkey' => $this->config->get('oplata_secretkey')
        );

        $paymentInfo = Oplata::isPaymentValid($options, $_POST);

        if ($paymentInfo === true && $_POST['order_status'] != Oplata::ORDER_DECLINED) {
            list($order_id,) = explode(Oplata::ORDER_SEPARATOR, $_POST['order_id']);

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_id'));

            $this->redirect($this->url->link('checkout/success'));
        } else {
            if ($_POST['order_status'] == Oplata::ORDER_DECLINED) {
                $paymentInfo = 'Transaction has been declined.';
            }

            $this->document->setTitle('Pay via Oplata');

            $this->data['heading_title'] = 'Payment failed';
            $this->data['text_payment_failed'] = $paymentInfo;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oplata_failure.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/oplata_failure.tpl';
            } else {
                $this->template = 'default/template/payment/oplata_failure.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render(true));
        }
    }

	public function callback() {

		$options = array(
            'merchant' => $this->config->get('oplata_merchant'),
			'secretkey' => $this->config->get('oplata_secretkey')
        );

        if ($_POST['order_status'] == Oplata::ORDER_DECLINED) {
            exit('Order declined');
        }

        $paymentInfo = Oplata::isPaymentValid($options, $_POST);
        if ($paymentInfo === true) {
            list($order_id,) = explode(Oplata::ORDER_SEPARATOR, $_POST['order_id']);

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $this->model_checkout_order->confirm($order_id, $this->config->get('oplata_order_status_id'));

            echo "OK";
//            $this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful.";
//            $this->msg['class'] = 'woocommerce-message';
        } else {
//            $this->msg['class'] = 'error';
//            $this->msg['message'] = $paymentInfo;
            echo $paymentInfo;
        }
	}
}


class Oplata
{
    const RESPONCE_SUCCESS = 'success';
    const RESPONCE_FAIL = 'failure';

    const ORDER_SEPARATOR = '#';

    const SIGNATURE_SEPARATOR = '|';

    const ORDER_APPROVED = 'approved';
    const ORDER_DECLINED = 'declined';

    const URL = "https://api.oplata.com/api/checkout/redirect/";

    protected static $responseFields = array(
        'rrn',
        'masked_card',
        'sender_cell_phone',
        'response_status',
        'currency',
        'fee',
        'reversal_amount',
        'settlement_amount',
        'actual_amount',
        'order_status',
        'response_description',
        'order_time',
        'actual_currency',
        'order_id',
        'tran_type',
        'eci',
        'settlement_date',
        'payment_system',
        'approval_code',
        'merchant_id',
        'settlement_currency',
        'payment_id',
        'sender_account',
        'card_bin',
        'response_code',
        'card_type',
        'amount',
        'sender_email');

    public static function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= self::SIGNATURE_SEPARATOR . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    public static function isPaymentValid($oplataSettings, $response)
    {
        if ($oplataSettings['merchant'] != $response['merchant_id']) {
            return 'An error has occurred during payment. Merchant data is incorrect.';
        }

        $originalResponse = $response;
        foreach ($response as $k => $v) {
            if (!in_array($k, self::$responseFields)) {
                unset($response[$k]);
            }
        }

        if (self::getSignature($response, $oplataSettings['secretkey']) != $originalResponse['signature']) {
            return 'An error has occurred during payment. Signature is not valid.';
        }

        return true;
    }

    public static function getAmount($order)
    {
        return round($order['total'] * 100);
    }
}

?>