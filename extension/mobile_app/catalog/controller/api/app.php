<?php

namespace Opencart\Catalog\Controller\Extension\MobileApp\Api;

/**
 * Class App
 *
 * @package Opencart\Catalog\Controller\Extension\MobileApp\Api
 */


class App extends \Opencart\System\Engine\Controller
{
    public function __construct($registry) {
        parent::__construct($registry);
        $this->initCurrency();
    }

    private function initCurrency(): void {
        $this->load->model('localisation/currency');
        
        // Check currency from header first
        $currency_code = isset($this->request->server['HTTP_X_CURRENCY']) ? $this->request->server['HTTP_X_CURRENCY'] : '';
        
        // If no header, check from request parameter
        if (!$currency_code && isset($this->request->get['currency'])) {
            $currency_code = $this->request->get['currency'];
        }
        
        // If currency code is provided, validate it
        if ($currency_code) {
            $currency_info = $this->model_localisation_currency->getCurrencyByCode($currency_code);
            if ($currency_info && $currency_info['status']) {
                $this->session->data['currency'] = $currency_code;
                return;
            }
        }
        
        // If no valid currency is set, use default
        if (empty($this->session->data['currency'])) {
            $this->session->data['currency'] = $this->config->get('config_currency');
        }
    }

    public function getHomePageData(): void
    {
        $this->load->model('tool/image');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/review');
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('tool/image');

        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');

        $json['reviews'] = [];

        $sql = "SELECT r.author, r.text, r.rating, r.date_added FROM `" . DB_PREFIX . "review` r WHERE r.status = '1' ORDER BY r.rating DESC, r.date_added DESC LIMIT 5";
        $query = $this->db->query($sql);
        foreach ($query->rows as $review) {
            $json['reviews'][] = [
                'author' => $review['author'],
                'text' => $review['text'],
                'rating' => $review['rating'],
                'date_added' => $review['date_added'],
            ];
        }

        // Banner
        $json['banner'] = [];
        if ($this->config->get('module_mobile_app_banner_status') == '1') {
            $banner_images = $this->config->get('module_mobile_app_banner_image');
            if (!is_array($banner_images)) {
                $banner_images = $banner_images ? [$banner_images] : [];
            }
            foreach ($banner_images as $banner) {
                if (!empty($banner['image'])) {
                    $image_path = $this->model_tool_image->resize($banner['image'], 400, 400);
                    $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
                } else {
                    $image_url = '';
                }

                $link_type = $banner['link_type'] ?? 'external';
                if ($link_type === 'product') {
                    $link_value = isset($banner['product_id']) ? (int)$banner['product_id'] : 0;
                } elseif ($link_type === 'category') {
                    $link_value = isset($banner['category_id']) ? (int)$banner['category_id'] : 0;
                } else {
                    $link_value = $banner['link'] ?? '';
                }

                $item = [
                    'title' => $banner['title'] ?? '',
                    'link_type' => $link_type,
                    'link' => $link_value,
                    'sort_order' => $banner['sort_order'] ?? ''
                ];

                if ($image_url !== '') {
                    $item['image'] = $image_url;
                    $json['banner'][] = $item;
                }
            }
        }

        // Deal
        $json['deal'] = null;
        $deal_status = $this->config->get('module_mobile_app_deal_status');
        $deal_end_date = $this->config->get('module_mobile_app_deal_end_date') ?? '';
        $product_ids = $this->config->get('module_mobile_app_deal_product');
        if (!is_array($product_ids)) {
            $product_ids = $product_ids ? [$product_ids] : [];
        }
        $now = date('Y-m-d H:i:s');
        if ($deal_status == '1') {
            if ($deal_end_date && strtotime($now) > strtotime($deal_end_date)) {
                foreach ($product_ids as $product_id) {
                    $this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = 1");
                }
                $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '0' WHERE `code` = 'module_mobile_app_deal' AND `key` = 'module_mobile_app_deal_status'");
                $json['deal'] = null;
            } else {
                $deal = [];
                $deal['end_date'] = $deal_end_date;
                $deal['current_date'] = $now;
                $deal['products'] = [];
                foreach ($product_ids as $product_id) {
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if ($product_info) {
                        $card = $this->formatProductCard($product_info, 200, 150);
                        $deal['products'][] = $card;
                    }
                }
                if (empty($deal['products'])) {
                    $json['deal'] = null;
                } else {
                    $json['deal'] = $deal;
                }
            }
        }

        // Feature Category
        $json['feature_category'] = [];
        if ($this->config->get('module_mobile_app_feature_category_status') == '1') {
            $feature_categories = $this->config->get('module_mobile_app_feature_category_items') ?? [];
            if (!is_array($feature_categories)) {
                $feature_categories = $feature_categories ? [$feature_categories] : [];
            }
            foreach ($feature_categories as $item) {
                if (empty($item['category_id'])) continue;
                $category_info = $this->model_catalog_category->getCategory($item['category_id']);
                if ($category_info) {
                    $products = [];
                    if (!empty($item['product'])) {
                        foreach ($item['product'] as $product_id) {
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            if ($product_info) {
                                $card = $this->formatProductCard($product_info, 200, 250);
                                $products[] = $card;
                            }
                        }
                    }
                    if (!empty($products)) {
                        $catCard = $this->formatCategoryCard($category_info);
                        $catCard['category_id'] = $catCard['id'];
                        unset($catCard['id']);
                        $catCard['products'] = $products;
                        $json['feature_category'][] = $catCard;
                    }
                }
            }
        }

        // Trust Badges
        $json['trust_badges'] = [];
        if ($this->config->get('module_mobile_app_trust_badges_status') == '1') {
            $trust_badges = $this->config->get('module_mobile_app_trust_badges_items');
            if (!is_array($trust_badges)) {
                $trust_badges = $trust_badges ? [$trust_badges] : [];
            }
            foreach ($trust_badges as $item) {
                if (isset($item['image']) && $item['image']) {
                    $image_path = $this->model_tool_image->resize($item['image'], 100, 100);
                    $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
                } else {
                    $image_url = '';
                }
                $raw_short = $item['short_description'] ?? '';
                $short_clean = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($raw_short, ENT_QUOTES, 'UTF-8'))));
                $json['trust_badges'][] = [
                    'image' => $image_url,
                    'title' => html_entity_decode($item['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'short_description' => $short_clean
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function searchProducts(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $json = [];
        $name = isset($this->request->get['name']) ? trim($this->request->get['name']) : '';
        if ($name === '') {
            $json['error'] = 'Missing product name.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $filter_data = [
            'filter_search' => $name,
            'start' => 0,
            'limit' => 100
        ];
        $products = $this->model_catalog_product->getProducts($filter_data);
        $json['products'] = [];
        foreach ($products as $product) {
            $card = $this->formatProductCard($product, 200, 150);
            $json['products'][] = $card;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getProductDetail(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/category');

        $json = [];
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (!$product_info) {
            $json['error'] = 'Product not found';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');

        // Main image
        if ($product_info['image']) {
            $image_path = $this->model_tool_image->resize($product_info['image'], 500, 500);
            $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
        } else {
            $image_url = '';
        }

        // Additional images
        $images = [];
        $product_images = $this->model_catalog_product->getImages($product_id);
        foreach ($product_images as $img) {
            $img_path = $this->model_tool_image->resize($img['image'], 500, 500);
            $images[] = (strpos($img_path, 'http') === 0) ? $img_path : $server . ltrim($img_path, '/');
        }

        // Manufacturer
        $manufacturer = '';
        if (!empty($product_info['manufacturer_id'])) {
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
            if ($manufacturer_info) {
                $manufacturer = $manufacturer_info['name'];
            }
        }

        // Categories
        $categories = [];
        $product_categories = $this->model_catalog_product->getCategories($product_id);
        foreach ($product_categories as $cat) {
            $cat_id = (int)$cat['category_id'];
            $cat_info = $this->model_catalog_category->getCategory($cat_id);
            if ($cat_info) {
                $categories[] = [
                    'id' => $cat_info['category_id'],
                    'name' => html_entity_decode($cat_info['name'] ?? '', ENT_QUOTES, 'UTF-8')
                ];
            }
        }

        $json['id'] = $product_info['product_id'];
        $json['name'] = html_entity_decode($product_info['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $json['model'] = $product_info['model'];
        $json['reward'] = $product_info['reward'];
        $json['points'] = $product_info['points'];
        $raw_description = html_entity_decode($product_info['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $json['description'] = trim(preg_replace('/\s+/', ' ', strip_tags($raw_description)));
        $json['image'] = $image_url;
        $json['images'] = $images;
        $json['price'] = $this->currency->format($product_info['price'], $this->session->data['currency'] ?? $this->config->get('config_currency'));
        $json['special'] = $product_info['special'] ? $this->currency->format($product_info['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null;
        // Tax
        if ($this->config->get('config_tax')) {
            $json['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency'] ?? $this->config->get('config_currency'));
        } else {
            $json['tax'] = false;
        }
        // Discounts
        $discounts = $this->model_catalog_product->getDiscounts($product_id);
        $json['discounts'] = [];
        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            foreach ($discounts as $discount) {
                $json['discounts'][] = [
                    'quantity' => $discount['quantity'],
                    'price' => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'] ?? $this->config->get('config_currency'))
                ];
            }
        }
        $json['manufacturer'] = $manufacturer;
        $json['quantity'] = $product_info['quantity'];
        // Stock Status
        $this->load->model('localisation/stock_status');
        if ($product_info['quantity'] <= 0) {
            $stock_status_id = $product_info['stock_status_id'];
        } elseif (!$this->config->get('config_stock_display')) {
            $stock_status_id = (int)$this->config->get('config_stock_status_id');
        } else {
            $stock_status_id = 0;
        }
        $stock_status_info = $this->model_localisation_stock_status->getStockStatus($stock_status_id);
        if ($stock_status_info) {
            $json['stock'] = $stock_status_info['name'];
        } else {
            $json['stock'] = $product_info['quantity'];
        }
        $json['status'] = $product_info['status'];
        $json['categories'] = $categories;
        // Rating & Review
        $json['rating'] = (int)$product_info['rating'];
        $json['review_status'] = (int)$this->config->get('config_review_status');
        $json['reviews'] = (int)$product_info['reviews'];
        // Minimum
        $json['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
        // Attribute Groups
        $json['attribute_groups'] = $this->model_catalog_product->getAttributes($product_id);
        // Options
        $json['options'] = [];
        $master_id = $product_info['master_id'] ? (int)$product_info['master_id'] : $product_id;
        $product_options = $this->model_catalog_product->getOptions($master_id);
        foreach ($product_options as $option) {
            $product_option_value_data = [];
            foreach ($option['product_option_value'] as $option_value) {
                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                    if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                        $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'] ?? $this->config->get('config_currency'));
                    } else {
                        $price = false;
                    }
                    if ($option_value['image']) {
                        $image = $option_value['image'];
                    } else {
                        $image = '';
                    }
                    $product_option_value_data[] = [
                        'product_option_value_id' => $option_value['product_option_value_id'],
                        'name' => html_entity_decode($option_value['name'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'image' => $image ? $this->model_tool_image->resize($image, 50, 50) : '',
                        'price' => $price,
                        'price_prefix' => $option_value['price_prefix'],
                        'quantity' => $option_value['quantity'],
                        'subtract' => $option_value['subtract']
                    ];
                }
            }
            $json['options'][] = [
                'product_option_id' => $option['product_option_id'],
                'option_id' => $option['option_id'],
                'name' => html_entity_decode($option['name'] ?? '', ENT_QUOTES, 'UTF-8'),
                'type' => $option['type'],
                'required' => $option['required'],
                'product_option_value' => $product_option_value_data
            ];
        }
        // Tags
        $json['tags'] = [];
        if (!empty($product_info['tag'])) {
            $tags = explode(',', $product_info['tag']);
            foreach ($tags as $tag) {
                $json['tags'][] = trim($tag);
            }
        }
        // Related products (just IDs and names for now)
        $json['related'] = [];
        $related_products = $this->model_catalog_product->getRelated($product_id);
        foreach ($related_products as $related) {
            $related_id = isset($related['product_id']) ? (int)$related['product_id'] : 0;
            if ($related_id) {
                $related_info = $this->model_catalog_product->getProduct($related_id);
                if ($related_info) {
                    $card = $this->formatProductCard($related_info, 200, 150);
                    $json['related'][] = $card;
                }
            }
        }
        // Subscription plans
        $json['subscription_plans'] = [];
        $subscriptions = $this->model_catalog_product->getSubscriptions($product_id);
        foreach ($subscriptions as $result) {
            $description = '';
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                if ($result['duration']) {
                    $price = ($product_info['special'] ?: $product_info['price']) / $result['duration'];
                } else {
                    $price = ($product_info['special'] ?: $product_info['price']);
                }
                $price = $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'] ?? $this->config->get('config_currency'));
                $cycle = $result['cycle'];
                $frequency = $this->language->get('text_' . $result['frequency']);
                $duration = $result['duration'];
                if ($duration) {
                    $description = sprintf($this->language->get('text_subscription_duration'), $price, $cycle, $frequency, $duration);
                } else {
                    $description = sprintf($this->language->get('text_subscription_cancel'), $price, $cycle, $frequency);
                }
            }
            $json['subscription_plans'][] = [
                'description' => $description
            ] + $result;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategoryView(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $json = [];
        $category_id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;

        // Get category info
        $category_info = $this->model_catalog_category->getCategory($category_id);
        if (!$category_info) {
            $json['error'] = 'Category not found';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Description (HTML decoded and cleaned)
        $raw_cat_description = html_entity_decode($category_info['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $json['description'] = trim(preg_replace('/\s+/', ' ', strip_tags($raw_cat_description)));

        // Subcategories
        $subcategories = $this->model_catalog_category->getCategories($category_id);
        $json['subcategories'] = [];
        foreach ($subcategories as $subcategory) {
            $json['subcategories'][] = $this->formatCategoryCard($subcategory);
        }

        // Products in this category
        $filter_data = [
            'filter_category_id' => $category_id,
            'filter_sub_category' => false,
            'start' => 0,
            'limit' => 100
        ];
        $products = $this->model_catalog_product->getProducts($filter_data);
        $json['products'] = [];
        foreach ($products as $product) {
            $card = $this->formatProductCard($product, 200, 150);
            $json['products'][] = $card;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategories(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $json = [];
        $categories = $this->model_catalog_category->getCategories(0); // 0 = top-level
        $result = [];
        foreach ($categories as $category) {
            $result[] = $this->formatCategoryCard($category);
        }
        $json['categories'] = $result;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function formatProductCard(array $product, int $width = 250, int $height = 250): array
    {
        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');

        if (!empty($product['image'])) {
            $image_path = $this->model_tool_image->resize($product['image'], $width, $height);
            $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
        } else {
            $image_url = '';
        }

        $master_id = isset($product['master_id']) && $product['master_id'] ? (int)$product['master_id'] : (int)($product['product_id'] ?? $product['id'] ?? 0);
        $product_options = $this->model_catalog_product->getOptions($master_id);
        $has_options = !empty($product_options);

        $card = [
            'id' => (int)$product['product_id'],
            'name' => html_entity_decode($product['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'image' => $image_url,
            'price' => $this->currency->format($product['price'] ?? 0, $this->session->data['currency'] ?? $this->config->get('config_currency')),
            'special' => !empty($product['special']) ? $this->currency->format($product['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null,
            'options' => $has_options,
            'date_added' => $product['date_added'] ?? ''
        ];

        return $card;
    }

    protected function formatCategoryCard(array $category): array
    {
        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');

        if (!empty($category['image'])) {
            $image_path = $this->model_tool_image->resize($category['image'], 80, 80);
            $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
        } else {
            $image_url = '';
        }

        return [
            'id' => (int)$category['category_id'],
            'name' => html_entity_decode($category['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'image' => $image_url
        ];
    }

    public function prepareCheckoutSession(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $cart = $data['cart'] ?? [];

        if (isset($data['session_id'])) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "cart` WHERE `session_id` = '" . $this->db->escape($data['session_id']) . "'");
        }
        // Login user if credentials provided
        if ($email && $password) {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomerByEmail($email);
            if (!$customer_info || !$this->customer->login($email, $password)) {
                $json['error'] = $this->language->get('error_login');
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            } else {
                // Set customer_token for compatibility with header and other components
                $this->session->data['customer_token'] = oc_token(26);
                $this->session->data['customer'] = [
                    'customer_id'       => $customer_info['customer_id'],
                    'customer_group_id' => $customer_info['customer_group_id'],
                    'firstname'         => $customer_info['firstname'],
                    'lastname'          => $customer_info['lastname'],
                    'email'             => $customer_info['email'],
                    'telephone'         => $customer_info['telephone'],
                    'custom_field'      => $customer_info['custom_field']
                ];
            }
        }

        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                $product_id = (int)($item['product_id'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 1);
                if ($product_id > 0 && $quantity > 0) {
                    $options = $item['option'] ?? $item['options'] ?? [];
                    if (!is_array($options) && is_string($options)) {
                        $decoded = json_decode($options, true);
                        $options = is_array($decoded) ? $decoded : [];
                    }

                    $subscription_plan_id = (int)($item['subscription_plan_id'] ?? $item['subscription_id'] ?? 0);

                    $override = $item['override'] ?? [];
                    if (!is_array($override) && is_string($override)) {
                        $decoded = json_decode($override, true);
                        $override = is_array($decoded) ? $decoded : [];
                    }

                    $this->cart->add($product_id, $quantity, (array)$options, $subscription_plan_id, (array)$override);
                }
            }
        }

        // Return session id
        if (!$this->session->getId()) {
            $this->session->start();
        }
        $session_id = $this->session->getId();
        $json['success'] = true;
        $json['session_id'] = $session_id;
        $json['message'] = 'Session prepared. Use this session id as OCSESSID cookie in webview.';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function syncCart(): void
    {
        $this->load->language('extension/mobile_app/api/app');

        $json = [
            'success' => false,
            'all_product_ids' => [],
            'update_ids' => []
        ];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $session_id = $data['session_id'] ?? '';

        if (empty($session_id)) {
            $json['error'] = 'Session ID is required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $cart_query = $this->db->query("SELECT product_id, quantity FROM `" . DB_PREFIX . "cart` WHERE `session_id` = '" . $this->db->escape($session_id) . "'");

        if ($cart_query->num_rows) {
            $this->load->model('catalog/product');

            foreach ($cart_query->rows as $cart) {
                $product_id = (string)$cart['product_id'];
                $quantity = (int)$cart['quantity'];
                $json['all_product_ids'][] = $product_id;
                $json['update_ids'][$product_id] = $quantity;
            }

            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // User Profile
    public function getProfile(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomer($id);

        if (!$customer_info) {
            $json['error'] = 'User not found';
        } else {
            $json['id'] = $customer_info['customer_id'];
            $json['firstname'] = $customer_info['firstname'];
            $json['lastname'] = $customer_info['lastname'];
            $json['email'] = $customer_info['email'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateProfile(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $firstname = $data['firstname'] ?? '';
        $lastname = $data['lastname'] ?? '';
        $email = $data['email'] ?? '';

        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomer($id);

        if (!$customer_info) {
            $json['error'] = 'User not found';
        } else {
            $update_data = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'telephone' => $customer_info['telephone'] ?? '',
                'custom_field' => $customer_info['custom_field'] ?? []
            ];
            $this->model_account_customer->editCustomer($id, $update_data);
            $json['message'] = 'Profile updated successfully.';
            $json['user'] = [
                'id' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function login(): void
    {
        $this->load->language('extension/mobile_app/api/app');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomerByEmail($email);

        // Validate the credentials
        if (!$customer_info || !$this->customer->login($email, $password)) {
            $json['error'] = $this->language->get('error_login');
        } else {
            $json['user'] = [
                'id' => $customer_info['customer_id'],
                'firstname' => $customer_info['firstname'],
                'lastname' => $customer_info['lastname'],
                'email' => $customer_info['email']
            ];
            $json['message'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function register(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('account/customer');

        $json = [];

        $required = [
            'customer_group_id' => 0,
            'firstname'         => '',
            'lastname'          => '',
            'email'             => '',
            'telephone'         => '',
            'custom_field'      => [],
            'password'          => '',
            'agree'             => 0
        ];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true) + $required;

        $firstname = $data['firstname'] ?? '';
        $lastname = $data['lastname'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validate input
        if (!oc_validate_length($firstname, 1, 32)) {
            $json['error']['firstname'] = $this->language->get('error_firstname');
        }

        if (!oc_validate_length($lastname, 1, 32)) {
            $json['error']['lastname'] = $this->language->get('error_lastname');
        }

        if (!oc_validate_email($email)) {
            $json['error']['email'] = $this->language->get('error_email');
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($email)) {
            $json['error']['warning'] = $this->language->get('error_exists');
        }

        if (!oc_validate_length(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), 4, 40)) {
            $json['error']['password'] = $this->language->get('error_password');
        }

        if (!$json) {
            $customer_id = $this->model_account_customer->addCustomer($data);
            $this->model_account_customer->deleteLoginAttempts($email);
            $customer_info = $this->model_account_customer->getCustomer($customer_id);
            $json['message'] = 'User registered successfully';
            $json['user'] = [
                'id' => $customer_info['customer_id'],
                'firstname' => $customer_info['firstname'],
                'lastname' => $customer_info['lastname'],
                'email' => $customer_info['email']
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function forgotPassword(): void
    {
        $this->load->language('account/forgotten');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $email = $data['email'] ?? '';

        $this->load->model('account/customer');

        $customer_info = $this->model_account_customer->getCustomerByEmail($email);

        if (!$customer_info) {
            $json['error'] = $this->language->get('error_not_found');
        } else {
            $this->model_account_customer->addToken($customer_info['customer_id'], 'password', oc_token(40));
            $json['message'] = 'Password reset link sent to your email.';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function changePassword(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->language('account/password');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $required = [
            'email' => '',
            'currentPassword' => '',
            'newPassword' => '',
            'confirm' => ''
        ];
        $data = $data + $required;

        $email = $data['email'];
        $currentPassword = $data['currentPassword'];
        $newPassword = $data['newPassword'];
        $confirm = $data['confirm'];

        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomerByEmail($email);

        if (!$customer_info || !$this->customer->login($email, $currentPassword)) {
            $json['error']['currentPassword'] = $this->language->get('error_current_password');
        }

        $password_length = (int)$this->config->get('config_password_length');
        $password = html_entity_decode($newPassword, ENT_QUOTES, 'UTF-8');
        if (!oc_validate_length($password, $password_length, 40)) {
            $json['error']['newPassword'] = sprintf($this->language->get('error_password_length'), $password_length);
        }

        $required = [];
        if ($this->config->get('config_password_uppercase') && !preg_match('/[A-Z]/', $password)) {
            $required[] = $this->language->get('error_password_uppercase');
        }
        if ($this->config->get('config_password_lowercase') && !preg_match('/[a-z]/', $password)) {
            $required[] = $this->language->get('error_password_lowercase');
        }
        if ($this->config->get('config_password_number') && !preg_match('/[0-9]/', $password)) {
            $required[] = $this->language->get('error_password_number');
        }
        if ($this->config->get('config_password_symbol') && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $required[] = $this->language->get('error_password_symbol');
        }
        if ($required) {
            $json['error']['newPassword'] = sprintf($this->language->get('error_password'), implode(', ', $required), $password_length);
        }

        // Confirm match
        if ($confirm != $newPassword) {
            $json['error']['confirm'] = $this->language->get('error_confirm');
        }

        if (!$json) {
            $this->model_account_customer->editPassword($email, $newPassword);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function logout(): void
    {
        // Initialize the response array
        $json = [];

        // Clear customer session
        $this->customer->logout();
        unset($this->session->data['shipping_address']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_address']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);

        $json['success'] = true;
        $json['message'] = 'You have been successfully logged out';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getOrders(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('account/order');
        $this->load->model('extension/mobile_app/api/app');
        $this->load->model('localisation/order_status');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $orders = $this->model_extension_mobile_app_api_app->getOrders($customer_id, 0, 999999);

        $json['orders'] = [];
        foreach ($orders as $order) {
            $product_total = $this->model_extension_mobile_app_api_app->getTotalProductsByOrderId($order['order_id']);

            $order_status_info = $this->model_localisation_order_status->getOrderStatus($order['order_status_id']);

            if ($order_status_info) {
                $order_status = $order_status_info['name'];
            } else {
                $order_status = '';
            }
            $json['orders'][] = [
                'order_id' => $order['order_id'],
                'status' => $order_status,
                'products' => $product_total,
                'total' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']),
                'date_added' => $order['date_added'],
            ];
        }

        usort($json['orders'], function ($a, $b) {
            return strtotime($b['date_added']) - strtotime($a['date_added']);
        });

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getOrder(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $this->load->model('account/order');
        $this->load->model('extension/mobile_app/api/app');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        $order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        if (!$order_id) {
            $json['error'] = 'Order ID is required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $order_info = $this->model_extension_mobile_app_api_app->getOrder($order_id, $customer_id);

        if (!$order_info || $order_info['customer_id'] != $customer_id) {
            $json['error'] = 'Order not found or access denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('catalog/product');
        $this->load->model('tool/upload');

        $json['products'] = [];
        $products = $this->model_extension_mobile_app_api_app->getProducts($order_id);

        foreach ($products as $product) {
            $option_data = [];
            $options = $this->model_extension_mobile_app_api_app->getOptions($order_id, $product['order_product_id']);

            foreach ($options as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                    $value = $upload_info ? $upload_info['name'] : '';
                }

                $option_data[] = [
                    'name'  => $option['name'],
                    'value' => $value
                ];
            }

            $json['products'][] = [
                'name'     => $product['name'],
                'model'    => $product['model'],
                'option'   => $option_data,
                'quantity' => $product['quantity'],
                'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
                'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
            ];
        }

        $json['order'] = [
            'order_id'         => $order_info['order_id'],
            'invoice_no'       => $order_info['invoice_no'] ? $order_info['invoice_prefix'] . $order_info['invoice_no'] : '',
            'payment_method'   => $order_info['payment_method'],
            'shipping_method'  => $order_info['shipping_method']
        ];

        $json['addresses'] = [
            'shipping' => [
                'firstname'      => $order_info['shipping_firstname'],
                'lastname'       => $order_info['shipping_lastname'],
                'company'        => $order_info['shipping_company'],
                'address_1'      => $order_info['shipping_address_1'],
                'address_2'      => $order_info['shipping_address_2'],
                'city'          => $order_info['shipping_city'],
                'postcode'      => $order_info['shipping_postcode'],
                'zone'          => $order_info['shipping_zone'],
                'zone_code'     => $order_info['shipping_zone_code'],
                'country'       => $order_info['shipping_country']
            ]
        ];

        $json['totals'] = [];
        $totals = $this->model_extension_mobile_app_api_app->getTotals($order_id);

        foreach ($totals as $total) {
            $json['totals'][] = [
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
            ];
        }

        $json['history'] = [];
        $histories = $this->model_extension_mobile_app_api_app->getHistories($order_id);

        foreach ($histories as $history) {
            $json['history'][] = [
                'date_added' => $history['date_added'],
                'status'     => $history['status'],
                'comment'    => $history['comment']
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getAddresses(): void
    {
        $this->load->language('account/address');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true) ?: [];

        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
        } else {
            $this->load->model('account/address');

            $results = $this->model_account_address->getAddresses($customer_id);
            $json['addresses'] = [];

            foreach ($results as $result) {
                $find = [
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                ];

                $replace = [
                    'firstname' => $result['firstname'],
                    'lastname'  => $result['lastname'],
                    'company'   => $result['company'],
                    'address_1' => $result['address_1'],
                    'address_2' => $result['address_2'],
                    'city'      => $result['city'],
                    'postcode'  => $result['postcode'],
                    'zone'      => $result['zone'],
                    'zone_code' => $result['zone_code'],
                    'country'   => $result['country']
                ];

                $json['addresses'][] = [
                    'address_id' => $result['address_id'],
                    'address'    => str_replace(["\r\n", "\r", "\n"], '<br/>', preg_replace(["/\\s\\s+/", "/\r\r+/", "/\n\n+/"], '<br/>', trim(str_replace($find, $replace, $result['address_format']))))
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getAddress(): void
    {
        $this->load->language('extension/mobile_app/api/app');
        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        $address_id = isset($data['address_id']) ? (int)$data['address_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        $this->load->model('account/address');

        if ($address_id && $customer_id) {
            $address_info = $this->model_account_address->getAddress($customer_id, $address_id);

            if ($address_info && $address_info['customer_id'] == $customer_id) {
                $json['address'] = [
                    'address_id'     => $address_info['address_id'],
                    'firstname'      => $address_info['firstname'],
                    'lastname'       => $address_info['lastname'],
                    'company'        => $address_info['company'],
                    'address_1'      => $address_info['address_1'],
                    'address_2'      => $address_info['address_2'],
                    'postcode'       => $address_info['postcode'],
                    'city'           => $address_info['city'],
                    'zone_id'        => $address_info['zone_id'],
                    'country_id'     => $address_info['country_id'],
                ];
                $json['success'] = true;
            } else {
                $json['error'] = $this->language->get('error_address');
            }
        } else {
            $json['error'] = $this->language->get('error_address_id');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function editAddress(): void
    {
        $this->load->language('account/address');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true) ?: [];

        // Get customer ID from request
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
        } else {
            $required = [
                'firstname'  => '',
                'lastname'   => '',
                'address_1'  => '',
                'address_2'  => '',
                'city'       => '',
                'postcode'   => '',
                'country_id' => 0,
                'zone_id'    => 0
            ];

            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body, true) + $required;

            if (!oc_validate_length((string)$data['firstname'], 1, 32)) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if (!oc_validate_length((string)$data['lastname'], 1, 32)) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if (!oc_validate_length((string)$data['address_1'], 3, 128)) {
                $json['error']['address_1'] = $this->language->get('error_address_1');
            }

            if (!oc_validate_length((string)$data['city'], 2, 128)) {
                $json['error']['city'] = $this->language->get('error_city');
            }

            // Country
            $this->load->model('localisation/country');
            $country_info = $this->model_localisation_country->getCountry((int)$data['country_id']);

            if ($country_info && $country_info['postcode_required'] && !oc_validate_length((string)$data['postcode'], 2, 10)) {
                $json['error']['postcode'] = $this->language->get('error_postcode');
            }

            if (!$country_info) {
                $json['error']['country'] = $this->language->get('error_country');
            }

            // Zone
            $this->load->model('localisation/zone');
            $zone_total = $this->model_localisation_zone->getTotalZonesByCountryId((int)$data['country_id']);

            if ($zone_total && !$data['zone_id']) {
                $json['error']['zone'] = $this->language->get('error_zone');
            }

            if (!$json) {
                $this->load->model('account/address');

                // Add Address
                if (!isset($data['address_id'])) {
                    $json['address_id'] = $this->model_account_address->addAddress($customer_id, $data);
                    $json['success'] = $this->language->get('text_add');
                }

                // Edit Address
                if (isset($data['address_id'])) {
                    $this->model_account_address->editAddress($customer_id, (int)$data['address_id'], $data);
                    $json['success'] = $this->language->get('text_edit');
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteAddress(): void
    {
        $this->load->language('account/address');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true) ?: [];

        // Get customer ID and address ID from request
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        $address_id = isset($data['address_id']) ? (int)$data['address_id'] : 0;

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
        } elseif (!$address_id) {
            $json['error'] = 'Address ID is required';
        } else {

            // Load customer model to check default address
            $this->load->model('account/customer');

            $this->load->model('account/address');

            if ($this->model_account_address->getTotalAddresses($this->customer->getId()) == 1) {
                $json['error'] = $this->language->get('error_delete');
            }

            $this->load->model('account/subscription');
            $subscription_total = $this->model_account_subscription->getTotalSubscriptionByShippingAddressId($address_id);
            if ($subscription_total) {
                $json['error'] = sprintf($this->language->get('error_subscription'), $subscription_total);
            }

            $subscription_total = $this->model_account_subscription->getTotalSubscriptionByPaymentAddressId($address_id);
            if ($subscription_total) {
                $json['error'] = sprintf($this->language->get('error_subscription'), $subscription_total);
            }

            if (!$json) {
                $this->model_account_address->deleteAddress($this->customer->getId(), $address_id);
                $json['success'] = $this->language->get('text_delete');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCountries(): void
    {
        $this->load->model('localisation/country');

        $json = [];

        $results = $this->model_localisation_country->getCountries();

        $json['countries'] = [];

        foreach ($results as $result) {
            $json['countries'][] = [
                'country_id' => $result['country_id'],
                'name'       => $result['name'],
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getZones(): void
    {
        $this->load->model('localisation/zone');

        $json = [];

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true) ?: [];
        $country_id = isset($data['country_id']) ? (int)$data['country_id'] : 0;

        if (!$country_id) {
            $json['error'] = 'Country ID is required';
        } else {
            $results = $this->model_localisation_zone->getZonesByCountryId($country_id);

            $json['zones'] = [];

            foreach ($results as $result) {
                $json['zones'][] = [
                    'zone_id'    => $result['zone_id'],
                    'name'       => $result['name'],
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCurrencies(): void {
        $this->load->language('common/currency');
        $this->load->model('localisation/currency');
        
        $json = [];
        $json['currencies'] = [];
        
        $results = $this->model_localisation_currency->getCurrencies();
        
        foreach ($results as $result) {
            if ($result['status']) {
                $json['currencies'][$result['code']] = [
                    'title'        => $result['title'],
                    'code'         => $result['code'],
                    'symbol_left'  => $result['symbol_left'],
                    'symbol_right' => $result['symbol_right']
                ];
            }
        }
        
        $code = $this->session->data['currency'];
        
        $json['code'] = $code;
        $json['active_currency'] = [
            'title'        => $results[$code]['title'],
            'code'         => $code,
            'symbol_left'  => $results[$code]['symbol_left'],
            'symbol_right' => $results[$code]['symbol_right']
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function setCurrency(): void {
        $this->load->language('common/currency');
        
        $json = [];
        
        if (!isset($this->request->post['code'])) {
            $json['error'] = $this->language->get('error_currency');
        }
        
        $this->load->model('localisation/currency');
        
        $currency_info = $this->model_localisation_currency->getCurrencyByCode($this->request->post['code']);
        
        if (!$currency_info) {
            $json['error'] = $this->language->get('error_currency');
        }
        
        if (!$json) {
            $this->session->data['currency'] = $this->request->post['code'];
            
            // Clear shipping methods when currency is changed
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            
            // Set currency cookie
            $option = [
                'expires'  => time() + 60 * 60 * 24 * 30,
                'path'     => '/',
                'SameSite' => 'Lax'
            ];
            
            setcookie('currency', $this->session->data['currency'], $option);
            
            // Return updated currency info
            $json['success'] = true;
            $json['currency'] = [
                'code'         => $currency_info['code'],
                'title'        => $currency_info['title'],
                'symbol_left'  => $currency_info['symbol_left'],
                'symbol_right' => $currency_info['symbol_right']
            ];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
