<?php
class ControllerExtensionModuleOchozmegamenu extends Controller {
	public function index($setting) { 
		$this->language->load('extension/module/ochozmegamenu');

		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$this->load->model('hozmegamenu/menu');

		$data = array();

		$data['mobile_menu'] = $setting['mobile'];
		$data['sticky_menu'] = $setting['sticky'];

      	$data['heading_title'] = $this->language->get('heading_title');
		$data['category_title'] = $this->language->get('category_title');
		$mobile = $this->model_hozmegamenu_menu->getblockCategTree();
		$html  = null;
		$html .='<ul id="ma-mobilemenu" class="mobilemenu nav-collapse collapse">';

			foreach($mobile['children'] as $m) {
				//echo "<pre>"; print_r($m); echo "</pre>";
				if(!isset($m["name"])) $m["name"] = 'Root';
				//echo "<pre>"; print_r($m); echo "</pre>";
				$child_class = '';
				if(count($m['children'])>0) { $child_class = 'collapse1';} else {
					$child_class = 'no-close';
				}

				$html .='<li><span class=" button-view1 '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$m['id']).'">'.$m["name"].'</a></span>';

						if(isset($m['children'])) {
							//echo "<pre>"; print_r($m); echo "</pre>";
							$sub1 = $m['children'] ;
							$html .='<ul class="level2">';
								if(isset($sub1)) {
									foreach($sub1 as $child1) {
										if(count($child1['children'])>0) { $child_class = 'collapse1';} else {
											$child_class = 'no-close';
										}
										$html .='<li><span class="button-view2   '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$child1['id']).'">'.$child1["name"].'</a></span>';
										if(isset($child1['children'])) {
										  $html .='<ul class="level3">';

											$sub2 = $child1['children'] ;
											foreach($sub2 as $child2) {
												if(count($child2['children'])>0) { $child_class = 'collapse1';} else {
													$child_class = 'no-close';
												}
												$html .='<li><span class="  '.$child_class.'"><a href="'. $this->url->link('product/category', "path=".$child2['id']).'">'.$child2["name"].'</a></span></li>';
											}
										  $html .='</ul>';

										}
										$html .='</li>';
									}
								}
							$html .='</ul>';

						}
				$html .='</li>';
			}
			
		$html .='</ul>'	;
		
		$lang_id = (int)$this->config->get('config_language_id');
		$top_menu = $this->model_hozmegamenu_menu->getMenuCustomerLink($lang_id,$setting) ;
		if($data['mobile_menu']) {
			$mobile_menu = $this->model_hozmegamenu_menu->getMenuCustomerLinkMobile($lang_id,$setting) ;
		}else{
			$mobile_menu = $html;
		}
		$data['hozmegamenu'] = $top_menu;
		$data['top_offset'] = 70; 
		$data['effect'] = 0; 
		$data['_menu'] = $mobile_menu; 

		
		$this->document->addScript('catalog/view/javascript/opentheme/hozmegamenu/custommenu.js');
		$this->document->addScript('catalog/view/javascript/opentheme/hozmegamenu/mobile_menu.js');
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/stylesheet/opentheme/hozmegamenu/css/custommenu.css')) {
			$this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/opentheme/hozmegamenu/css/custommenu.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/hozmegamenu/css/custommenu.css');
		}

		return $this->load->view('extension/module/ochozmegamenu', $data);
		
	}
}
?>