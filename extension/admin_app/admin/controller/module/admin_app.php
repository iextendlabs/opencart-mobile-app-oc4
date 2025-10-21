<?php

namespace Opencart\Admin\Controller\Extension\AdminApp\Module;

/**
 * Class AdminApp
 *
 * @package Opencart\Admin\Controller\Extension\AdminApp\Module
 */
class AdminApp extends \Opencart\System\Engine\Controller
{
    public function install(): void {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_session` (
            `api_session_id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `token` varchar(32) NOT NULL,
            `date_added` datetime NOT NULL,
            `date_modified` datetime NOT NULL,
            `expire_date` datetime NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`api_session_id`),
            KEY `token` (`token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
	
	public function uninstall(): void {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "api_session`");
    }

	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('extension/admin_app/module/admin_app');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/admin_app/module/admin_app', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/admin_app/module/admin_app.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_admin_app_status'] = $this->config->get('module_admin_app_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/admin_app/module/admin_app', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void
	{
		$this->load->language('extension/admin_app/module/admin_app');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/admin_app/module/admin_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			// Setting
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_admin_app', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
