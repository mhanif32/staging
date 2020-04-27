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
        $data['sellers_href'] = $this->url->link('mpmultivendor/mv_seller', '', true);

		//men count and path
        $countMen = $this->model_catalog_product->getCountMen();
        $data['count_men'] = $countMen['total'];

        $categoryMen = $this->model_catalog_product->getCategoryMen();
        $data['path_men'] = !empty($categoryMen['total']) ? $this->url->link('product/category', 'path=' . $categoryMen['path']) : '#';
        if ($categoryMen['image']) {
            $data['men_thumb'] = $this->model_tool_image->resize($categoryMen['image'], 250, 177);
        } else {
            $data['men_thumb'] = $this->model_tool_image->resize('placeholder.png', 250, 177);;
        }

        //women count and path
        $countWomen = $this->model_catalog_product->getCountWomen();
        $data['count_women'] = $countWomen['total'];

        $categoryWomen = $this->model_catalog_product->getCategoryWomen();
        $data['path_women'] = !empty($categoryWomen['total']) ? $this->url->link('product/category', 'path=' . $categoryWomen['path']) : '#';
        if ($categoryWomen['image']) {
            $data['women_thumb'] = $this->model_tool_image->resize($categoryWomen['image'], 250, 177);
        } else {
            $data['women_thumb'] = $this->model_tool_image->resize('placeholder.png', 250, 177);;
        }

        //brand category and path
        $categoryDesignerBrands = $this->model_catalog_product->getCountDesignerBrands();
        $data['count_brands'] = $categoryDesignerBrands['total'];
        $data['path_brands'] = !empty($categoryDesignerBrands['total']) ? $this->url->link('product/category', 'path=' . $categoryDesignerBrands['path']) : '#';
        if ($categoryDesignerBrands['image']) {
            $data['brand_thumb'] = $this->model_tool_image->resize($categoryDesignerBrands['image'], 250, 177);
        } else {
            $data['brand_thumb'] = $this->model_tool_image->resize('placeholder.png', 250, 177);;
        }

        $this->load->model('design/banner');
        $retailerBanner = $this->model_design_banner->getBannerSingle(9);
        $data['RetailerBanners'] = array(
            'title' => $retailerBanner['title'],
            'link'  => $retailerBanner['link'],
            'image' => $this->model_tool_image->resize($retailerBanner['image'], 250, 177)
        );
		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}