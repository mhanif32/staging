<?php
class ControllerCommonHome extends Controller {
	public function index() {

        $this->load->model('catalog/information');

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

        $leftSlideMsg = $this->model_catalog_information->getInfoMessage(1);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['leftMsgTitle'] = $leftSlideMsg['title'];
		$data['leftMsgDesc'] = html_entity_decode($leftSlideMsg['description'], ENT_QUOTES, 'UTF-8');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function deliveryaddress()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/area');
        $this->load->model('catalog/information');

        //print_r($this->request->post);exit('okok');

        $stringAddress = []; $strAddress = '';
        if(!empty($this->request->post) && $this->request->post['country']) {

            //country
            $country_id = $this->request->post['country'];
            $countryData = $this->model_localisation_country->getCountry($country_id);
            $stringAddress[] = $this->session->data['loggedInCountry'] = !empty($countryData['name']) ? $countryData['name'] : '';

            //zone
            if(!empty($this->request->post['state'])) {

                $zone_id = $this->request->post['state'];
                $zoneData = $this->model_localisation_zone->getZone($zone_id);
                $stringAddress[] = $this->session->data['loggedInState'] = !empty($zoneData['name']) ? $zoneData['name'] : '';
            }

            //city
            if(!empty($this->request->post['city'])) {
                $city_id = $this->request->post['city'];
                $cityData = $this->model_localisation_area->getArea($city_id);
                $stringAddress[] = $this->session->data['loggedInCity'] = !empty($cityData['name']) ? $cityData['name'] : '';
            }
//print_r($stringAddress);exit('kokok');
            $this->session->data['session_country_id'] = $country_id;
            $strAddress = implode(', ', $stringAddress);
        }
        //print_r($strAddress);exit('kokok');
        $json = [
            'result' => $strAddress,
            'success' => true
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
