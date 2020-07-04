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

        $leftSlideMsg = $this->model_catalog_information->getInfoMessage(9);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['leftSlideMsg'] = $leftSlideMsg;


		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function deliveryaddress()
    {
        $this->load->model('localisation/country');
        $this->load->model('catalog/information');

        if(!empty($this->request->post) && $this->request->post['country']) {

            //zone
//            $zone_id = $this->request->post['state'];
//            $zoneData = $this->model_localisation_zone->getZone($zone_id);
//            $this->session->data['loggedInState'] = $zoneData['name'];

            //country
            $country_id = $this->request->post['country'];
            $countryData = $this->model_localisation_country->getCountry($country_id);
            //$this->session->data['loggedInCountry'] = $zoneData['name'].', '.$countryData['name'];
            $this->session->data['loggedInCountry'] = $countryData['name'];

            $this->session->data['session_country_id'] = $country_id;
        }
        return true;
    }
}
