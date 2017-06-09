<?php 
class ModelPaymentOplata extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/oplata');

		$method_data = array(
				'code'       => 'oplata',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('oplata_sort_order')
			);
		return $method_data;
	}
}
?>