<?php
class ControllerCommonCategory extends Controller
{
    public function index()
    {
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

        $this->load->language('product/category');

        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $data['categories'] = array();
        $categories = $this->model_catalog_category->getCategories(0);
        foreach ($categories as $category) {
            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name'        => $category['name'],
                'image'        => $this->model_tool_image->resize($category['image'], 100, 100),
                'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
        }

        return $this->load->view('common/category', $data);
    }
}