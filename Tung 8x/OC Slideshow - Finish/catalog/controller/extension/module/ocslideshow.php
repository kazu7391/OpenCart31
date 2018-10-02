<?php
class ControllerExtensionModuleOcslideshow extends Controller {
	public function index($setting) { 
		static $module = 0;	
		$this->language->load('extension/module/ocslideshow');
		$this->load->model('ocslideshow/slide');
		$this->load->model('tool/image');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/opentheme/ocslideshow/ocslideshow.css')) {
			$this->document->addStyle('catalog/view/theme/'.$this->config->get($this->config->get('config_theme') . '_directory').'/stylesheet/opentheme/ocslideshow/ocslideshow.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/ocslideshow/ocslideshow.css');
		}

		$this->document->addScript('catalog/view/javascript/opentheme/ocslideshow/jquery.nivo.slider.js');

		$data = array();
		$data['text_readmore'] = $this->language->get('text_readmore');
		$data['ocslideshows'] = array();
		$data['animate'] = 'animate-in';
		$results = array();
		if(isset($setting['banner'])) {
			$results = $this->model_ocslideshow_slide->getocslideshow($setting['banner']);
		}
		if($results ) {
			$store_id  = $this->config->get('config_store_id');
			foreach ($results as $result) {
			//	if (file_exists(DIR_IMAGE . $result['image'])) {
			  $banner_store = array();
			  if(isset($result['banner_store'])) {
				$banner_store = explode(',',$result['banner_store']);
			  }
			  if(in_array($store_id,$banner_store)) {
					$data['ocslideshows'][] = array(
						'title' => $result['title'],
						'sub_title' => $result['sub_title'],
						'description' => $result['description'],
						'link'  => $result['link'],
						'type'  => $result['type'],
						'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
						'small_image' => $this->config->get('config_url'). 'image/' . $result['small_image'],
						'small_image_name' => $result['small_image']
					);
			 }
					
				//}
				
				$data['slide_setting'] = $this->model_ocslideshow_slide->getSettingSlide($result['ocslideshow_id']);
			}
			
					

			return $this->load->view('extension/module/ocslideshow', $data);

		
		}
		
		
	}
}