<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {

		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {

            $orderId = $this->session->data['order_id'];

            //START : send request to delivery partner
            $this->load->model('account/request');
            $this->model_account_request->sendRequestToDeliveryPartner($orderId);
            //END

            $this->load->model('setting/setting');

            $from = $this->config->get('config_email');
            //Mail send to  Customer
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo('yogeshphp.alkurn@gmail.com');
            $mail->setFrom($from);
            $mail->setSender('TheChampionMallWilson');
            $mail->setSubject(html_entity_decode(sprintf('Test mail', 'TheChampionMall', $orderId), ENT_QUOTES, 'UTF-8'));
            $mail->setText('Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In ac felis quis tortor malesuada pretium. Ut leo. Duis vel nibh at velit scelerisque suscipit. Cras non dolor.

Vestibulum turpis sem, aliquet eget, lobortis pellentesque, rutrum eu, nisl. Etiam sit amet orci eget eros faucibus tincidunt. Fusce a quam. Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Sed hendrerit.');
            $mail->send();

            $this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'),
                $this->url->link('account/account', '', true),
                $this->url->link('account/order', '', true),
                $this->url->link('account/download', '', true),
                $orderId,
                $this->url->link('information/contact')
            );
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('account/edit');

//		$data['column_left'] = $this->load->controller('common/column_left');
//		$data['column_right'] = $this->load->controller('common/column_right');
//		$data['content_top'] = $this->load->controller('common/content_top');
//		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}