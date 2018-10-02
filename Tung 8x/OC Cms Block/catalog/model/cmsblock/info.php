<?php
class ModelCmsblockInfo extends Model {

	public function addcmsblock($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock SET status = '" . $this->db->escape($data['status']) . "',sort_order = '" . $this->db->escape($data['sort_order']) . "',identify = '" . $this->db->escape($data['identify']) . "',link = '" . $this->db->escape($data['link']) . "',type = '" . $this->db->escape($data['type']) . "'");
		$cmsblock_id = $this->db->getLastId();
		
		foreach ($data['cmsblock_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock_description SET cmsblock_id = '" . (int)$cmsblock_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock_to_store SET cmsblock_id = '" . (int)$cmsblock_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		$this->cache->delete('cmsblock');
		
	}
	
	public function editCmsblock($cmsblock_id, $data) {
		
			$this->db->query("UPDATE " . DB_PREFIX . "cmsblock SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "',identify = '" . $this->db->escape($data['identify']) . "',link = '" . $this->db->escape($data['link']) . "',type = '" . $this->db->escape($data['type']) . "' WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
	
			$this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock_description WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
						
			foreach ($data['cmsblock_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock_description SET cmsblock_id = '" . (int)$cmsblock_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
			}

			$this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock_to_store WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
			
			if (isset($data['information_store'])) {
				foreach ($data['information_store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock_to_store SET cmsblock_id = '" . (int)$cmsblock_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
			
			// $this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock_to_layout WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");

			// if (isset($data['cmsblock_layout'])) {
				// foreach ($data['cmsblock_layout'] as $store_id => $layout) {
					// if ($layout['layout_id']) {
						// $this->db->query("INSERT INTO " . DB_PREFIX . "cmsblock_to_layout SET cmsblock_id = '" . (int)$cmsblock_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					// }
				// }
			// }
					
			
			$this->cache->delete('cmsblock');
		
	}
	
	public function deletecmsblock($cmsblock_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock_description WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "cmsblock_to_store WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");

		$this->cache->delete('cmsblock');
	}
	
	public function getCmsBlockInfo($cmsblock_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cmsblock WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");
		
		return $query->row;
	}

	
	public function getcmsblockDescriptions($cmsblock_id) {
		$information_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock_description WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");

		foreach ($query->rows as $result) {
			$information_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}
		
		return $information_description_data;
	}
	
	
		
	public function getcmsblocks($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' i.`status` = 1 ";
		
			$sort_data = array(
				'id.title',
				'i.sort_order'
			);		
		
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY id.title";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}		

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$information_data = $this->cache->get('cmsblock.' . (int)$this->config->get('config_language_id'));

			if (!$information_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' and i.`status` = 1  ORDER BY id.title");
	
				$information_data = $query->rows;
			
				$this->cache->set('cmsblock.' . (int)$this->config->get('config_language_id'), $information_data);
			}	
	
			return $information_data;			
		}
	}
	
	public function getCmsblockStores($cmsblock_id) {
		$information_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock_to_store WHERE cmsblock_id = '" . (int)$cmsblock_id . "'");

		foreach ($query->rows as $result) {
			$information_store_data[] = $result['store_id'];
		}
		
		return $information_store_data;
	}

		
	public function getTotalcmsblocks() { 
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cmsblock");
		
		return $query->row['total'];
	}

	public function createTable(){
			$sql = " SHOW TABLES LIKE '".DB_PREFIX."cmsblock'";
			$query = $this->db->query( $sql );
			if( count($query->rows) >0 ){
				return true;
			}
			$sql = array();
			$sql[] = "
					CREATE TABLE IF NOT EXISTS `".DB_PREFIX."cmsblock` (
						  `cmsblock_id` int(11) NOT NULL AUTO_INCREMENT,
						  `status` tinyint(1) NOT NULL,
						  `auto` tinyint(1) DEFAULT NULL,
						  PRIMARY KEY (`cmsblock_id`)
						) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
			";

			$sql[] = "
					CREATE TABLE IF NOT EXISTS `".DB_PREFIX."cmsblock_description` (
					  `cmsblock_des_id` int(11) NOT NULL,
					  `language_id` int(11) NOT NULL,
					  `cmsblock_id` int(11) NOT NULL,
					  `title` varchar(64) NOT NULL,
					  `sub_title` varchar(64) DEFAULT NULL,
					  `description` text,
					  PRIMARY KEY (`cmsblock_des_id`,`language_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			";
			
			foreach( $sql as $q ){
				$query = $this->db->query( $q );
			}
			return true;
			
	}
	
	public function deleteTable() {
			$sql = array(); 
			$sql[] = "DROP TABLE `".DB_PREFIX."cmsblock";
			$sql[] = "DROP TABLE `".DB_PREFIX."cmsblock_description";
			foreach( $sql as $q ){
				$query = $this->db->query( $q );
			}
			return true;
	}
}
?>