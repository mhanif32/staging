<?php

class ControllerCommonMenu extends Controller
{
    public function index()
    {
        $this->load->language('common/menu');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {

            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);
                $data['subCategories'] = [];
                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id' => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $subCategories = $this->model_catalog_category->getCategories($child['category_id']);
                    $subCategoriesArray = array();
                    foreach ($subCategories as $subCategory) {
                        $subCategoriesArray[] = array(
                            'category_id' => $subCategory['category_id'],
                            'name' => $subCategory['name'],
                            'href' => $this->url->link('product/category', 'path=' . $subCategory['category_id'])
                        );
                    }
                    $children_data[] = array(
                        'name' => $child['name'],
                        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
                        'subCategories' => !empty($subCategoriesArray) ? $subCategoriesArray : []
                    );
                } 

                // Level 1
                $data['categories'][] = array(
                    'name' => $category['name'],
                    'children' => $children_data,
                    'column' => $category['column'] ? $category['column'] : 1,
                    'href' => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
                $data['sale_offer_link'] = $this->url->link('product/special');
                $data['more_category'] = $this->url->link('product/category/listing');
            }
        }

        //more categories
        $this->load->model('catalog/category');
        $data['moreCategories'] = array();
        $categories = $this->model_catalog_category->getMoreCategories(0);
        foreach ($categories as $category) {

            $data['subCategories'] = array();
            $subCategories = $this->model_catalog_category->getMoreCategories($category['category_id']);
            foreach ($subCategories as $subCategory) {
                $data['subCategories'][] = array(
                    'category_id' => $subCategory['category_id'],
                    'name' => $subCategory['name'],
                    'href' => $this->url->link('product/category', 'path=' . $subCategory['category_id'])
                );
            }

            $data['moreCategories'][] = array(
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'href' => $this->url->link('product/category', 'path=' . $category['category_id']),
                'subCategories' => $data['subCategories']
            );
        }
        return $this->load->view('common/menu', $data);
    }
}
