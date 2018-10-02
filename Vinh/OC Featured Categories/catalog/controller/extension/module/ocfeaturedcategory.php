<?php
class ControllerExtensionModuleOcfeaturedcategory extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/ocfeaturedcategory');

		$this->load->model('extension/module/ocfeaturedcategory');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		if(isset($setting['limit'])) {
			$limit = $setting['limit'];
		} else {
			$limit = 10;
		}

		if(isset($setting['showsubnumber'])) {
			$number_sub = (int) $setting['showsubnumber'];
		} else {
			$number_sub = 4;
		}

		if(isset($setting['slider']) && $setting['slider']) {
			$use_slider = true;
		} else {
			$use_slider = false;
		}

		if(isset($setting['showdes']) && $setting['showdes']) {
			$show_des = true;
		} else {
			$show_des = false;
		}

		if(isset($setting['showsub']) && $setting['showsub']) {
			$show_sub = true;
		} else {
			$show_sub = false;
		}

		$_featured_categories = $this->model_extension_module_ocfeaturedcategory->getFeaturedCategories($limit);
		$data['categories'] = array();
		
		if ($_featured_categories) {
			foreach ($_featured_categories as $_category) {
				$sub_categories = array();

				$sub_data_categories = $this->model_catalog_category->getCategories($_category['category_id']);

				foreach($sub_data_categories as $sub_category) {
					$filter_data = array('filter_category_id' => $sub_category['category_id'], 'filter_sub_category' => true);

					$sub_categories[] = array(
						'category_id' => $sub_category['category_id'],
						'name' => $sub_category['name'],
						'href' => $this->url->link('product/category', 'path='. $sub_category['category_id'])
					);
				}

				if ($_category['homethumb_image']) {
					$homethumb_image = $this->model_tool_image->resize($_category['homethumb_image'], $setting['width'], $setting['height']);
				} else {
					$homethumb_image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				$data['categories'][] = array(
					'children'			=> $sub_categories,
					'category_id'  		=> $_category['category_id'],
					'homethumb_image'   => $homethumb_image,
					'name'        		=> $_category['name'],
					'description' 		=> utf8_substr(strip_tags(html_entity_decode($_category['description'], ENT_QUOTES, 'UTF-8')), 0, 80) . 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod',
					'href'        		=> $this->url->link('product/category', 'path=' . $_category['category_id']),
				);
			}
		}

		if(isset($setting['rows'])) {
			$rows = (int) $setting['rows'];
		} else {
			$rows = 1;
		}

		$data['config_slide'] = array(
			'number_sub' => $number_sub,
			'use_slider' => $use_slider,
			'show_sub_category' => $show_sub,
			'show_description' => $show_des,
			'items' => $setting['item'],
			'autoplay' => $setting['autoplay'],
			'f_show_nextback' => $setting['shownextback'],
			'f_show_ctr' => $setting['shownav'],
			'f_speed' => $setting['speed'],
			'f_rows' => $rows
		);

		if ($data['categories']) {
			return $this->load->view('extension/module/ocfeaturedcategory', $data);
		}
	}
}