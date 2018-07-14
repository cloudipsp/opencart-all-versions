<?php 
class ModelExtensionPaymentFondyBank extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/fondy_bank');

		$method_data = array(
				'code'       => 'fondy_bank',
				'terms'      => '',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('fondy_bank_sort_order')
			);
		return $method_data;
	}
}
?>