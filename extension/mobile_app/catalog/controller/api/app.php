<?php

namespace Opencart\Catalog\Controller\Extension\MobileApp\Api;

/**
 * Class App
 *
 * @package Opencart\Catalog\Controller\Extension\MobileApp\Api
 */


class App extends \Opencart\System\Engine\Controller
{

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
            $banner_images = $this->config->get('module_mobile_app_banner_image') ?? [];
            foreach ($banner_images as $banner) {
                if (!empty($banner['image'])) {
                    $image_path = $this->model_tool_image->resize($banner['image'], 300, 300);
                    $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
                } else {
                    $image_url = '';
                }
                $json['banner'][] = [
                    'title' => $banner['title'] ?? '',
                    'link' => $banner['link'] ?? '',
                    'image' => $image_url,
                    'sort_order' => $banner['sort_order'] ?? ''
                ];
            }
        }

        // Deal
        $json['deal'] = null;
        if ($this->config->get('module_mobile_app_deal_status') == '1') {
            $deal = [];
            $deal['end_date'] = $this->config->get('module_mobile_app_deal_end_date') ?? '';
            $deal['products'] = [];
            $product_ids = $this->config->get('module_mobile_app_deal_product') ?? [];
            $product_discounts = $this->config->get('module_mobile_app_deal_product_discount') ?? [];
            foreach ($product_ids as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);
                if ($product_info) {
                    $discount_value = isset($product_discounts[$product_id]) ? (float)$product_discounts[$product_id] : 0;
                    // Product image
                    if ($product_info['image']) {
                        $prod_image_path = $this->model_tool_image->resize($product_info['image'], 250, 250);
                        $prod_image_url = (strpos($prod_image_path, 'http') === 0) ? $prod_image_path : $server . ltrim($prod_image_path, '/');
                    } else {
                        $prod_image_url = '';
                    }
                    $deal['products'][] = [
                        'product_id' => $product_info['product_id'],
                        'name' => $product_info['name'],
                        'image' => $prod_image_url,
                        'price' => $this->currency->format($product_info['price'], $this->session->data['currency'] ?? $this->config->get('config_currency')),
                        'special' => $product_info['special'] ? $this->currency->format($product_info['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null,
                        'discount' => $discount_value
                    ];
                }
            }
            $json['deal'] = $deal;
        }

        // Feature Category
        $json['feature_category'] = [];
        if ($this->config->get('module_mobile_app_feature_category_status') == '1') {
            $feature_categories = $this->config->get('module_mobile_app_feature_category_items') ?? [];
            foreach ($feature_categories as $item) {
                if (empty($item['category_id'])) continue;
                $category_info = $this->model_catalog_category->getCategory($item['category_id']);
                if ($category_info) {
                    // Category image
                    if ($category_info['image']) {
                        $cat_image_path = $this->model_tool_image->resize($category_info['image'], 100, 100);
                        $cat_image_url = (strpos($cat_image_path, 'http') === 0) ? $cat_image_path : $server . ltrim($cat_image_path, '/');
                    } else {
                        $cat_image_url = '';
                    }
                    $products = [];
                    if (!empty($item['product'])) {
                        foreach ($item['product'] as $product_id) {
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            if ($product_info) {
                                // Product image
                                if ($product_info['image']) {
                                    $prod_image_path = $this->model_tool_image->resize($product_info['image'], 250, 250);
                                    $prod_image_url = (strpos($prod_image_path, 'http') === 0) ? $prod_image_path : $server . ltrim($prod_image_path, '/');
                                } else {
                                    $prod_image_url = '';
                                }
                                $products[] = [
                                    'name' => $product_info['name'],
                                    'image' => $prod_image_url,
                                    'price' => $this->currency->format($product_info['price'], $this->session->data['currency'] ?? $this->config->get('config_currency')),
                                    'special' => $product_info['special'] ? $this->currency->format($product_info['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null
                                ];
                            }
                        }
                    }
                    $json['feature_category'][] = [
                        'category_id' => $category_info['category_id'],
                        'name' => $category_info['name'],
                        'image' => $cat_image_url,
                        'products' => $products
                    ];
                }
            }
        }

        // Trust Badges
        $json['trust_badges'] = [];
        if ($this->config->get('module_mobile_app_trust_badges_status') == '1') {
            $trust_badges = $this->config->get('module_mobile_app_trust_badges_items') ?? [];
            foreach ($trust_badges as $item) {
                if (isset($item['image']) && $item['image']) {
                    $image_path = $this->model_tool_image->resize($item['image'], 100, 100);
                    $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
                } else {
                    $image_url = '';
                }
                $json['trust_badges'][] = [
                    'image' => $image_url,
                    'title' => $item['title'] ?? '',
                    'short_description' => $item['short_description'] ?? ''
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
        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $json['products'] = [];
        foreach ($products as $product) {
            if ($product['image']) {
                $image_path = $this->model_tool_image->resize($product['image'], 250, 250);
                $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
            } else {
                $image_url = '';
            }
            $json['products'][] = [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'image' => $image_url,
                'price' => $this->currency->format($product['price'], $this->session->data['currency'] ?? $this->config->get('config_currency')),
                'special' => $product['special'] ? $this->currency->format($product['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null
            ];
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
                    'name' => $cat_info['name']
                ];
            }
        }

        $json['id'] = $product_info['product_id'];
        $json['name'] = $product_info['name'];
        $json['model'] = $product_info['model'];
        $json['reward'] = $product_info['reward'];
        $json['points'] = $product_info['points'];
        $json['description'] = html_entity_decode($product_info['description'] ?? '', ENT_QUOTES, 'UTF-8');
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
                        'name' => $option_value['name'],
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
                'name' => $option['name'],
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
                    $json['related'][] = [
                        'id' => $related_info['product_id'],
                        'name' => $related_info['name']
                    ];
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
        // Get category_id from GET query string
        $category_id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;

        // Get category info
        $category_info = $this->model_catalog_category->getCategory($category_id);
        if (!$category_info) {
            $json['error'] = 'Category not found';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Description (HTML decoded)
        $json['description'] = html_entity_decode($category_info['description'] ?? '', ENT_QUOTES, 'UTF-8');

        // Subcategories
        $subcategories = $this->model_catalog_category->getCategories($category_id);
        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $json['subcategories'] = [];
        foreach ($subcategories as $subcategory) {
            if ($subcategory['image']) {
                $image_path = $this->model_tool_image->resize($subcategory['image'], 100, 100);
                $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
            } else {
                $image_url = '';
            }
            $json['subcategories'][] = [
                'id' => $subcategory['category_id'],
                'name' => $subcategory['name'],
                'image' => $image_url
            ];
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
            if ($product['image']) {
                $image_path = $this->model_tool_image->resize($product['image'], 250, 250);
                $image_url = (strpos($image_path, 'http') === 0) ? $image_path : $server . ltrim($image_path, '/');
            } else {
                $image_url = '';
            }
            $json['products'][] = [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'image' => $image_url,
                'price' => $this->currency->format($product['price'], $this->session->data['currency'] ?? $this->config->get('config_currency')),
                'special' => $product['special'] ? $this->currency->format($product['special'], $this->session->data['currency'] ?? $this->config->get('config_currency')) : null
            ];
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
        $server = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        foreach ($categories as $category) {
            if ($category['image']) {
                $image_path = $this->model_tool_image->resize($category['image'], 100, 100);
                // If resize returns a relative path, make it absolute
                if (strpos($image_path, 'http') === 0) {
                    $image_url = $image_path;
                } else {
                    $image_url = $server . ltrim($image_path, '/');
                }
            } else {
                $image_url = '';
            }
            $result[] = [
                'id' => $category['category_id'],
                'name' => $category['name'],
                'image' => $image_url
            ];
        }
        $json['categories'] = $result;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
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
            }
        }

        // Add products to cart
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                $product_id = (int)($item['product_id'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 1);
                if ($product_id > 0 && $quantity > 0) {
                    $this->cart->add($product_id, $quantity);
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
        $json['message'] = 'Session prepared. Use this session id as PHPSESSID cookie in webview.';

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
            $json['error']['currentPassword'] = $this->language->get('error_password');
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
}
