<?php
class ControllerExtensionModuleOpcZendeskchat extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('extension/module/opc_zendeskchat');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$this->load->model('setting/store');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_opc_zendeskchat', $this->request->post, $this->request->post['module_opc_zendeskchat_store']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$opc_error = array(
		  'warning',
		  'code_head',
		);

		foreach ($opc_error as $key => $value) {
		  if (isset($this->error[$value])) {
		    $data['error_'.$value] = $this->error[$value];
		  } else {
		    $data['error_'.$value] = '';
		  }
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
			'href' => $this->url->link('extension/module/opc_zendeskchat', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_guide'] = $this->url->link('extension/module/opc_zendeskchat/user_guide', 'user_token=' . $this->session->data['user_token'], true);

		$data['action'] = $this->url->link('extension/module/opc_zendeskchat', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$config_array = array(
			'module_opc_zendeskchat_status',
			'module_opc_zendeskchat_store',
			'module_opc_zendeskchat_code_head',
		);

		foreach ($config_array as $key => $value) {
			if (isset($this->request->post[$value])) {
				$data[$value] = $this->request->post[$value];
			} else {
				$data[$value] = $this->config->get($value);
			}
		}

		if (isset($this->request->get['store']) && $this->request->get['store']) {
			$data = array_merge($data, $this->model_setting_setting->getSetting('module_opc_zendeskchat', $this->request->get['store']));
		}

		if (isset($this->request->get['store']) && $this->request->get['store']) {
			$data['module_opc_zendeskchat_store'] = $this->request->get['store'];
		}

		$data['stores'] = array();

		$data['stores'][] = array(
			'id' => 0,
			'name' => $this->config->get('config_name')
		);

		$stores = $this->model_setting_store->getStores();

		if ($stores) {
			foreach ($stores as $key => $store) {
				$data['stores'][] = array(
					'id' => $store['store_id'],
					'name' => $store['name']
				);
			}
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/opc_zendeskchat', $data));
	}

	public function user_guide() {
	  $this->document->setTitle('Opencart Zendesk Chat Integration User Guide');

	  $data['cancel'] = $this->url->link('extension/module/opc_zendeskchat', 'user_token=' . $this->session->data['user_token'], true);

	  $data['user_token'] = $this->session->data['user_token'];

	  $data['header'] = $this->load->controller('common/header');

	  $data['column_left'] = $this->load->controller('common/column_left');

	  $data['footer'] = $this->load->controller('common/footer');

	  $this->response->setOutput($this->load->view('extension/module/opc_zendeskchat_user_guide', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/opc_zendeskchat')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$opc_error = array(
		  'code_head',
		);

		foreach ($opc_error as $key => $value) {
		  if (!isset($this->request->post['module_opc_zendeskchat_'.$value]) || !$this->request->post['module_opc_zendeskchat_'.$value]) {
		    $this->error[$value] = $this->language->get('error_'.$value);
		  }
		}

		return !$this->error;
	}
}
