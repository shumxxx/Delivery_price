<?php
class ModelShippingCalcShipping extends Model {
	function getQuote($address) {
		$this->load->language('shipping/calc_shipping');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('calc_shipping_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		if (!$this->config->get('calc_shipping_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();
	
		if ($status) {
			$quote_data = array();
			
      		$quote_data['calc_shipping'] = array(
        		'code'         => 'calc_shipping.calc_shipping',
        		'title'        => $this->language->get('text_description'),
        		'cost'         => $this->config->get('calc_shipping_cost'),
        		'tax_class_id' => $this->config->get('calc_shipping_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($this->config->get('calc_shipping_cost'), $this->config->get('calc_shipping_tax_class_id'), $this->config->get('config_tax')))
      		);

      		$method_data = array(
        		'code'       => 'calc_shipping',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('calc_shipping_sort_order'),
        		'error'      => false
      		);
		}
	
		return $method_data;
	}	
	
	public function getStoreList() {
		$country_data = array();
		
		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_settings WHERE active = '1' ORDER BY sort, city ASC");
	
			$country_data = $query->rows;

		}

		return $country_data;
	}

}
?>