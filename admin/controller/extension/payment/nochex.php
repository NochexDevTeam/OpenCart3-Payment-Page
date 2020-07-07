<?php
class ControllerExtensionPaymentNOCHEX extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/nochex');

		$this->document->setTitle("Nochex");

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_nochex', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success'); 
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = "Nochex";

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_seller'] = $this->language->get('text_seller');
		$data['text_merchant'] = $this->language->get('text_merchant');

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_account'] = $this->language->get('entry_account');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_template'] = $this->language->get('entry_template');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['entry_hide'] = $this->language->get('entry_hide');
		$data['entry_callback'] = $this->language->get('entry_callback');
		
		$data['entry_xmlcollection'] = $this->language->get('entry_xmlcollection');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_postage'] = $this->language->get('entry_postage');
		
		$data['help_test'] = $this->language->get('help_test');
		
		$data['help_billing'] = $this->language->get('help_billing');
		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_postage'] = $this->language->get('help_postage');
		$data['help_xml'] = $this->language->get('help_xml');
		$data['help_callback'] = $this->language->get('help_callback');
		$data['help_merchantid'] = $this->language->get('help_merchantid');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => "Payment",
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => "Nochex",
			'href' => $this->url->link('extension/payment/nochex', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/nochex', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_nochex_email'])) {
			$data['payment_nochex_email'] = $this->request->post['payment_nochex_email'];
		} else {
			$data['payment_nochex_email'] = $this->config->get('payment_nochex_email');
		}

		if (isset($this->request->post['payment_nochex_account'])) { 
			$data['payment_nochex_account'] = $this->request->post['payment_nochex_account'];
		} else { 
			$data['payment_nochex_account'] = $this->config->get('payment_nochex_account');
		}

		if (isset($this->request->post['payment_nochex_merchant'])) { 
			$data['payment_nochex_merchant'] = $this->request->post['payment_nochex_merchant'];
		} else { 
			$data['payment_nochex_merchant'] = $this->config->get('payment_nochex_merchant');
		}

		if (isset($this->request->post['payment_nochex_template'])) { 
			$data['payment_nochex_template'] = $this->request->post['payment_nochex_template'];
		} else { 
			$data['payment_nochex_template'] = $this->config->get('payment_nochex_template');
		}

		if (isset($this->request->post['payment_nochex_test'])) { 
			$data['payment_nochex_test'] = $this->request->post['payment_nochex_test'];
		} else { 
			$data['payment_nochex_test'] = $this->config->get('payment_nochex_test');
		}
				
		if (isset($this->request->post['payment_nochex_xmlcollection'])) { 
			$data['payment_nochex_xmlcollection'] = $this->request->post['payment_nochex_xmlcollection'];
		} else { 
			$data['payment_nochex_xmlcollection'] = $this->config->get('payment_nochex_xmlcollection');
		}
		if (isset($this->request->post['payment_nochex_debug'])) { 
			$data['payment_nochex_debug'] = $this->request->post['payment_nochex_debug'];
		} else { 
			$data['payment_nochex_debug'] = $this->config->get('payment_nochex_debug');
		}
		
		if (isset($this->request->post['payment_nochex_postage'])) { 
			$data['payment_nochex_postage'] = $this->request->post['payment_nochex_postage'];
		} else { 
			$data['payment_nochex_postage'] = $this->config->get('payment_nochex_postage');
		}
		
		if (isset($this->request->post['payment_nochex_hide'])) { 
			$data['payment_nochex_hide'] = $this->request->post['payment_nochex_hide'];
		} else { 
			$data['payment_nochex_hide'] = $this->config->get('payment_nochex_hide');
		}
		
		if (isset($this->request->post['payment_nochex_total'])) { 
			$data['payment_nochex_total'] = $this->request->post['payment_nochex_total'];
		} else { 
			$data['payment_nochex_total'] = $this->config->get('payment_nochex_total');
		}

		if (isset($this->request->post['payment_nochex_order_status_id'])) {
			$data['payment_nochex_order_status_id'] = $this->request->post['payment_nochex_order_status_id']; 
		} else { 
			$data['payment_nochex_order_status_id'] = $this->config->get('payment_nochex_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_nochex_geo_zone_id'])) { 
			$data['payment_nochex_geo_zone_id'] = $this->request->post['payment_nochex_geo_zone_id'];
		} else { 
			$data['payment_nochex_geo_zone_id'] = $this->config->get('payment_nochex_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['payment_geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
 
		if (isset($this->request->post['payment_nochex_status'])) {
 
			$data['payment_nochex_status'] = $this->request->post['payment_nochex_status'];

		} else {
 
			$data['payment_nochex_status'] = $this->config->get('payment_nochex_status');
		}

		if (isset($this->request->post['payment_nochex_sort_order'])) { 
			$data['payment_nochex_sort_order'] = $this->request->post['payment_nochex_sort_order'];
		} else { 
			$data['payment_nochex_sort_order'] = $this->config->get('payment_nochex_sort_order');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/nochex', $data));
	}

	protected function validate() {
	
		if (!$this->user->hasPermission('modify', 'extension/payment/nochex')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_nochex_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		return !$this->error;
	}
}
