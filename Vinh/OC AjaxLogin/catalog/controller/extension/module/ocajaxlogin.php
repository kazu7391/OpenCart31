<?php

class ControllerExtensionModuleOcajaxlogin extends Controller {

    public function index() {

        $this->load->language('extension/module/ocajaxlogin'); // loads the language file of Ajax Login

        $enable_status = $this->config->get('module_ocajaxlogin_status');
        if($enable_status == '1') {
            $data['enable_status'] = true;
        } else {
            $data['enable_status'] = false;
        }

        $enable_redirect = $this->config->get('module_ocajaxlogin_redirect_status');
        if($enable_redirect == '1') {
            $data['enable_redirect'] = true;
        } else {
            $data['enable_redirect'] = false;
        }

        $loader_img = $this->config->get('module_ocajaxlogin_loader_img');
        if($loader_img) {
            $data['loader_img'] = $this->config->get('config_url') . 'image/' . $loader_img;
        }

        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/opentheme/ocajaxlogin/css/ocajaxlogin.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/opentheme/ocajaxlogin/css/ocajaxlogin.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/ocajaxlogin/css/ocajaxlogin.css');
        }

        $this->document->addScript('catalog/view/javascript/opentheme/ocajaxlogin/ocajaxlogin.js');

        $data['heading_title'] = $this->language->get('heading_title'); // set the heading_title of the module
        $data['ajax_login_content'] = $this->load->controller('extension/module/ajaxlogin');
        $data['ajax_register_content'] = $this->load->controller('extension/module/ajaxregister');
        $data['ajax_success_content'] = $this->load->controller('extension/module/ajaxregister/success');
        $data['ajax_logoutsuccess_content'] = $this->load->controller('extension/module/ajaxlogin/logoutsuccess');

        return $this->load->view('extension/module/ocajaxlogin/ocajaxlogin', $data);
    }
}