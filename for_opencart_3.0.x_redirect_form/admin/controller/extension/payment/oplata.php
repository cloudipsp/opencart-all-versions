<?php

class ControllerExtensionPaymentOplata extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/oplata');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            if (isset($this->error['bank' . $language['language_id']])) {
                $data['error_bank' . $language['language_id']] = $this->error['bank' . $language['language_id']];
            } else {
                $data['error_bank' . $language['language_id']] = '';
            }
        }
//------------------------------------------------------------
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_oplata', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $arr = array(
            "heading_title", "text_payment", "text_success", "text_pay", "text_card", 'entry_geo_zone', 'text_all_zones',
            "entry_merchant", "entry_styles", "entry_secretkey", "entry_order_status",
            "entry_currency", "entry_backref", "entry_server_back", "entry_language", "entry_status", "entry_order_status_cancelled",
            "entry_sort_order", "error_permission", "error_merchant", "error_secretkey", 'text_edit', "entry_help_lang");
        foreach ($arr as $v)
            $data[$v] = $this->language->get($v);
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_order_process_status'] = $this->language->get('entry_order_process_status');
//------------------------------------------------------------
        $arr = array("warning", "merchant", "secretkey", "type");
        foreach ($arr as $v)
            $data['error_' . $v] = (isset($this->error[$v])) ? $this->error[$v] : "";
//------------------------------------------------------------
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/oplata', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/payment/oplata', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
//------------------------------------------------------------
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['payment_oplata_currencyc'] = array(' ', 'EUR', 'USD', 'GBP', 'RUB', 'UAH');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $array_data = array(
            "payment_oplata_merchant",
            "payment_oplata_secretkey",
            "payment_oplata_backref",
            "payment_oplata_server_back",
            "payment_oplata_order_cancelled_status_id",
            'payment_oplata_geo_zone_id',
            "payment_oplata_language",
            "payment_oplata_status",
            "payment_oplata_sort_order",
            "payment_oplata_order_status_id",
            "payment_oplata_order_process_status_id",
            "payment_oplata_currency"
        );

        foreach ($array_data as $v) {
            $data[$v] = (isset($this->request->post[$v])) ? $this->request->post[$v] : $this->config->get($v);
        }
//------------------------------------------------------------
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/oplata', $data));
    }

//------------------------------------------------------------
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/oplata')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_oplata_merchant']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }
        if (!$this->request->post['payment_oplata_secretkey']) {
            $this->error['secretkey'] = $this->language->get('error_secretkey');
        }
        return (!$this->error) ? true : false;
    }
}

?>