<?php
class ControllerBlogArticle extends Controller {
	public function index() {
		$this->load->language('blog/article');

		$this->load->model('blog/article');

		if (isset($this->request->get['article_id'])) {
			$article_id = (int)$this->request->get['article_id'];
		} else {
			$article_id = 0;
		}

		$article_info = $this->model_blog_article->getArticle($article_id);

		if ($article_info) {
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_blog'),
				'href' => $this->url->link('blog/blog')
			);

			$data['breadcrumbs'][] = array(
				'text' => $article_info['name'],
				'href' => $this->url->link('blog/article', '&article_id=' . $this->request->get['article_id'])
			);

			$this->document->setTitle($article_info['meta_title']);
			$this->document->setDescription($article_info['meta_description']);
			$this->document->setKeywords($article_info['meta_keyword']);
			$this->document->addLink($this->url->link('blog/article', 'article_id=' . $this->request->get['article_id']), 'canonical');

			$data['heading_title'] = $article_info['name'];
			$data['author'] = $article_info['author'];
            $data['date'] = date($this->language->get('date_format_short'), strtotime($article_info['date_added']));
			$data['article_id'] = (int)$this->request->get['article_id'];
			
			$data['description'] = html_entity_decode($article_info['description'], ENT_QUOTES, 'UTF-8');

			$this->load->model('tool/image');

			if($this->config->get('module_ocblog_article_width')) {
				$image_size_width = (int) $this->config->get('module_ocblog_article_width');
			} else {
				$image_size_width = 200;
			}

			if($this->config->get('module_ocblog_article_height')) {
				$image_size_height = (int) $this->config->get('module_ocblog_article_height');
			} else {
				$image_size_height = 200;
			}

			$data['image'] = $this->model_tool_image->resize($article_info['image'], $image_size_width, $image_size_height);	

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('blog/article', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('blog/article', $url . '&article_id=' . $article_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}