<?php
class ControllerMpmultivendorStore extends Controller {
	public function index() {
		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('common/home', '', true));
		}

		$this->load->language('mpmultivendor/store');

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->model('mpmultivendor/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_mv_seller'),
			'href' => $this->url->link('mpmultivendor/mv_seller')
		);

		$data['text_no_result'] = $this->language->get('text_no_result');

		$data['entry_sort'] = $this->language->get('entry_sort');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_category'] = $this->language->get('entry_category');

		$data['text_all_category'] = $this->language->get('text_all_category');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('mpmultivendor_store_list_product');
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = (int)$this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if (!empty($seller_info['status']) && !empty($seller_info['approved'])) {
				$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $seller_info['store_name'],
				'href' => $this->url->link('mpmultivendor/store', 'mpseller_id='. $mpseller_id . $url, true)
			);

			$this->document->setTitle($seller_info['store_name']);
			$this->document->setDescription($seller_info['meta_description']);
			$this->document->setKeywords($seller_info['meta_keyword']);

			$filter_data = array(
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'filter_category_id' => $filter_category_id,
				'filter_mpseller_id' => $mpseller_id,
			);

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);

			$data['products'] = array();
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$data['sorts'] = array();
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('mpmultivendor_store_list_product'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['no_category_href'] = $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . $url, true);

			$data['product_categories'] = array();
			$search_filter_data = array(
				'filter_mpseller_id' => $mpseller_id,
			);
			$product_results = $this->model_mpmultivendor_category->getProducts($search_filter_data);
			foreach ($product_results as $product_result) {
				$product_categories = $this->model_mpmultivendor_category->getProductCategories($product_result['product_id']);
				foreach($product_categories as $category_id) {
					$category_info = $this->model_mpmultivendor_category->getCategory($category_id);
					if ($category_info) {
						$data['product_categories'][$category_id] = array(
							'category_id' 	=> $category_info['category_id'],
							'name' 			=> ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
							'href'  		=> $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . $url . '&filter_category_id=' . $category_info['category_id'])
						);
					}
				}
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('mpmultivendor/store', 'mpseller_id=' . $this->request->get['mpseller_id'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			if ($page == 1) {
			    $this->document->addLink($this->url->link('mpmultivendor/store', 'mpseller_id=' . $seller_info['mpseller_id'], true), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('mpmultivendor/store', 'mpseller_id=' . $seller_info['mpseller_id'], true), 'prev');
			} else {
			    $this->document->addLink($this->url->link('mpmultivendor/store', 'mpseller_id=' . $seller_info['mpseller_id'] . '&page='. ($page - 1), true), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('mpmultivendor/store', 'mpseller_id=' . $seller_info['mpseller_id'] . '&page='. ($page + 1), true), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
			$data['filter_category_id'] = $filter_category_id;

			$data['store_profile'] = $this->load->controller('mpmultivendor/store_profile');
			$data['store_banner'] = $this->load->controller('mpmultivendor/store_banner');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			/* Theme Work Starts */
			if($this->config->get('config_theme')) {
	     		$custom_themename = $this->config->get('config_theme');
	    	}
	    	if($this->config->get('theme_default_directory')) {
				$custom_themename = $this->config->get('theme_default_directory');
			}
			if($this->config->get('config_template')) {
				$custom_themename = $this->config->get('config_template');
			}
			// else{
			// 	$custom_themename = 'default';
			// }

			if (defined('JOURNAL3_ACTIVE')) {
				$custom_themename = 'journal3';
			}

			if(strpos($this->config->get('config_template'), 'journal2') === 0){
				$custom_themename = 'journal2';
			}

			if(empty($custom_themename)) {
				$custom_themename = 'default';
			}

			$data['custom_themename'] = $custom_themename;
			/* Theme Work Ends */

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/store.tpl')) {
			    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/mpmultivendor/store.tpl', $data));
			   } else {
			   		$this->response->setOutput($this->load->view('default/template/mpmultivendor/store.tpl', $data));
			   }
		  	} else {
			   $this->response->setOutput($this->load->view('mpmultivendor/store', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $mpseller_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}