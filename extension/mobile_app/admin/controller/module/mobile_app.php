<?php
namespace Opencart\Admin\Controller\Extension\MobileApp\Module;
/**
 * Class MobileApp
 *
 * @package Opencart\Admin\Controller\Extension\MobileApp\Module
 */
class MobileApp extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/mobile_app/module/mobile_app');

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
			'href' => $this->url->link('extension/mobile_app/module/mobile_app', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/mobile_app/module/mobile_app.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['banner'] = $this->url->link('extension/mobile_app/module/mobile_app.banner', 'user_token=' . $this->session->data['user_token']);

		$data['module_mobile_app_status'] = $this->config->get('module_mobile_app_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_app', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/mobile_app/module/mobile_app');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/mobile_app/module/mobile_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			// Setting
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_mobile_app', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function banner(): void {

        $this->load->language('extension/mobile_app/module/mobile_banner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_mobile_app', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));
        }
   
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
            'href' => $this->url->link('extension/mobile_app/module/mobile_banner', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['save'] = $this->url->link('extension/mobile_app/module/mobile_app.banner_save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
        $data['status'] = $this->url->link('extension/mobile_app/module/mobile_app', 'user_token=' . $this->session->data['user_token']);

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 300, 300);
		$data['module_mobile_app_banner_status'] = $this->config->get('module_mobile_app_banner_status') ?? 0;
		$banner_images = $this->config->get('module_mobile_app_banner_image') ?? [];

		// Structure banner_images as a flat array for the view
		$data['banner_images'] = [];
		if (is_array($banner_images)) {
			foreach ($banner_images as $banner) {
				if (!empty($banner['image']) && is_file(DIR_IMAGE . $banner['image'])) {
					$thumb = $this->model_tool_image->resize($banner['image'], 300, 300);
				} else {
					$thumb = $data['placeholder'];
				}
				$data['banner_images'][] = [
					'title'      => $banner['title'] ?? '',
					'link'       => $banner['link'] ?? '',
					'image'      => $banner['image'] ?? '',
					'thumb'      => $thumb,
					'sort_order' => $banner['sort_order'] ?? ''
				];
			}
		}

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_banner', $data));
    }

	public function banner_save(): void {
		$this->load->language('extension/mobile_app/module/mobile_banner');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/mobile_app/module/mobile_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_mobile_app', $this->request->post);
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

}
