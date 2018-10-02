<?php
class ModelBlogArticle extends Model {
	public function getArticle($article_id) {
        $query = $this->db->query("SELECT DISTINCT a.article_id, a.image, a.author, a.sort_order, a.status, a.date_added, a.date_modified, ad.language_id, ad.name, ad.description, ad.intro_text, ad.meta_title, ad.meta_description, ad.meta_keyword FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) LEFT JOIN ". DB_PREFIX ."article_to_store ats ON (a.article_id = ats.article_id) WHERE a.article_id = '" . (int)$article_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ats.store_id = '". (int)$this->config->get('config_store_id') ."' AND a.status = '1' ");

		if ($query->num_rows) {
			return array(
				'article_id'       => $query->row['article_id'],
				'name'             => $query->row['name'],
				'author'           => $query->row['author'],
				'image'            => $query->row['image'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'intro_text'       => $query->row['intro_text'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified']
			);
		} else {
			return false;
		}
	}

    public function getArticlesByList($data = array(), $article_list_id) {
        $sql = "SELECT a.article_id " . " FROM " . DB_PREFIX . "article a";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_to_list al ON (a.article_id = al.article_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_to_store ats ON (a.article_id = ats.article_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND al.article_list_id = '" . (int) $article_list_id . "' AND ats.store_id = '" . (int)$this->config->get('config_store_id') . "' AND a.status = '1' ";


        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "ad.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR ad.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }
            $sql .= ")";
        }

        $sql .= " GROUP BY a.article_id";

        $sort_data = array(
            'ad.name',
            'a.sort_order',
            'a.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'ad.name') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY a.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(ad.name) DESC";
        } else {
            $sql .= " ASC, LCASE(ad.name) ASC";
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

        $article_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $article_data[$result['article_id']] = $this->getArticle($result['article_id']);
        }

        return $article_data;
    }

    public function getArticles($data = array()) {
        $sql = "SELECT a.article_id " . " FROM " . DB_PREFIX . "article a";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_to_store ats ON (a.article_id = ats.article_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ats.store_id = '". (int)$this->config->get('config_store_id') ."' AND a.status = '1' ";


        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "ad.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR ad.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }
            $sql .= ")";
        }

        $sql .= " GROUP BY a.article_id";

        $sort_data = array(
            'ad.name',
            'a.sort_order',
            'a.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'ad.name') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY a.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(ad.name) DESC";
        } else {
            $sql .= " ASC, LCASE(ad.name) ASC";
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

        $article_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $article_data[$result['article_id']] = $this->getArticle($result['article_id']);
        }

        return $article_data;
    }

	public function getLatestArticles($limit) {
		$article_data = $this->cache->get('article.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$limit);

        if (!$article_data) {
            $query = $this->db->query("SELECT article_id FROM " . DB_PREFIX . "article WHERE status = '1' ORDER BY date_added DESC LIMIT " . (int)$limit);

            foreach ($query->rows as $result) {
                $article_data[$result['article_id']] = $this->getArticle($result['article_id']);
            }

            $this->cache->set('article.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$limit, $article_data);
        }

        return $article_data;
	}

	public function getTotalArticles($data = array()) {
		$sql = "SELECT COUNT(DISTINCT a.article_id) AS total FROM " . DB_PREFIX . "article a";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_to_store ats ON (a.article_id = ats.article_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ats.store_id = '". (int)$this->config->get('config_store_id') ."' AND a.status = '1' ";

        if (!empty($data['filter_name']) ) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "ad.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR ad.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			$sql .= ")";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
