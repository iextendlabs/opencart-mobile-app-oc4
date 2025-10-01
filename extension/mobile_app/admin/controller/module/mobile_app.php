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
		$data['deal'] = $this->url->link('extension/mobile_app/module/mobile_app.deal', 'user_token=' . $this->session->data['user_token']);

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
		$data['module_mobile_app_banner_status'] = $this->config->get('module_mobile_app_banner_status') ?? '0';

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

	public function deal(): void {
		$this->load->language('extension/mobile_app/module/mobile_app_deal');

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

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/mobile_app/module/mobile_app_deal', 'user_token=' . $this->session->data['user_token'])
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/mobile_app/module/mobile_app_deal', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'])
			];
		}

		$data['save'] = $this->url->link('extension/mobile_app/module/mobile_app.deal_save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
        $data['status'] = $this->url->link('extension/mobile_app/module/mobile_app', 'user_token=' . $this->session->data['user_token']);

		$data['module_mobile_app_deal_end_date']   = $this->config->get('module_mobile_app_deal_end_date') ?? '';
		$data['module_mobile_app_deal_product']   = $this->config->get('module_mobile_app_deal_product') ?? '';
		$data['module_mobile_app_deal_status']     = $this->config->get('module_mobile_app_deal_status') ?? '0';
		$data['module_mobile_app_product'] = $this->config->get('module_mobile_app_product') ?? [];

		if (isset($this->request->post['module_mobile_app_product'])) {
			$product_ids = $this->request->post['module_mobile_app_product'];
		} elseif ($this->config->get('module_mobile_app_product')) {
			$product_ids = $this->config->get('module_mobile_app_product');
		} else {
			$product_ids = [];
		}

		if (isset($this->request->post['module_mobile_app_product_discount'])) {
			$product_discounts = $this->request->post['module_mobile_app_product_discount'];
		} elseif ($this->config->get('module_mobile_app_product_discount')) {
			$product_discounts = $this->config->get('module_mobile_app_product_discount');
		} else {
			$product_discounts = [];
		}
		$this->load->model('catalog/product');
		$data['module_mobile_app_products'] = [];

		foreach ($product_ids as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info) {
				$discount_value = isset($product_discounts[$product_id]) ? (float)$product_discounts[$product_id] : 0;
				$price = isset($product_info['price']) ? (float)$product_info['price'] : 0;
				// Calculate discounted price
				$discounted_price = $price;
				if ($discount_value > 0) {
					$discounted_price = $price - ($price * ($discount_value / 100));
				}
				$data['module_mobile_app_products'][] = [
					'product_id'      => $product_info['product_id'],
					'name'            => $product_info['name'],
					'price'           => $price,
					'discount'        => $discount_value,
					'discounted_price'=> $discounted_price
				];
			}
		}
		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_app_deal', $data));
	}

	public function deal_save(): void {
		$this->load->language('extension/mobile_app/module/mobile_app_deal');
		$json = [];
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_mobile_app', $this->request->post);

		// Update product discounts in product_discount table
		if (isset($this->request->post['module_mobile_app_product']) && isset($this->request->post['module_mobile_app_product_discount'])) {
			$this->load->model('catalog/product');
			foreach ($this->request->post['module_mobile_app_product'] as $product_id) {
				$discount = (float)($this->request->post['module_mobile_app_product_discount'][$product_id] ?? 0);
				if ($discount > 0) {
					// Get original price
					$product_info = $this->model_catalog_product->getProduct($product_id);
					if ($product_info && isset($product_info['price'])) {
						$original_price = (float)$product_info['price'];
						$discounted_price = $original_price - ($original_price * ($discount / 100));
						// Remove existing discount for this product/customer group
						$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = 1");
						// Insert new discount
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_discount`
							SET `product_id` = '" . (int)$product_id . "',
								`customer_group_id` = 1,
								`quantity` = 1,
								`priority` = 1,
								`price` = '" . (float)$discounted_price . "',
								`type` = 'fixed',
								`date_start` = '0000-00-00',
								`date_end` = '0000-00-00'");
					}
				} else {
					// Remove discount if discount is 0 or not set
					$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = 1");
				}
			}
		}

		$json['success'] = $this->language->get('text_success');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
}
