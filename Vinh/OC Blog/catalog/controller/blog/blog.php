<?php
class ControllerBlogBlog extends Controller 
{
	public function index() {
		$this->load->model('blog/article');
        $this->load->language('blog/blog');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('module_ocblog_article_limit');
		}

		$this->document->setTitle($this->config->get('module_ocblog_meta_title'));
		$this->document->setDescription($this->config->get('module_ocblog_meta_description'));
		$this->document->setKeywords($this->config->get('module_ocblog_meta_keyword'));
		$this->document->addLink($this->url->link('blog/blog'),'');

		$data['heading_title'] = $this->config->get('module_ocblog_meta_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_blog'),
			'href' => $this->url->link('blog/blog')
		);

		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['articles'] = array();

		$filter_data = array(
			'filter_filter'      => $filter,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$article_total = $this->model_blog_article->getTotalArticles($filter_data);

		$results = $this->model_blog_article->getArticles($filter_data);
		
		$this->load->model('tool/image');

		$image_size = $this->config->get('module_octhemeoption_blog_image_size');

		foreach ($results as $result) {
			if($image_size['width'] && $image_size['height']) {
				$image = $this->model_tool_image->resize($result['image'], $image_size['width'], $image_size['height']);
			} else {
				$image = $this->model_tool_image->resize($result['image'], 100, 100);
			}

			$data['articles'][] = array(
				'article_id'  => $result['article_id'],
				'name'        => $result['name'],
				'author'	  => $result['author'],
				'image'		  => $image,
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'intro_text' => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
				'href'        => $this->url->link('blog/article', 'article_id=' . $result['article_id'] . $url)
			);
		}

		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link('blog/blog', '&sort=p.sort_order&order=ASC' . $url)
		);

		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['limits'] = array();

		$limits = array_unique(array($this->config->get('module_ocblog_article_limit'), 50, 75, 100));

		sort($limits);

		foreach($limits as $value) {
			$data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link('blog/blog', $url . '&limit=' . $value)
			);
		}

		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $article_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('blog/blog', $url . '&page={page}');

		$data['pagination'] = $pagination->render();
		$data['text_empty'] = $this->language->get('text_empty');
		$data['results'] = sprintf($this->language->get('text_pagination'), ($article_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($article_total - $limit)) ? $article_total : ((($page - 1) * $limit) + $limit), $article_total, ceil($article_total / $limit));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$this->response->setOutput($this->load->view('blog/blog', $data));
    }
}