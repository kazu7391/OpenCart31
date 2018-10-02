<?php
class ControllerExtensionModuleOcnewproductpage extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_ocnewproductpage_status'   => 1,
            'module_ocnewproductpage_limit'    => '20'
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_ocnewproductpage', $config);
    }

    public function index() {
        $this->load->language('extension/module/ocnewproductpage');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post_data = $this->request->post;

            // Parse all the coming data to Setting Model to save it in database.
            $this->model_setting_setting->editSetting('module_ocnewproductpage', $post_data);

            // To display the success text on data save
            $this->session->data['success'] = $this->language->get('text_success');

            // Redirect to the Module Listing
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
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
            'href' => $this->url->link('extension/module/ocnewproductpage', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/ocnewproductpage', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_ocnewproductpage_limit'])) {
            $data['module_ocnewproductpage_limit'] = $this->request->post['module_ocnewproductpage_limit'];
        } else {
            $data['module_ocnewproductpage_limit'] = $this->config->get('module_ocnewproductpage_limit');
        }

        if (isset($this->request->post['module_ocnewproductpage_status'])) {
            $data['module_ocnewproductpage_status'] = $this->request->post['module_ocnewproductpage_status'];
        } else {
            $data['module_ocnewproductpage_status'] = $this->config->get('module_ocnewproductpage_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ocnewproductpage', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ocnewproductpage')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}