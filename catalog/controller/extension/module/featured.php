<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => (int) $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

        $data['count_retailers'] = $this->model_catalog_product->getRetailersCount();

		//men count and path
        $categoryMen = $this->model_catalog_product->getCountMen();
        $data['count_men'] = $categoryMen['total'];
        $data['path_men'] = !empty($categoryMen['total']) ? $this->url->link('product/category', 'path=' . $categoryMen['path']) : '#';

        //women count and path
        $categoryWomen = $this->model_catalog_product->getCountWomen();
        $data['count_women'] = $categoryWomen['total'];
        $data['path_women'] = !empty($categoryWomen['total']) ? $this->url->link('product/category', 'path=' . $categoryWomen['path']) : '#';

        //brand category and path
        $categoryDesignerBrands = $this->model_catalog_product->getCountDesignerBrands();
        $data['count_brands'] = $categoryDesignerBrands['total'];
        $data['path_brands'] = !empty($categoryDesignerBrands['total']) ? $this->url->link('product/category', 'path=' . $categoryDesignerBrands['path']) : '#';

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}