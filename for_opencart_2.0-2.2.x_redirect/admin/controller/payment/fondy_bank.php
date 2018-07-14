<?php 
class ControllerPaymentFondyBank extends Controller
{
	private $error = array(); 

	public function index() {

		$this->load->language('payment/fondy_bank');
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
			$this->model_setting_setting->editSetting('fondy_bank', $this->request->post);				
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$arr = array( 
				"heading_title", "text_payment", "text_success", "text_pay", "text_card", 
				"entry_merchant", "entry_styles" , "entry_secretkey", "entry_order_status", "entry_fondy_bank_result" ,
				"entry_currency", "entry_backref", "entry_server_back", "entry_language", "entry_status", "entry_order_status_cancelled",
				"entry_sort_order", "error_permission", "error_merchant", "error_secretkey", 'text_edit',"entry_help_lang");

		foreach ($arr as $v)
            $data[$v] = $this->language->get($v);
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_order_process_status'] = $this->language->get('entry_order_process_status');

		#$data['LUURL'] = "index.php?route=payment/fondy_bank/callback";


//------------------------------------------------------------
        $arr = array("warning", "merchant", "secretkey", "type");
        foreach ( $arr as $v )
            $data['error_'.$v] = ( isset($this->error[$v]) ) ? $this->error[$v] : "";
//------------------------------------------------------------

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/fondy_bank', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('payment/fondy_bank', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

//------------------------------------------------------------
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['fondy_bank_currencyc']= array('','EUR','USD','GBP','RUB','UAH');
		$arr = array( "fondy_bank_merchant", "fondy_bank_secretkey", "fondy_bank_result", "fondy_bank_backref", "fondy_bank_server_back",
            "fondy_bank_language", "fondy_bank_status", "fondy_bank_sort_order", "fondy_bank_order_status_id", "fondy_bank_order_process_status_id", "fondy_bank_order_cancelled_status_id", "fondy_bank_currency", "fondy_bank_styles");

		
		

		foreach ( $arr as $v )
		{
			$data[$v] = ( isset($this->request->post[$v]) ) ? $this->request->post[$v] : $this->config->get($v);
		}


//------------------------------------------------------------

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('payment/fondy_bank.tpl', $data));
	}

//------------------------------------------------------------
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/fondy_bank')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['fondy_bank_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['fondy_bank_secretkey']) {
			$this->error['secretkey'] = $this->language->get('error_secretkey');
		}

		return (!$this->error) ? true : false ;
	}
}
?>