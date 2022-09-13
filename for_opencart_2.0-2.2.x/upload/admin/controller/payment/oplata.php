<?php

class ControllerPaymentOplata extends Controller
{
    private $error = [];
    private $extensionVersion = '2.0.2';

    public function install()
    {
        $this->load->model('payment/oplata');
        $this->model_payment_oplata->install();
    }

    public function uninstall()
    {
        $this->load->model('payment/oplata');
        $this->model_payment_oplata->uninstall();
    }

    public function index()
    {
        $this->load->language('payment/oplata');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('oplata', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $errorMessageValues = ["warning", "merchant", "secretkey", "type"];
        foreach ($errorMessageValues as $v)
            $data['error_' . $v] = (isset($this->error[$v])) ? $this->error[$v] : "";

        foreach ($this->getSettingsTranslateKeys() as $v)
            $data[$v] = $this->language->get($v);

        $data['breadcrumbs'] = $this->getBreadcrumbs();
        $data['extension_version'] = $this->extensionVersion;
        $data['process_payment_types'] = $this->getProcessPaymentTypes();
        $data['style_presets'] = $this->getStylePresets();
        $data['action'] = $this->url->link('payment/oplata', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true);
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        foreach ($this->getFormInputs() as $v)
            $data[$v] = (isset($this->request->post[$v])) ? $this->request->post[$v] : $this->config->get($v);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->getTemplateRoute('payment/oplata'), $data));
    }

    public function action()
    {
        return $this->order();
    }

    public function order()
    {
        $orderID = $this->request->get['order_id'];
        $userToken = $this->request->get['token'];

        if ($this->config->get('oplata_status')) {
            $this->load->model('payment/oplata');

            if ($fondyOrder = $this->model_payment_oplata->getLastFondyOrder($orderID)) {
                $this->load->language('payment/oplata');

                foreach ($this->getOrderTranslateKeys() as $v)
                    $data[$v] = $this->language->get($v);

                $data['order'] = $fondyOrder;
                $data['order']['formatted_total'] = $fondyOrder['total'] / 100;
                $data['capture_url'] = html_entity_decode($this->url->link(
                    'payment/oplata/capture',
                    'order_id=' . $orderID . '&token=' . $userToken
                ));
                $data['reverse_url'] = html_entity_decode($this->url->link(
                    'payment/oplata/reverse',
                    'order_id=' . $orderID . '&token=' . $userToken
                ));
                $data['upd_payment_detail_table_url'] = html_entity_decode($this->url->link(
                    'payment/oplata/order',
                    'order_id=' . $orderID . '&token=' . $userToken . '&upd=1'
                ));

                $view = $this->load->view($this->getTemplateRoute('payment/oplata_order'), $data);

                if (isset($this->request->get['upd'])) {
                    echo $view;
                    exit;
                }

                return $view;
            }
        }

        return false;
    }

    public function capture()
    {
        $this->load->model('payment/oplata');
        $this->load->language('payment/oplata');
        $orderID = $this->request->get['order_id'];
        $fondyOrder = $this->model_payment_oplata->getLastFondyOrder($orderID);
        $jsonResponse = [];

        if (!empty($fondyOrder) && $fondyOrder['preauth'] == 'Y' && $fondyOrder['last_tran_type'] != 'capture') {
            $this->load->model('sale/order');
            $order = $this->model_sale_order->getOrder($orderID);
            $captureAmount = round($this->request->post['amount'] * $order['currency_value'] * 100);

            try {
                $this->model_payment_oplata->capture([
                    'order_id' => $fondyOrder['id'],
                    'merchant_id' => $this->config->get('oplata_merchant'),
                    'amount' => (int)$captureAmount,
                    'currency' => $fondyOrder['currency_code']
                ]);

                if ($captureAmount < $fondyOrder['total'])
                    $fondyOrder['total'] = $captureAmount;

                $fondyOrder['last_tran_type'] = 'capture';
                $this->model_payment_oplata->updateFondyOrder($fondyOrder);
                $jsonResponse['success_message'] = $this->language->get('text_success_action');;
            } catch (Exception $e) {
                http_response_code(400);
                $jsonResponse['error_message'] = $e->getMessage();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($jsonResponse));
    }

    public function reverse()
    {
        $this->load->model('payment/oplata');
        $this->load->language('payment/oplata');
        $orderID = $this->request->get['order_id'];
        $fondyOrder = $this->model_payment_oplata->getLastFondyOrder($orderID);
        $jsonResponse = [];

        if (!empty($fondyOrder)) {
            $this->load->model('sale/order');
            $order = $this->model_sale_order->getOrder($orderID);
            $refundAmount = round($this->request->post['amount'] * $order['currency_value'] * 100);

            if ($fondyOrder['preauth'] === 'Y' && $fondyOrder['last_tran_type'] == 'purchase')
                $refundAmount = $fondyOrder['total'];

            try {
                $this->model_payment_oplata->reverse([
                    'order_id' => $fondyOrder['id'],
                    'merchant_id' => $this->config->get('oplata_merchant'),
                    'amount' => (int)$refundAmount,
                    'currency' => $fondyOrder['currency_code'],
                ]);

                $fondyOrder['total'] -= $refundAmount;
                $fondyOrder['last_tran_type'] = 'reverse';
                $this->model_payment_oplata->updateFondyOrder($fondyOrder);
                $jsonResponse['success_message'] = $this->language->get('text_success_action');;
            } catch (Exception $e) {
                http_response_code(400);
                $jsonResponse['error_message'] = $e->getMessage();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($jsonResponse));
    }

    /**
     * @return bool
     */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/oplata')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['oplata_merchant']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }
        if (!$this->request->post['oplata_secretkey']) {
            $this->error['secretkey'] = $this->language->get('error_secretkey');
        }
        return !$this->error;
    }

    private function getFormInputs()
    {
        return [
            'oplata_status',
            'oplata_environment',
            'oplata_merchant',
            'oplata_secretkey',
            'oplata_process_payment_type',
            'oplata_type',
            'oplata_geo_zone_id',
            'oplata_sort_order',
            'oplata_order_success_status_id',
            'oplata_order_cancelled_status_id',
            'oplata_order_process_status_id',
            'oplata_order_reverse_status_id',
            'oplata_style_type',
            'oplata_style_preset',
        ];
    }

    private function getSettingsTranslateKeys()
    {
        return [
            'button_save',
            'button_cancel',
            'text_edit',
            'tab_general',
            'tab_order_status',
            'tab_style',
            'tooltip_entry_status',
            'entry_status',
            'text_enabled',
            'text_disabled',
            'tooltip_entry_environment',
            'entry_env',
            'help_entry_environment',
            'tooltip_entry_merchant',
            'entry_merchant',
            'tooltip_entry_secretkey',
            'entry_secretkey',
            'tooltip_entry_process_payment_type',
            'entry_process_payment_type',
            'tooltip_entry_payment_type',
            'entry_payment_type',
            'entry_common_type',
            'entry_preauth_type',
            'entry_geo_zone',
            'text_all_zones',
            'entry_sort_order',
            'entry_order_success_status',
            'entry_order_cancelled_status',
            'entry_order_process_status',
            'entry_order_reverse_status',
            'entry_style_theme',
            'entry_light',
            'entry_dark',
            'entry_style_preset',
        ];
    }

    private function getOrderTranslateKeys()
    {
        return [
            'text_payment_details',
            'text_fondy_order_id',
            'text_payment_id',
            'text_order_total',
            'text_masked_card',
            'text_order_status',
            'text_order_last_tran_type',
            'text_charge_the_amount',
            'text_refund',
            'text_total',
            'tooltip_field_payment_id',
            'tooltip_btn_preauth_reverse',
            'btn_capture',
            'btn_preauth_reverse',
            'btn_reverse'
        ];
    }

    private function getProcessPaymentTypes()
    {
        return [
            'redirect' => $this->language->get('entry_redirect'),
            'built_in_checkout' => $this->language->get('entry_built_in_checkout'),
        ];
    }

    private function getStylePresets()
    {
        return [
            'black' => 'black',
            'vibrant_gold' => 'vibrant gold',
            'vibrant_silver' => 'vibrant silver',
            'euphoric_pink' => 'euphoric pink',
            'solid_black' => 'solid black',
            'silver' => 'silver',
            'black_and_white' => 'black and white',
            'heated_steel' => 'heated steel',
            'nude_pink' => 'nude pink',
            'tropical_gold' => 'tropical gold',
            'navy_shimmer' => 'navy shimmer',
        ];
    }

    /**
     * @return array[]
     */
    private function getBreadcrumbs()
    {
        return [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
                'separator' => false
            ],
            [
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true),
                'separator' => ' :: '
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('payment/oplata', 'token=' . $this->session->data['token'], true),
                'separator' => ' :: '
            ],
        ];
    }

    private function getTemplateRoute($route)
    {
        return version_compare(VERSION, '2.2.0.0', '<') ? $route . '.tpl' : $route;
    }
}

