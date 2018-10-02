<?php
class ModelOcslideshowSlide extends Model {
	public function getocslideshow($banner_id) {
		$select ="SELECT * FROM " . DB_PREFIX . "ocslideshow_image bi LEFT JOIN " . DB_PREFIX . "ocslideshow_image_description bid ON (bi.ocslideshow_image_id  = bid.ocslideshow_image_id) WHERE bi.ocslideshow_id = '" . (int)$banner_id . "' AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($select);
		
		return $query->rows;
	}
	
	public function getSettingSlide($banner_id) {
		$query = "SELECT * FROM " . DB_PREFIX .  "ocslideshow WHERE ocslideshow_id = ". (int) $banner_id; 
		$result = $this->db->query($query);
		return $result->rows;
	}
}