<?php

class ModelExtensionPaymentOplata extends Model
{
    public $testMerchantID = 1396424;
    public $testMerchantSecretKey = 'test';

    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fondy_order` (
				`id` varchar(30) NOT NULL,
				`status` varchar(15) DEFAULT NULL,
				`payment_id` int(10) DEFAULT NULL,
				`last_tran_type` varchar(255) DEFAULT NULL,
				`currency_code` CHAR(3) NOT NULL,
				`total` int(11) NOT NULL,
				`masked_card` varchar(255) DEFAULT NULL,
				`preauth` char(1) DEFAULT NULL,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP NULL on update CURRENT_TIMESTAMP,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP NULL,
				PRIMARY KEY `id` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "fondy_order`;");
    }

    public function getLastFondyOrder($orderID)
    {
        $q = $this->db->query("
            SELECT 
            * 
            FROM `" . DB_PREFIX . "fondy_order` 
            WHERE id LIKE '". $orderID . "_%' 
                and `payment_id` IS NOT null 
            ORDER BY id DESC 
            LIMIT 1
        ");

        return $q->num_rows ? $q->row : false;
    }

    public function updateFondyOrder($fondyOrder)
    {
        $this->db->query("
            UPDATE `" . DB_PREFIX . "fondy_order` 
            SET `status` = '" . $this->db->escape($fondyOrder['status']) . "', 
            `last_tran_type` = '" . $this->db->escape($fondyOrder['last_tran_type']) . "',
            `total` = '" . (int)$fondyOrder['total'] . "'
            WHERE `id` = '" . $this->db->escape($fondyOrder['id']) . "'
        ");
    }

    public function capture($requestData)
    {
        $request = $this->sendToAPI('capture/order_id', $requestData);

        if ($request->capture_status != 'captured')
            throw new \Exception('Fondy capture status: ' . $request->capture_status);

        return true;
    }

    public function reverse($requestData)
    {
        $request = $this->sendToAPI('reverse/order_id', $requestData);

        if ($request->reverse_status != 'approved')
            throw new \Exception('Fondy refund status: ' . $request->reverse_status);

        return true;
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
            throw new \Exception("Fondy response status: " . $request->response->response_status); // todo mb error message

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