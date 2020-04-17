<?php
class ControllerExtensionModuleTracking extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/tracking');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_tracking', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['entry_start'] 	= $this->language->get('entry_start');
		$data['entry_end'] 	= $this->language->get('entry_end');
		$data['entry_status'] 	= $this->language->get('entry_status');
		
		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['module_tracking_start'])) {
			$data['error_module_tracking_start'] = $this->error['module_tracking_start'];
		} else {
			$data['error_module_tracking_start'] = '';
		}
		if (isset($this->error['module_tracking_end'])) {
			$data['error_module_tracking_end'] = $this->error['module_tracking_end'];
		} else {
			$data['error_module_tracking_end'] = '';
		}
		if (isset($this->error['module_tracking_license'])) {
			$data['error_module_tracking_license'] = $this->error['module_tracking_license'];
		} else {
			$data['error_module_tracking_license'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tracking', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/tracking', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_tracking_status'])) {
			$data['module_tracking_status'] = $this->request->post['module_tracking_status'];
		} else {
			$data['module_tracking_status'] = $this->config->get('module_tracking_status');
		}

		if (isset($this->request->post['module_tracking_start'])) {
			$data['module_tracking_start'] = $this->request->post['module_tracking_start'];
		} else {
			$data['module_tracking_start'] = $this->config->get('module_tracking_start');
		}

		if (isset($this->request->post['module_tracking_end'])) {
			$data['module_tracking_end'] = $this->request->post['module_tracking_end'];
		} else {
			$data['module_tracking_end'] = $this->config->get('module_tracking_end');
		}
		if (isset($this->request->post['module_tracking_license'])) {
			$data['module_tracking_license'] = $this->request->post['module_tracking_license'];
		} else {
			$data['module_tracking_license'] = $this->config->get('module_tracking_license');
		}
		
		function UniqueMachineID($salt = "") {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
        if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
        $output = shell_exec("diskpart /s ".$temp);
        $lines = explode("\n",$output);
        $result = array_filter($lines,function($line) {
            return stripos($line,"ID:")!==false;
        });
        if(count($result)>0) {
            $result = array_shift($result);
            $result = explode(":",$result);
            $result = trim(end($result));       
        } else $result = $output;       
		} else {
        $result = shell_exec("blkid -o value -s UUID");  
        if(stripos($result,"blkid")!==false) {
            $result = $_SERVER['HTTP_HOST'];
        }
		}   
		return md5($salt.md5($result));
		}
		
		$hwid = UniqueMachineID();
		$data['module_tracking_hwid'] = $hwid;


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tracking', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tracking')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!utf8_strlen($this->request->post['module_tracking_start']) || !is_numeric($this->request->post['module_tracking_start'])) {
			$this->error['module_tracking_start'] = $this->language->get('error_start');
		}
		if (!utf8_strlen($this->request->post['module_tracking_end']) || !is_numeric($this->request->post['module_tracking_end'])) {
			$this->error['module_tracking_end'] = $this->language->get('error_end');
		}
		if (!utf8_strlen($this->request->post['module_tracking_license'])) {
			$this->error['module_tracking_license'] = $this->language->get('error_license');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://hyatna-eg.com/admin/license.php?key='.$this->request->post['module_tracking_license'].'&hwid='.$this->request->post['module_tracking_hwid']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_REFERER, 'https://autorized.domain');
		$html = curl_exec($ch);
		if ($html != 'Valid') {
			$this->error['module_tracking_license'] = $this->language->get('error_license');
		}


		return !$this->error;
	}
}