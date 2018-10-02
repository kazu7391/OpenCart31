<?php
class ModelCatalogOctestimonial extends Model {
    public function getTestimonial($testimonial_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE t.testimonial_id = '" . (int)$testimonial_id . "'  AND t.status = '1'");
        return $query->row;
    }

    public function getTestimonials($start = 0, $limit = 10) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX ."testimonial_description td LEFT JOIN " . DB_PREFIX . "testimonial t ON (t.testimonial_id = td.testimonial_id) WHERE t.status = '1' ORDER BY t.sort_order ASC LIMIT " . (int)$start . "," . (int)$limit);
        return $query->rows;
    }
    public function getRandomTestimonial(){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX ."testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE   t.status = '1' ORDER BY RAND() LIMIT 1");
        return $query->row;
    }

    public function getTotalTestimonials() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "testimonial AS t WHERE t.status = '1'");
        return $query->row['total'];
    }

    public function addTestimonial($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "testimonial SET status = 1,sort_order =1");
        $testimonial_id = $this->db->getLastId();
        $this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_description SET   testimonial_id = '" . $testimonial_id . "',customer_name = '" . $this->db->escape($data['name']) . "',image = '" . $this->db->escape($data['image']) . "', content = '" . $this->db->escape($data['content'])."'");
        return $testimonial_id;
    }

}
?>