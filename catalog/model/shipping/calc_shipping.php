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

	public function getDistanceInfo($delivery_setting_id = -1, $shipping_string = "", $shipping_id = -1) {
		
        $distance_array = array();
		$shipping_string = trim($shipping_string);
        
        if($shipping_id > 0){
            
        } elseif($delivery_setting_id > 0 && $shipping_string != ""){
           
           $product_query = $this->db->query("SELECT id, mode, country, city, adress, free_distance, price_per_one, active FROM " . DB_PREFIX . "delivery_settings WHERE id = ".$delivery_setting_id." and active = 1;");
           ////$query->row['total'];
           foreach ($product_query->rows as $product) {
             $delivery_id = $product['id'];
             $mode = $product['mode'];
             $country = $product['country'];
             $city = $product['city'];
             $adress = $product['adress'];
             $free_distance = $product['free_distance'];
             $price_per_one = $product['price_per_one'];
           }
           // && ($_SESSION['shipping_price_array']['shipping_string'] != $shipping_string && $_SESSION['shipping_price_array']['city_id'] != $city_id)
           if ($shipping_string != "" && $delivery_id > 0){
            
		     $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ','));
             
             $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($adress)."+".urlencode($city)."+".urlencode($country)."&destinations=".urlencode($shipping_string)."+".urlencode($city)."+".urlencode($country)."&mode=driving&language=".$language."&sensor=false";

             $shipping_array = json_decode(implode("", file($url)), true);
           
             $distance_array = $shipping_array['rows'][0]['elements'][0]['distance'];
             $distance_array['value'] = round($distance_array['value']/1000, 1);
             $distance_array['distance_text'] = $distance_array['value']."km";
             $distance_array['shipping_string'] = $shipping_string;
             $distance_array['city_id'] = $city_id;
             $distance_array['url'] = $url;
             
             if ($distance_array['value'] <= $free_distance) {
               $distance_array['price'] = 0;
             } else {
               if ($mode == 1) {
                 $distance_array['price'] = round(($distance_array['value']-$free_distance)*$price_per_one, 0);
               } elseif ($mode == 2) $distance_array['price'] = round($distance_array['value']*$price_per_one, 0);
             }
             $distance_array['price_text'] = $distance_array['price'].$this->language->get('currency_text');
             $distance_array['distance_text'] = $distance_array['value']."km";
             
             $shipping_status = $shipping_array['status'];
             $destination_addresses = $shipping_array['destination_addresses'][0];
             
             $_SESSION['shipping_price_array'] = $distance_array;
             
           }
        }
         
		return $distance_array;
	}

}
?>