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
        $calc = 0;
        $price = 0;
        
        if($shipping_id > 0){
            
          $adress_query = $this->db->query("select a.address_id, a.delivery_distance, a.delivery_setting_id, a.address_1, a.address_2, ds.mode, ds.free_distance, ds.price_per_one from address a
                                            left join delivery_settings ds on ds.id = a.delivery_setting_id
                                            where a.address_id = ".$shipping_id.";");

          foreach ($adress_query->rows as $adress) {
            $address_id = $adress['address_id'];
            $delivery_distance = $adress['delivery_distance'];
            $delivery_setting_id = $adress['delivery_setting_id'];
            $mode = $adress['mode'];
            $free_distance = $adress['free_distance'];
            $price_per_one = $adress['price_per_one'];
            $address_1 = $adress['address_1'];
            $address_2 = $adress['address_2'];
          }
          if ($delivery_distance > 0){
            if ($delivery_distance <= $free_distance) {
              $price = 0;
            } else {
              if ($mode == 1) $price = round(($delivery_distance-$free_distance)*$price_per_one, 0);
              elseif ($mode == 2) $price = round($delivery_distance*$price_per_one, 0);
            }
          } else {
            $shipping_string = trim($address_1." ".$address_2);
            $calc = 1;
          }
          
        }

        if(($delivery_setting_id > 0 && $shipping_string != "") || ($calc == 1 && $shipping_id > 0)){
           
           $settings_query = $this->db->query("SELECT id, mode, country, city, adress, free_distance, price_per_one, active FROM " . DB_PREFIX . "delivery_settings WHERE id = ".$delivery_setting_id." and active = 1;");
           ////$query->row['total'];
           foreach ($settings_query->rows as $setting) {
             $delivery_id = $setting['id'];
             $mode = $setting['mode'];
             $country = $setting['country'];
             $city = $setting['city'];
             $adress = $setting['adress'];
             $free_distance = $setting['free_distance'];
             $price_per_one = $setting['price_per_one'];
           }
        
           if ($shipping_string != "" && $delivery_id > 0){
            
		     $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ','));
             
             $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($adress)."+".urlencode($city)."+".urlencode($country)."&destinations=".urlencode($shipping_string)."+".urlencode($city)."+".urlencode($country)."&mode=driving&language=".$language."&sensor=false";

             $shipping_array = json_decode(implode("", file($url)), true);
           
             $distance_array = $shipping_array['rows'][0]['elements'][0]['distance'];
             $distance_array['value'] = round($distance_array['value']/1000, 1);
             //$distance_array['distance_text'] = $distance_array['value']."km";
             $distance_array['shipping_string'] = $shipping_string;
             $distance_array['delivery_setting_id'] = $delivery_id;
             $distance_array['url'] = $url;
             
             if ($distance_array['value'] <= $free_distance) {
               $distance_array['price'] = 0;
             } else {
               if ($mode == 1) {
                 $price = round(($distance_array['value']-$free_distance)*$price_per_one, 0);
               } elseif ($mode == 2) $price = round($distance_array['value']*$price_per_one, 0);
             }

             $delivery_distance = $distance_array['value'];
             
             if($shipping_id > 0) $update_adress_query = $this->db->query("UPDATE address SET delivery_distance = ". $distance_array['value'] ." WHERE address_id = ".$shipping_id.";");

             //$shipping_status = $shipping_array['status'];
             //$destination_addresses = $shipping_array['destination_addresses'][0];

           }

        }
        
        $distance_array['price_text'] = $price.$this->language->get('currency_text');
        $distance_array['distance_text'] = $delivery_distance."km";
        $distance_array['price'] = $price;
        
        $_SESSION['shipping_price_array'] = $distance_array; 
        
		return $distance_array;
	}

}
?>