<?php
class ControllerExtensionModuleSlideshow extends Controller {
	public function index($setting) {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

		static $module = 0;		

		$this->load->model('design/banner');
		$this->load->model('tool/image');
//		top message
        $this->load->model('catalog/information');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

		
		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;
        $data['search_by_nigeria'] = $this->url->link('product/search', '&location=Nigeria');
        $data['sale_href'] = $this->url->link('product/special', '', true);
        $data['information_href'] = $this->url->link('information/information&information_id=7', '', true);

        $leftSlideMsg = $this->model_catalog_information->getInfoMessage(2);
        $data['leftMsgTitle'] = $leftSlideMsg['title'];
        $data['leftMsgDesc'] = html_entity_decode($leftSlideMsg['description'], ENT_QUOTES, 'UTF-8');

        //Retailers
        $this->load->model('catalog/product');
        $data['count_retailers'] = $this->model_catalog_product->getRetailersCount();
        $data['sellers_href'] = $this->url->link('mpmultivendor/mv_seller', '', true);
        //Start : men count and path
        $this->load->model('catalog/product');
        $countMen = $this->model_catalog_product->getCountMen();
        $data['count_men'] = $countMen['total'];

        $categoryMen = $this->model_catalog_product->getCategoryMen();
        $data['path_men'] = $this->url->link('product/category', 'path=' . $categoryMen['path']);
        if ($categoryMen['image']) {
            $data['men_thumb'] = $this->model_tool_image->resize($categoryMen['image'], 250, 177);
        } else {
            $data['men_thumb'] = $this->model_tool_image->resize('placeholder.png', 250, 177);;
        }

        $this->load->model('catalog/information');
        //women count and path
        $countWomen = $this->model_catalog_product->getCountWomen();
        $data['count_women'] = $countWomen['total'];

        $categoryWomen = $this->model_catalog_product->getCategoryWomen();
        $data['path_women'] = $this->url->link('product/category', 'path=' . $categoryWomen['path']);
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

        return $this->load->view('extension/module/slideshow', $data);


	}
}