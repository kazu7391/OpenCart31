<?php
class ControllerProductOctestimonial extends Controller {
    public function index() {
        $this->load->language('extension/module/octestimonial');
        $this->load->model('catalog/octestimonial');
        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = array();

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->url->link('product/octestimonial'),
            'text'      => $this->language->get('heading_title')
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

//        $limit = (int) $this->config->get($this->config->get('config_theme') . '_testimonial_limit');
        $limit = 100;
        $testimonial_total = $this->model_catalog_octestimonial->getTotalTestimonials();

        if($limit > (int) $testimonial_total) {
            $limit = (int) $testimonial_total;
        }

        $data['testimonials'] = array();

//        $twidth = (int) $this->config->get($this->config->get('config_theme') . '_image_testimonial_width');
//        $theight = (int) $this->config->get($this->config->get('config_theme') . '_image_testimonial_height');

        $results = $this->model_catalog_octestimonial->getTestimonials(($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            if($result['image']) {
                $timage = $this->model_tool_image->resize($result['image'], 200, 200);
            } else {
                $timage = $this->model_tool_image->resize('placeholder.png', 200, 200);
            }

            $data['testimonials'][] = array(
                'customer_name'    => $result['customer_name'],
                'image' => $timage,
                'content' => html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $testimonial_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('product/octestimonial','page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($testimonial_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($testimonial_total - $limit)) ? $testimonial_total : ((($page - 1) * $limit) + $limit), $testimonial_total, ceil($testimonial_total / $limit));

        $data['continue'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('product/octestimonial', $data));
    }
}