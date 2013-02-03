<?php
//class ControllerShippingcalc_shipping extends Controller {
class ControllerShippingCalcShipping extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('shipping/calc_shipping');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('calc_shipping', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_none'] = $this->language->get('text_none');
		
		$this->data['entry_cost'] = $this->language->get('entry_cost');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('shipping/calc_shipping', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('shipping/calc_shipping', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['calc_shipping_cost'])) {
			$this->data['calc_shipping_cost'] = $this->request->post['calc_shipping_cost'];
		} else {
			$this->data['calc_shipping_cost'] = $this->config->get('calc_shipping_cost');
		}

		if (isset($this->request->post['calc_shipping_tax_class_id'])) {
			$this->data['calc_shipping_tax_class_id'] = $this->request->post['calc_shipping_tax_class_id'];
		} else {
			$this->data['calc_shipping_tax_class_id'] = $this->config->get('calc_shipping_tax_class_id');
		}

		if (isset($this->request->post['calc_shipping_geo_zone_id'])) {
			$this->data['calc_shipping_geo_zone_id'] = $this->request->post['calc_shipping_geo_zone_id'];
		} else {
			$this->data['calc_shipping_geo_zone_id'] = $this->config->get('calc_shipping_geo_zone_id');
		}
		
		if (isset($this->request->post['calc_shipping_status'])) {
			$this->data['calc_shipping_status'] = $this->request->post['calc_shipping_status'];
		} else {
			$this->data['calc_shipping_status'] = $this->config->get('calc_shipping_status');
		}
		
		if (isset($this->request->post['calc_shipping_sort_order'])) {
			$this->data['calc_shipping_sort_order'] = $this->request->post['calc_shipping_sort_order'];
		} else {
			$this->data['calc_shipping_sort_order'] = $this->config->get('calc_shipping_sort_order');
		}				

		$this->load->model('localisation/tax_class');
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        
        $this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();
        
        //Get delivery settings
        $product_query = $this->db->query("SELECT id, mode, country, city, adress, free_distance, price_per_one, active FROM " . DB_PREFIX . "delivery_settings;");
        $delivery_list = array();
        foreach ($product_query->rows as $product) {
          $delivery_list[$product['id']] = array("mode"=>$product['mode'], 
                                               "country"=>$product['country'],
                                               "city"=>$product['city'],
                                               "adress"=>$product['adress'],
                                               "free_distance"=>$product['free_distance'],
                                               "price_per_one"=>$product['price_per_one'],
                                               "active"=>$product['active']);
   
        }
		$this->data['delivery_list'] = $delivery_list;
        $this->data['token'] = $this->session->data['token'];
        				
		$this->template = 'shipping/calc_shipping.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/calc_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
    
	public function save_delivery_settings() {
      //$this->load->model('shipping/calc_shipping');
      //$this->model_shipping_calc_shipping->SaveSettings();
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $delivery_id = (int)$this->request->post['delivery_id'];  
        $country = $this->request->post['country'];
        $city = $this->request->post['city'];  
        $adress = $this->request->post['adress'];
        $free_distance = (float)$this->request->post['free_distance'];  
        $price_per_one = (float)$this->request->post['price_per_one'];
        $calc_active = (int)$this->request->post['active'];
        //if($this->request->post['calc_active'] == 'on') $calc_active = 1;

        if ($delivery_id > 0) {
          $free_distance = round($free_distance, 3);  
          $price_per_one = round($price_per_one, 2);

          $this->db->query("UPDATE " . DB_PREFIX . "delivery_settings SET country = '" . $country . "', city = '" . $city . "', 
                                                                          adress = '" . $adress . "', free_distance = " . $free_distance . ",
                                                                          price_per_one = " . $price_per_one . ", active = " . $calc_active . " 
                            WHERE id = " . $delivery_id);
        } 
      }
	}
    
	public function add_delivery_setting() {

      $store_id = -1;
      $status = "ERROR";
      $message = "";
      
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $country = $this->request->post['country'];
        $city = $this->request->post['city'];  
        $adress = $this->request->post['adress'];
        $free_distance = (float)$this->request->post['free_distance'];  
        $price_per_one = (float)$this->request->post['price_per_one'];
        $calc_active = (int)$this->request->post['active'];
        //if($this->request->post['calc_active'] == 'on') $calc_active = 1;

        if ($country != "" && $city != "" && $adress != "") {
          $free_distance = round($free_distance, 3);  
          $price_per_one = round($price_per_one, 2);

          $this->db->query("INSERT INTO " . DB_PREFIX . "delivery_settings(country, city, adress, free_distance, price_per_one, active) 
                            VALUE ('" . $country . "', '" . $city . "', '" . $adress . "', " . $free_distance . ", " . $price_per_one . ", " . $calc_active . ");" . $delivery_id);
          $store_id = $this->db->getLastId();
          
          $message = "Сохранено"; 
          $status = "OK";
                           
        } else $message = "Запоните все поля $country $city $adress $free_distance $price_per_one $calc_active";
      } else $message = "NOT POST server method ".$this->request->server['REQUEST_METHOD'];
      $data = array("store_id"=>$store_id, "status"=>$status, "message"=>$message, "country"=>$country, "city"=>$city, "adress"=>$adress, "free_distance"=>$free_distance, "price_per_one"=>$price_per_one, "calc_active"=>$calc_active);
      
      $this->response->setOutput(json_encode($data));
      
	}
	public function zone() {
		$output = '<option value="">' . $this->language->get('text_select') . '</option>';
		
		$this->load->model('localisation/zone');

    	$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
        $i = 0;
        $data = array();
        
      	foreach ($results as $result) {
	    	$data['zone'][$i] = array('zone_id' => $result['zone_id'],
                                      'name' => $result['name']);
            $i++;
    	} 
		if (!$results) {
    	    $data['zone'][$i]['zone_id'] = 0;
	        $data['zone'][$i]['name'] = $this->language->get('text_none');
	    	$data['zone'][$i] = array('zone_id' => 0,
                                      'name' => $this->language->get('text_none'));
		}

		$this->response->setOutput(json_encode($data));
  	}
}
?>