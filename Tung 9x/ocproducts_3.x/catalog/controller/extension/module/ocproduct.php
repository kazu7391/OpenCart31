<?php
class ControllerExtensionModuleOcProduct extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/ocproduct');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['text_label_new'] = $this->language->get('text_label_new');
		$data['text_label_sale'] = $this->language->get('text_label_sale');
		
		$this->document->addScript('catalog/view/javascript/jquery.plugin.js');
		$this->document->addScript('catalog/view/javascript/jquery.countdown.js');
		$this->document->addStyle('catalog/view/javascript/jquery.countdown.css');

		$this->load->model('catalog/product');
		$this->load->model('extension/module/ocproduct');

		$this->load->model('tool/image');

		$data['products'] = array();

		$this->load->model('localisation/language');
		
		$data['code'] = $this->session->data['language'];

		$store_id = $this->config->get('config_store_id');
		
		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		$results = array();

		if($setting['option'] == 0) {
			if (!empty($setting['product'])) {
				$results = array();
				$products = array_slice($setting['product'], 0, (int)$setting['limit']);
				foreach ($products as $product_id) {
					$results[] = $this->model_catalog_product->getProduct($product_id);
				}
			}
			
		} else if ($setting['option']==1){
			if($setting['productfrom']==1){
				$data['filter_category_id'] = $setting['cate_id'];
				$results = $this->model_catalog_product->getProducts($data);
				
			} else if($setting['productfrom']==0) {
				if (!empty($setting['productcate'])) {
					$products = array_slice($setting['productcate'], 0, (int)$setting['limit']);
					foreach ($products as $product_id) {
						$results[] = $this->model_catalog_product->getProduct($product_id);
					}
				}			
			} else {
				if ($setting['input_specific_product']==0){
					$data['products'] = array();
					$filter_data = array(
						'filter_category_id' => $setting['cate_id'],
						'sort'  => 'p.date_added',
						'order' => 'DESC',
						'start' => 0,
						'limit' => $setting['limit'],
					);
					$results = $this->model_catalog_product->getProducts($filter_data);
						
				} else if ($setting['input_specific_product']==1){
					$filter_data = array(
					'sort'  => 'pd.name',
					'order' => 'ASC',
					'start' => 0,
					'limit' => $setting['limit']
					);
					$results = $this->model_module_ocproduct->getProductSpecialsCategory($filter_data, $setting['cate_id']);		
				} else if ($setting['input_specific_product']==2){
					$data['products'] = array();
					$results = $this->model_module_ocproduct->getBestSellerProductsCategory($setting['limit'], $setting['cate_id']);				
				} else{
					$data['products'] = array();
					$results = $this->model_module_ocproduct->getMostViewedProductsCategory($setting['limit'],  $setting['cate_id']);		
				}
			}
	
		} else {
			if ($setting['autoproduct']==0){
				$data['products'] = array();

				$filter_data = array(
					'sort'  => 'p.date_added',
					'order' => 'DESC',
					'start' => 0,
					'limit' => $setting['limit']
				);
				$results = $this->model_catalog_product->getProducts($filter_data);
					
			} else if ($setting['autoproduct']==1){
				$filter_data = array(
				'sort'  => 'pd.name',
				'order' => 'ASC',
				'start' => 0,
				'limit' => $setting['limit']
				);

				$results = $this->model_catalog_product->getProductSpecials($filter_data);
					
			} else if ($setting['autoproduct']==2){
					$data['products'] = array();

				$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
				
			} else{
				$data['products'] = array();

				$results = $this->model_catalog_product->getPopularProducts($setting['limit']);		
				
			}		
		}

		$data['use_quickview'] = (int) $this->config->get('module_octhemeoption_quickview')[$store_id];
		$data['use_catalog'] = (int) $this->config->get('module_octhemeoption_catalog')[$store_id];

		$product_rotator_status = (int) $this->config->get('module_octhemeoption_rotator')[$store_id];

		/* Get new product */
		$this->load->model('catalog/product');

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10
		);

		$new_results = $this->model_catalog_product->getProducts($filter_data);
		/* End */

		if ($results) {
			$f_products = $this->getFirstProduts($results);

			$e_f_products = $this->getOtherExcpFirstProducts($results);

			if($f_products) {
				foreach ($f_products as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					$date_end = false;
					if ($setting['countdown']){
						$date_end = $this->model_module_ocproduct->getSpecialCountdown($result['product_id']);
						if ($date_end === '0000-00-00') {
							$date_end = false;
						}
					}
					/* Product Rotator */
					if($product_rotator_status == 1) {
					  $this->load->model('catalog/ocproductrotator');
					  $this->load->model('tool/image');

					  $product_id = $result['product_id'];
					  $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

					  if($product_rotator_image) {
					    $rotator_image_width = $setting['width'];
					    $rotator_image_height = $setting['height'];
					    $data['rotator_image'] = $this->model_tool_image->resize($product_rotator_image, $rotator_image_width, $rotator_image_height);  
					  } else {
					    $data['rotator_image'] = false;
					  } 
					} else {
					  $data['rotator_image'] = false;       
					}
					/* End Product Rotator */

					$is_new = false;
					if ($new_results) { 
						foreach($new_results as $new_r) {
							if($result['product_id'] == $new_r['product_id']) {
								$is_new = true;
							}
						}
					}
					$data['frsProducts'][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						'name'        => $result['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
						'date_end'    => $date_end,
						'is_new'      => $is_new,
						'rotator_image' => $data['rotator_image'],
					);
				}
			}

			if($e_f_products) {
				foreach ($e_f_products as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					$date_end = false;
					if ($setting['countdown']){
						$date_end = $this->model_module_ocproduct->getSpecialCountdown($result['product_id']);
						if ($date_end === '0000-00-00') {
							$date_end = false;
						}
					}
					/* Product Rotator */
					if($product_rotator_status == 1) {
					  $this->load->model('catalog/ocproductrotator');
					  $this->load->model('tool/image');

					  $product_id = $result['product_id'];
					  $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

					  if($product_rotator_image) {
					    $rotator_image_width = $setting['width'];
					    $rotator_image_height = $setting['height'];
					    $data['rotator_image'] = $this->model_tool_image->resize($product_rotator_image, $rotator_image_width, $rotator_image_height);  
					  } else {
					    $data['rotator_image'] = false;
					  } 
					} else {
					  $data['rotator_image'] = false;       
					}
					/* End Product Rotator */

					$is_new = false;
					if ($new_results) { 
						foreach($new_results as $new_r) {
							if($result['product_id'] == $new_r['product_id']) {
								$is_new = true;
							}
						}
					}
					$data['excpFrsProducts'][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						'name'        => $result['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
						'date_end'    => $date_end,
						'is_new'      => $is_new,
						'rotator_image' => $data['rotator_image'],
					);
				}
			}

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				$date_end = false;
				if ($setting['countdown']){
					$date_end = $this->model_module_ocproduct->getSpecialCountdown($result['product_id']);
					if ($date_end === '0000-00-00') {
						$date_end = false;
					}
				}
				/* Product Rotator */
				if($product_rotator_status == 1) {
				  $this->load->model('catalog/ocproductrotator');
				  $this->load->model('tool/image');

				  $product_id = $result['product_id'];
				  $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

				  if($product_rotator_image) {
				    $rotator_image_width = $setting['width'];
				    $rotator_image_height = $setting['height'];
				    $data['rotator_image'] = $this->model_tool_image->resize($product_rotator_image, $rotator_image_width, $rotator_image_height);  
				  } else {
				    $data['rotator_image'] = false;
				  } 
				} else {
				  $data['rotator_image'] = false;       
				}
				/* End Product Rotator */

				$is_new = false;
				if ($new_results) { 
					foreach($new_results as $new_r) {
						if($result['product_id'] == $new_r['product_id']) {
							$is_new = true;
						}
					}
				}
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'date_end'    => $date_end,
					'is_new'      => $is_new,
					'rotator_image' => $data['rotator_image'],
				);
			}
		}

		$number_random = rand ( 1 , 1000 );
		$data['config_module'] = array(
				'name' => $setting['name'],
				'type' => $setting['type'],
				'slider' => $setting['slider'],
				'auto' => $setting['auto'],
				'loop' => $setting['loop'],
				'margin' => $setting['margin'],
				'nrow' => $setting['nrow'],
				'items' => $setting['items'],
				'time' => $setting['time'],
				'speed' => $setting['speed'],
				'row' => $setting['row'],
				'navigation' => $setting['navigation'],
				'pagination' => $setting['pagination'],
				'desktop' => $setting['desktop'],
				'tablet' => $setting['tablet'],
				'mobile' => $setting['mobile'],
				'smobile' => $setting['smobile'],
				'title_lang' => $setting['title_lang'],
				'description' => $setting['description'],
				'countdown' => $setting['countdown'],
				'rotator'  => $setting['rotator'],
				'newlabel'  => $setting['newlabel'],
				'salelabel'  => $setting['salelabel'],
				'module_id' => $number_random
			);
			if (isset($setting['module_description'][$this->session->data['language']])) {
				$data['module_description'] = html_entity_decode($setting['module_description'][$this->session->data['language']]['description'], ENT_QUOTES, 'UTF-8');
				if ($data['module_description'] = '<p><br><p>') $data['module_description']= '';
			}
			//echo '<pre>'; print_r($data['config_module']); die;

		if ($data['products']) {
			return $this->load->view('extension/module/ocproduct', $data);
		}
		
	}

	public function getFirstProduts($products) {
		$trdProduct = array();
		$count = 0;
		foreach($products as $product) {
			if($count < 1) {
				$product_id = $product['product_id'];
				$trdProduct[] = $product;
			}
			$count++;
		}
		
		return $trdProduct;
	}
	
	public function getOtherExcpFirstProducts($products) {
		$excpTrdProducts = array();
		
		$count = 0;
		foreach($products as $product) {
			if($count >= 1) {
				$excpTrdProducts[] = $product;
			}
			$count++;
		}
		
		return $excpTrdProducts;
	}
	
	
}