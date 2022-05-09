<?php

class ModelExtensionPaymentOplata extends Model
{
    public $testMerchantID = 1396424;
    public $testMerchantSecretKey = 'test';

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/oplata');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('oplata_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('oplata_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }
        $method_data = [];

        if ($status) {
            $method_data = [
                'code' => 'oplata',
                'terms' => '',
                'title' => $this->language->get('text_title'),
                'sort_order' => $this->config->get('oplata_sort_order')
            ];
        }
        return $method_data;
    }

    public function getTestMerchantID()
    {
        return $this->testMerchantID;
    }

    public function getTestMerchantSecretKey()
    {
        return $this->testMerchantSecretKey;
    }

    public function addFondyOrder($args)
    {
        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "fondy_order` 
            SET `id` = '" . $this->db->escape($args['order_id']) . "', 
            `currency_code` = '" . $this->db->escape($args['currency']) . "',
            `total` = '" . (int)$args['amount'] . "',
            `preauth` = '" . $this->db->escape($args['preauth']) . "'
        ");
    }

    public function updateFondyOrder($fondyOrderID, $data)
    {
        $this->db->query("
            UPDATE `" . DB_PREFIX . "fondy_order` 
            SET `status` = '" . $this->db->escape($data['order_status']) . "', 
            `last_tran_type` = '" . $this->db->escape($data['tran_type']) . "',
            `payment_id` = '" . (int)$data['payment_id'] . "',
            `masked_card` = '" . $this->db->escape($data['masked_card']) . "'
            WHERE `id` = '" . $this->db->escape($fondyOrderID) . "'
        ");
    }

    public function getFondyOrder($fondyOrderID)
    {
        $q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "fondy_order` WHERE id = '". $fondyOrderID . "'");

        return $q->num_rows ? $q->row : false;
    }

    public function getCheckoutUrl($requestData)
    {
        $request = $this->sendToAPI('checkout/url', $requestData);

        return $request->checkout_url;
    }

    public function getCheckoutToken($requestData)
    {
        $request = $this->sendToAPI('checkout/token', $requestData);

        return $request->token;
    }

    public function getReservationData($orderInfo)
    {
        $this->load->model('account/order');
        $orderProducts = $this->model_account_order->getOrderProducts($orderInfo['order_id']);

        $reservationData = [
            'customer_zip' => $orderInfo['payment_postcode'],
            'customer_name' => $orderInfo['payment_firstname'] . ' ' . $orderInfo['payment_lastname'],
            'customer_address' => $orderInfo['payment_address_1'] . ' ' . $orderInfo['payment_city'],
            'customer_state' => $orderInfo['payment_zone'],
            'customer_country' => $orderInfo['payment_country'],
            'phonemobile' => $orderInfo['telephone'],
            'account' => $orderInfo['email'],
            'cms_name' => 'OpenCart/ocStore',
            'cms_version' => VERSION,
            'shop_domain' => HTTP_SERVER,
            'path' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
        ];

        foreach ($orderProducts as $orderProduct){
            $reservationData['products'][] = [
                'id' => $orderProduct['product_id'],
                'name' => $orderProduct['name'],
                'price' => $this->formatPrice($orderProduct['price']),
                'total_amount' => $this->formatPrice($orderProduct['total']),
                'quantity' => $orderProduct['quantity'],
            ];
        }

        return base64_encode(json_encode($reservationData));
    }

    public function formatPrice($sum)
    {
        return number_format($sum, 2,'.','');
    }

    public function sendToAPI($endpoint, $requestData)
    {
        $secretKey = $this->config->get('oplata_secretkey');

        if ($this->config->get('oplata_environment') == 'test'){
            $requestData['merchant_id'] = $this->testMerchantID;
            $secretKey = $this->testMerchantSecretKey;
        }

        $requestData['signature'] = $this->getSignature($requestData, $secretKey);
        $request = $this->sendCurl('https://api.fondy.eu/api/' . $endpoint, $requestData);

        if (empty($request->response) && empty($request->response->response_status))
            throw new \Exception('Unknown Fondy API answer.');

        if ($request->response->response_status != 'success')
            throw new \Exception($request->response->error_message);

        return $request->response;
    }

    public function sendCurl($url, $fields)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['request' => $fields]));
        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function ($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= '|' . $v;
        }

        return $encoded ? sha1($str) : $str;
    }
}

