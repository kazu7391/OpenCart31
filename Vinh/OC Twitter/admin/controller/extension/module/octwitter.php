<?php
class ControllerExtensionModuleOctwitter extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_octwitter_id' => 'plazathemes',
            'module_octwitter_status' => 1,
            'module_octwitter_limit' => 3,
            'module_octwitter_show_time' => 1,
            'module_octwitter_consumer_key' => 'qulMEXc9RpNgvdHniZsKCQ',
            'module_octwitter_consumer_secret' => '9Wk7UwrlfkeR8BaKU1Nz7gS6Y3wQ2oMAuRTSPdwSpo',
            'module_octwitter_access_token' => '167448460-MuUwtTxWoehX4MKL8KrEbP6pkLnsQf0p3NKuiUGz',
            'module_octwitter_access_token_secret' => 'DKVQipT6cdOpnRELDxlsC3Mf5Rf20TA2IdUU6dzaqg'
        );

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_octwitter', $config);
    }

    public function index() {
        $this->load->language('extension/module/octwitter');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_octwitter', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['id'])) {
            $data['error_id'] = $this->error['id'];
        } else {
            $data['error_id'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/octwitter', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/octwitter', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_octwitter_status'])) {
            $data['module_octwitter_status'] = $this->request->post['module_octwitter_status'];
        } else {
            $data['module_octwitter_status'] = $this->config->get('module_octwitter_status');
        }

        if (isset($this->request->post['module_octwitter_id'])) {
            $data['module_octwitter_id'] = $this->request->post['module_octwitter_id'];
        } else {
            $data['module_octwitter_id'] = $this->config->get('module_octwitter_id');
        }

        if (isset($this->request->post['module_octwitter_limit'])) {
            $data['module_octwitter_limit'] = $this->request->post['module_octwitter_limit'];
        } else {
            $data['module_octwitter_limit'] = $this->config->get('module_octwitter_limit');
        }

        if (isset($this->request->post['module_octwitter_show_time'])) {
            $data['module_octwitter_show_time'] = $this->request->post['module_octwitter_show_time'];
        } else {
            $data['module_octwitter_show_time'] = $this->config->get('module_octwitter_show_time');
        }

        if (isset($this->request->post['module_octwitter_consumer_key'])) {
            $data['module_octwitter_consumer_key'] = $this->request->post['module_octwitter_consumer_key'];
        } else {
            $data['module_octwitter_consumer_key'] = $this->config->get('module_octwitter_consumer_key');
        }

        if (isset($this->request->post['module_octwitter_consumer_secret'])) {
            $data['module_octwitter_consumer_secret'] = $this->request->post['module_octwitter_consumer_secret'];
        } else {
            $data['module_octwitter_consumer_secret'] = $this->config->get('module_octwitter_consumer_secret');
        }

        if (isset($this->request->post['module_octwitter_access_token'])) {
            $data['module_octwitter_access_token'] = $this->request->post['module_octwitter_access_token'];
        } else {
            $data['module_octwitter_access_token'] = $this->config->get('module_octwitter_access_token');
        }

        if (isset($this->request->post['module_octwitter_access_token_secret'])) {
            $data['module_octwitter_access_token_secret'] = $this->request->post['module_octwitter_access_token_secret'];
        } else {
            $data['module_octwitter_access_token_secret'] = $this->config->get('module_octwitter_access_token_secret');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/octwitter', $data));
    }

    public function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/octwitter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_octwitter_id']) {
            $this->error['id'] = $this->language->get('error_id');
        }

        return !$this->error;
    }
}