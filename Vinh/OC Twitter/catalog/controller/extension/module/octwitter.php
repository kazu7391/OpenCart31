<?php
class ControllerExtensionModuleOctwitter extends Controller
{
    public function index() {
        $this->language->load('extension/module/octwitter');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['octwitter_user'] = $this->config->get('module_octwitter_id');
        $data['octwitter_limit'] = $this->config->get('module_octwitter_limit');
        $data['octwitter_consumer_key'] = $this->config->get('module_octwitter_consumer_key');
        $data['octwitter_consumer_secret'] = $this->config->get('module_octwitter_consumer_secret');
        $data['octwitter_access_token'] = $this->config->get('module_octwitter_access_token');
        $data['octwitter_access_token_secret'] = $this->config->get('module_octwitter_access_token_secret');

        $show_time = (int) $this->config->get('module_octwitter_show_time');

        if($show_time) {
            $data['octwitter_show_time'] = true;
        } else {
            $data['octwitter_show_time'] = false;
        }

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $base_url = str_replace('http', 'https', $this->config->get('config_url'));
        } else {
            $base_url = $this->config->get('config_url');
        }

        $data['base_url'] = $base_url;

        return $this->load->view('extension/module/octwitter', $data);
    }
}