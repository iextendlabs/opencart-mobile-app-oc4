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
	/**
	 * Banner
	 *
	 * @return void
	 */
	public function banner(): void {

		$this->load->language('extension/mobile_app/module/mobile_banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tool/image');

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
	/**
	 * Banner Save
	 *
	 * @return void
	 */
	public function banner_save(): void {
		$this->load->language('extension/mobile_app/module/mobile_banner');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/mobile_app/module/mobile_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_mobile_app_banner', $this->request->post);
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/**
	 * Deal
	 *
	 * @return void
	 */
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

		$data['module_mobile_app_deal_end_date']   = $this->config->get('module_mobile_app_deal_end_date') ?? '';
		$data['module_mobile_app_deal_status']     = $this->config->get('module_mobile_app_deal_status') ?? '0';
		$data['module_mobile_app_deal_product'] = $this->config->get('module_mobile_app_deal_product') ?? [];

		if ($this->config->get('module_mobile_app_deal_product')) {
			$product_ids = $this->config->get('module_mobile_app_deal_product');
		} else {
			$product_ids = [];
		}

		if ($this->config->get('module_mobile_app_deal_product_discount')) {
			$product_discounts = $this->config->get('module_mobile_app_deal_product_discount');
		} else {
			$product_discounts = [];
		}
		$this->load->model('catalog/product');
		$data['module_mobile_app_deal_products'] = [];

		foreach ($product_ids as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info) {
				$discount_value = isset($product_discounts[$product_id]) ? (float)$product_discounts[$product_id] : 0;
				
				$data['module_mobile_app_deal_products'][] = [
					'product_id'      => $product_info['product_id'],
					'name'            => $product_info['name'],
					'discount'        => $discount_value,
				];
			}
		}
		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_app_deal', $data));
	}
	/**
	 * Deal Save
	 *
	 * @return void
	 */
	public function deal_save(): void {
		$this->load->language('extension/mobile_app/module/mobile_app_deal');
		$json = [];
		$this->load->model('setting/setting');
		$this->load->model('catalog/product');
		$this->model_setting_setting->editSetting('module_mobile_app_deal', $this->request->post);

		if (isset($this->request->post['module_mobile_app_deal_product']) && isset($this->request->post['module_mobile_app_deal_product_discount'])) {
			
			foreach ($this->request->post['module_mobile_app_deal_product'] as $product_id) {
				$discount = (float)($this->request->post['module_mobile_app_deal_product_discount'][$product_id] ?? 0);
				if ($discount > 0) {
					$product_info = $this->model_catalog_product->getProduct($product_id);
					if ($product_info && isset($product_info['price'])) {
						$original_price = (float)$product_info['price'];
						$discounted_price = $original_price - ($original_price * ($discount / 100));
						$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = 1");
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_discount`
							SET `product_id` = '" . (int)$product_id . "',
								`customer_group_id` = 1,
								`quantity` = 1,
								`priority` = 1,
								`price` = '" . (float)$discounted_price . "',
								`type` = 'fixed',
								`special` = 1,
								`date_start` = '0000-00-00',
								`date_end` = '0000-00-00'");
					}
				} else {
					$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = 1");
				}
			}
		}

		$json['success'] = $this->language->get('text_success');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/**
	 * Feature Category
	 *
	 * @return void
	 */
	public function feature_category(): void
	{
		$this->load->language('extension/mobile_app/module/mobile_feature_categories');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

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
			'href' => $this->url->link('extension/mobile_app/module/mobile_app.feature_category', 'user_token=' . $this->session->data['user_token'])
		];

		$data['module_mobile_app_feature_category_status'] = $this->config->get('module_mobile_app_feature_category_status');

		$data['home_page_categories'] = [];
		$home_page_categories = $this->config->get('module_mobile_app_feature_category_items');

		if (!empty($home_page_categories)) {
			foreach ($home_page_categories as $item) {
				if (empty($item['category_id'])) {
					continue;
				}
				$category_info = $this->model_catalog_category->getCategory($item['category_id']);
				if ($category_info) {
					$products = [];
					if (!empty($item['product'])) {
						foreach ($item['product'] as $product_id) {
							$product_info = $this->model_catalog_product->getProduct($product_id);
							if ($product_info) {
								$products[] = [
									'product_id' => $product_info['product_id'],
									'name'       => $product_info['name']
								];
							}
						}
					}
					$data['home_page_categories'][] = [
						'category_id'   => $category_info['category_id'],
						'name'          => $category_info['name'],
						'products'      => $products
					];
				}
			}
		}


		$data['save'] = $this->url->link('extension/mobile_app/module/mobile_app.feature_category_save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_feature_categories', $data));
	}
	/**
	 * Category Save
	 *
	 * @return void
	 */
	public function feature_category_save(): void
	{
		$this->load->language('extension/mobile_app/module/mobile_feature_categories');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/mobile_app/module/mobile_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_mobile_app_feature_category', $this->request->post);
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/**
	 * Product Autocomplete
	 *
	 * @return void
	 */
	public function product_autocomplete(): void
	{
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			];

			if (isset($this->request->get['filter_category_id']) && !empty($this->request->get['filter_category_id'])) {
				$filter_data['filter_category_id'] = $this->request->get['filter_category_id'];
				$filter_data['filter_sub_category'] = true;
			}

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/**
	 * Trust Badges
	 *
	 * @return void
	 */

	public function trust_badges(): void {
		$this->load->language('extension/mobile_app/module/mobile_trust_badges');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tool/image');

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
			'href' => $this->url->link('extension/mobile_app/module/mobile_app.trust_badges', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/mobile_app/module/mobile_app.trust_badges_save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['user_token'] = $this->session->data['user_token'];

		$data['module_mobile_app_trust_badges_status'] = $this->config->get('module_mobile_app_trust_badges_status');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['trust_badges_items'] = [];
		$trust_badges = $this->config->get('module_mobile_app_trust_badges_items');

		if (!empty($trust_badges)) {
			foreach ($trust_badges as $item) {
				if (isset($item['image']) && is_file(DIR_IMAGE . $item['image'])) {
					$thumb = $this->model_tool_image->resize($item['image'], 100, 100);
				} else {
					$thumb = $data['placeholder'];
				}
				$data['trust_badges_items'][] = [
					'image'             => $item['image'] ?? '',
					'thumb'             => $thumb,
					'title'             => $item['title'] ?? '',
					'short_description' => $item['short_description'] ?? ''
				];
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/mobile_app/module/mobile_trust_badges', $data));
	}
	/**
	 * Trust Badges Save
	 *
	 * @return void
	 */

	public function trust_badges_save(): void
	{
		$this->load->language('extension/mobile_app/module/mobile_trust_badges');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/mobile_app/module/mobile_app')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			// Setting
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_mobile_app_trust_badges', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
}
}
