<?php 
class ControllerPaymentOplata extends Controller
{
	private $error = array(); 

	public function index() {
		$this->load->language('payment/oplata');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
//------------------------------------------------------------

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('oplata', $this->request->post);				
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$arr = array( 
				"heading_title", "text_payment", "text_success", "text_pay", "text_card", 
				"entry_merchant", "entry_secretkey", "entry_order_status", "entry_order_status_cancelled",
				"entry_currency", "entry_backref", "entry_server_back", "entry_language", "entry_status",
				"entry_sort_order", "error_permission", "error_merchant", "error_secretkey");

		foreach ($arr as $v)
            $this->data[$v] = $this->language->get($v);
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['entry_order_process_status'] = $this->language->get('entry_order_process_status');

		#$this->data['LUURL'] = "index.php?route=payment/oplata/callback";


//------------------------------------------------------------
        $arr = array("warning", "merchant", "secretkey", "type");
        foreach ( $arr as $v )
            $this->data['error_'.$v] = ( isset($this->error[$v]) ) ? $this->error[$v] : "";
//------------------------------------------------------------

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/oplata', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/oplata', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

//------------------------------------------------------------
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['oplata_currencyc']= array('EUR','USD','GBP','RUB','UAH');
		$arr = array( "oplata_merchant", "oplata_secretkey", "oplata_currency", "oplata_backref", "oplata_server_back",
            "oplata_language", "oplata_status", "oplata_sort_order", "oplata_order_status_id", "oplata_order_process_status_id", "oplata_order_cancelled_status_id" );

		foreach ( $arr as $v )
		{
			$this->data[$v] = ( isset($this->request->post[$v]) ) ? $this->request->post[$v] : $this->config->get($v);
		}
//------------------------------------------------------------

		$this->template = 'payment/oplata.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

//------------------------------------------------------------
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/oplata')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['oplata_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['oplata_secretkey']) {
			$this->error['secretkey'] = $this->language->get('error_secretkey');
		}

		return (!$this->error) ? true : false ;
	}
}
?>