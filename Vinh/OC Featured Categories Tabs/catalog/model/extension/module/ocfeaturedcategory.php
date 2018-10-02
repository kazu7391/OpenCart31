<?php
class ModelExtensionModuleOcfeaturedcategory extends Model
{
	public function getFeaturedCategories($limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' AND c.featured = '1' ORDER BY c.sort_order, LCASE(cd.name) LIMIT " . (int) $limit);

		return $query->rows;
	}
}