<?php 
class ModelHozmegamenuMenu extends Model {
    private $_show_level = 4;
	private $_menuLink = '';
	private $pattern = '/^([A-Z_]*)[0-9]+/';

	 public function getMenuCustomerLink($lang_id = NULL,$setting = array()) {
		$menu_items = $this->getMenuItems($setting);
		$item1=0;
		$lang_id = (int)$this->config->get('config_language_id');
		//$id_shop = (int)Shop::getContextShopID();
		 $showhome= $setting['hhome'];
		
        if ($showhome) {
		   $act = '';
		   if(isset($this->request->get['route'] )) {
				 if ($this->request->get['route'] == 'common/home') {
					$act = ' act';
				 }
			}
			$url =  $this->url->link('common/home');
            $id = "_home";
            $this->_menuLink .= '<div id="pt_menu' . $id . '" class="pt_menu' . $act . '">';
            $this->_menuLink .= '<div class="parentMenu">';
            $this->_menuLink .= '<a href="'.$url.'">';
            $this->_menuLink .= '<span>' . $this->language->get("text_home"). '</span>';
            $this->_menuLink .= '</a>';
            $this->_menuLink .= '</div>';
            $this->_menuLink .= '</div>';
        }
		

		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			if(!isset($value[1])) 
					continue;
			$id = (int)substr($item, strlen($value[1]), strlen($item));
			
			switch (substr($item, 0, strlen($value[1])))
			{
				case 'CAT':
					$item1 = $item1+ 1; 
					$this->_menuLink .= $this->drawCustomMenuItem($id, 0, false, $item1, $lang_id,$setting);
					break;
				case 'LINK': 
					$link = $this->getTopLinks($id);
					if($link) {
						$link = $link[0];
						//echo "<pre>"; print_r($cms); echo "</pre>";
						$this->_menuLink .= '<div id ="pt_menu_link' . $id . '" class ="pt_menu"><div class="parentMenu" ><a href="' . ($link['link']) . '"><span>' . $link['title'] . '</span></a></div></div>' . PHP_EOL;
					}
				case 'CMS': 
					$this->load->model('catalog/information');
					$cms = $this->model_catalog_information->getInformation($id);
                    if(isset($cms['information_id'])) {
    					//echo "<pre>"; print_r($cms); echo "</pre>"; 
    					$cms_link = $this->url->link('information/information', 'information_id=' . $cms['information_id']);
    					$this->_menuLink .= '<div id ="pt_menu_cms" class ="pt_menu"><div class="parentMenu" ><a href="'.$cms_link.'"><span>'.$cms['title'].'</span></a></div></div>'.PHP_EOL;
                    }
			}
	
		}
		
		$store_id = $this->config->get('config_store_id');
		// block customer menu link 
		$blockCustomer = $this->getCmsBlockContent(null, 'item');
		foreach ($blockCustomer as $bc) {
			$flag = false;
			$stores = explode(",", $bc['banner_store']);
			foreach($stores as $store) {
				if((int) $store == $store_id) {
					$flag = true;
				}
			}

			if($bc['status'] && $flag) {
				$this->_menuLink .= $this->drawCustomMenuBlock($bc['identify'], $bc);
			}
        }
		
		return 	$this->_menuLink ;
	}
	
	public function getMenuCustomerLinkMobile($lang_id = NULL,$setting = array()) {
		$menu_items = $this->getMenuItems($setting);
		$item1=0;
		$lang_id = (int)$this->config->get('config_language_id');
		$this->_menuLink = '<ul id="ma-mobilemenu" class="mobilemenu nav-collapse collapse">';

		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			if(!isset($value[1])) 
					continue;
			$id = (int)substr($item, strlen($value[1]), strlen($item));

			switch (substr($item, 0, strlen($value[1])))
			{
				case 'CAT':
					$curr_cate = $this->model_catalog_category->getCategory($id);
					//echo '<pre>'; print_r($this->drawCustomMenuItemMobile($id, 0, false, $item1, $lang_id,$setting)); die;
					if($curr_cate) {
						$item1 = $item1+ 1; 
						$this->_menuLink .= $this->drawCustomMenuItemMobile($id, 0, false, $item1, $lang_id,$setting);

					}
					break;
				case 'LINK': 
					$link = $this->getTopLinks($id);
					if(isset($link[0])) {
						$link = $link[0];
						$link_stores = explode(',', $link['banner_store']);
						$link_show = false;
						foreach($link_stores as $store) {
							$store = (int) $store;
							if($store == $this->config->get('config_store_id')) {
								$link_show = true;
							}
						}

						if($link_show) {
							$this->_menuLink .= '<li><a href="' . ($link['link']) . '"><span>' . $link['title'] . '</span></a></li>' . PHP_EOL;
						}
					}
					break;
				case 'CMS':
					$this->load->model('catalog/information');
					$cms = $this->model_catalog_information->getInformation($id);
                    if(isset($cms['information_id'])) {
    					$cms_link = $this->url->link('information/information', 'information_id=' . $cms['information_id']);
    					$this->_menuLink .= '<li><a href="'.$cms_link.'"><span>'.$cms['title'].'</span></a></li>'.PHP_EOL;
                    }
					break;
			}
	
		}

		$this->_menuLink .= '</ul>';
		return 	$this->_menuLink ;
	}
	
	 public function drawCustomMenuBlock($blockId, $bc) {
        $html = array();
        $id = '_' . $blockId;
		$blockHtml = null; 
		$blockHtml = nl2br(html_entity_decode($bc['description'], ENT_QUOTES, 'UTF-8'));
        $drawPopup = $blockHtml;
        if ($drawPopup) {
            $html[] = '<div id="pt_menu' . $id . '" class="pt_menu">';
        } else {
            $html[] = '<div id="pt_menu' . $id . '" class="pt_menu">';
        }
        // --- Top Menu Item ---
        $html[] = '<div class="parentMenu">';
//        $html[] = '<a href="#">';
        $name = $bc['title'];
        $html[] = '<span class="block-title">' . $name . '</span>';
//        $html[] = '</a>';
        $html[] = '</div>';
        // --- Add Popup block (hidden) ---
        if ($drawPopup) {
            // --- Popup function for hide ---
            $html[] = '<div id="popup' . $id . '" class="popup cmsblock" style="display: none; width: 904px;">';
            if ($blockHtml) {
                $html[] = '<div class="block2" id="block2' . $id . '">';
                $html[] = $blockHtml;
                $html[] = '</div>';
            }
            $html[] = '</div>';
        }
        $html[] = '</div>';
        $html = implode("\n", $html);
        return $html;
    }
	
	public function getStaticblockLists($blockId  = null) {
		$cmsinfos = $this->getInformations($blockId); 
		return $cmsinfos; 
	}
	
	public function getCmsBlockContent($blockId = NULL, $task = NULL) {
		$staticBlock = $this->getStaticblockLists($blockId);
		//echo "<pre>"; print_r($staticBlock);
		$html = "";
		if (count($staticBlock) > 0) {
			if($staticBlock[0]['status']) {
				$description= nl2br(html_entity_decode($staticBlock[0]['description'], ENT_QUOTES, 'UTF-8'));
				$html .= $description;
			}
		}
		if ($task == 'item') {
			$staticBlock = $this->getCustomerLinks();
			return $staticBlock;
		} else {
			return $html;
		}
	}
	
	public function getCategoryLevelByCateId($category_id=null) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)  LEFT JOIN " . DB_PREFIX . "category_path cp ON (c.category_id = cp.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE  cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = 1 AND c.`category_id`='".$category_id."' ORDER BY c.sort_order");
		$results = $query->rows;
		foreach($results as $result ) {
			$cate_level = $result['level']; 
		}
		if($cate_level) return $cate_level ;
		return 0; 
	}
	
	public function getInformations($identify = null) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.identify = '".$identify."'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
	}
	
	public function getTopLinks($identify = null) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.cmsblock_id = '".$identify."'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
	}
	
	
	public function getCustomerLinks() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.identify like 'pt_item_menu" . "%'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
	}
	
	function getCurrentCatetgoryID($obj){    
	if(isset($obj->request->get['path'])) {
		$path = $obj->request->get['path'];
		$cats = explode('_', $path);
		$cat_id = $cats[count($cats) - 1];
		return $path;
		} else {
		return null;
		}
	}

	public function drawCustomMenuItem($category, $level = 0, $last = false, $item, $lang_id,$setting) {

		if($setting['status']) {
				$this->_show_level = $setting['hlevel'];
			} else {
				$this->_show_level = 4; 
		}	

        $html = array();
        $blockHtml = '';
        $id_shop = 1;
        $id = $category;
        $blockId = sprintf('pt_menu_idcat_%d', $id);
        $staticBlock = $this->getCmsBlockContent($blockId);
        $blockIdRight = sprintf('pt_menu_idcat_%d_right', $id);
        $staticBlockRight = $this->getCmsBlockContent($blockIdRight);
        // --- Static Block ---
        $blockHtml = $staticBlock;
        /* check block right */
        $blockHtmlRight = $staticBlockRight;

        if ($blockHtmlRight)
            $blockHtml = $blockHtmlRight;
        // --- Sub Categories ---
          $activeChildren = $this->model_catalog_category->getCategories($id);		
         $activeChildren = $this->getCategoryByLevelMax($activeChildren,$setting); 
        // --- class for active category ---
		//echo "<pre>"; print_r($_GET	); 
        $active = '';
		$category_id = $this->getCurrentCatetgoryID($this); 
		if($category_id == $id) $active = ' act'; 
        //if (isset($cateCurrent[0]) && in_array($category, array($cateCurrent[0])))
        // --- Popup functions for show ---
        $drawPopup = ($blockHtml || count($activeChildren));
        if ($drawPopup) {
            $html[] = '<div id="pt_menu' . $id . '" class="pt_menu' . $active . ' nav-' . $item . '">';
        } else {
            $html[] = '<div id="pt_menu' . $id . '" class="pt_menu' . $active . ' nav-' . $item . ' pt_menu_no_child">';
        }
		//echo $category;
        //$cate = new Category((int) $category);
		$cate = $this->model_catalog_category->getCategory($id);
        //$link = $categoryObject->getLinkRewrite($category, $lang_id);
        $parameters = "";
		$link = $this->url->link('product/category', "path=".$id);
        // --- Top Menu Item ---
        $html[] = '<div class="parentMenu">';
        $html[] = '<a href="' . $link . '">';
        $name = null;
		  if(isset($cate['name'])) {
		   $name = strip_tags($cate['name']);
		  }
		$name = $name;
        $html[] = '<span>' . $name . '</span>';
        $html[] = '</a>';
        $html[] = '</div>';
		
		    // --- Add Popup block (hidden) ---
        if ($drawPopup) {
            if ($this->_show_level >= 2) {
                // --- Popup function for hide ---
                $html[] = '<div id="popup' . $id . '" class="popup" style="display: none; width: 1228px;">';
                // --- draw Sub Categories ---
                 if (count($activeChildren) || $blockHtml) {
					//echo "<pre>"; print_r($blockHtml); echo "</pre>"; 
                    $html[] = '<div class="block1" id="block1' . $id . '">';
                    $html[] = $this->drawColumns($activeChildren, $id, $lang_id,$setting);
                    if ($blockHtml && $blockHtmlRight) { 
                        $html[] = '<div class="column blockright">';
                        $html[] = $blockHtml;
                        $html[] = '</div>';
                    }
                    $html[] = '<div class="clearBoth"></div>';
                    $html[] = '</div>';
                }
                // --- draw Custom User Block ---
                if ($blockHtml && !$blockHtmlRight) { 
                    $html[] = '<div class="block2" id="block2' . $id . '">';
                    $html[] = $blockHtml;
                    $html[] = '</div>';
                }
                $html[] = '</div>';
            }
        }

 
        $html[] = '</div>';
		//echo "<pre>"; print_r($html); echo "</pre>";
        $html = implode("\n", $html);
        return $html;
    }
    
    public function drawCustomMenuItemMobile($category, $level = 0, $last = false, $item, $lang_id,$setting) {

		if($setting['status']) {
				$this->_show_level = $setting['hlevel'];
			} else {
				$this->_show_level = 4; 
		}	
        $blockHtml = '';
        $id_shop = 1;
        $id = $category;
        // --- Sub Categories ---
        $activeChildren = $this->model_catalog_category->getCategories($id);	
       $activeChildren = $this->getCategoryByLevelMax($activeChildren,$setting);
        // --- class for active category ---

        $active = '';
		$category_id = $this->getCurrentCatetgoryID($this); 
		if($category_id == $id) $active = ' act'; 

        // --- Popup functions for show ---
        $drawPopup = (count($activeChildren));
        if ($drawPopup) {
            $html = '<li class="had_child">';
        } else {
            $html = '<li class="no_child">';
        }

		$cate = $this->model_catalog_category->getCategory($id);

		$link = $this->url->link('product/category', "path=".$id);
        // --- Top Menu Item ---
        $html .= '<a href="' . $link . '">';
        $name = null;
		  if(isset($cate['name'])) {
		   $name = strip_tags($cate['name']);
		  }
        $html .=  $name ;
        $html .= '</a><span class="ttclose"><a href="javascript:void(0)"></a></span>';
		    // --- Add Popup block (hidden) ---
        if ($drawPopup) {
            if ($this->_show_level >= 2) {
                // --- Popup function for hide ---
                $html .= '<ul>';
                // --- draw Sub Categories ---
                 if (count($activeChildren)) {
                    $html .= $this->drawColumnsMobile($activeChildren, $id, $lang_id,$setting);
                }
                $html .= '</ul>';
            }
        }

 
        $html .= '</li>';
        return $html;
    }

    public function drawColumnsMobile($children, $id, $lang_id,$setting) {	
		$html = '';
		$test = array();
		foreach($children as $child){
			$test[][0] = $child;
			//$html.= $this->drawMenuItemMobile($test, 1, $lang_id,$setting);
		}
		foreach ($test as $test1){
			$html.= $this->drawMenuItemMobile($test1, 1, $lang_id,$setting);
		}
		return $html;
	}
	public function drawMenuItemMobile ($children, $level = 1, $lang_id = 1,$setting) {
		$lang_id = (int)$this->config->get('config_language_id');
		$this->load->model('catalog/category');
     
        $countChildren = 0;

        foreach ($children as $child) {
            $activeChildCat = $this->model_catalog_category->getCategories($child['category_id']);	
            
            $activeChildCat = $this->getCategoryByLevelMax($activeChildCat,$setting);
            if ($activeChildCat) {
                $countChildren++;
            }
            
        }
        $html = '';
        foreach ($children as $child) {
            $info =  $this->model_catalog_category->getCategory($child['category_id']);
            $level = $info['top'];

            $cate_id = (int) $child['category_id'];
            // --- format category name ---
            $name = strip_tags($child['name']);

            if (count($child) > 0) {
                $link = $this->url->link('product/category', "path=".$child['category_id']);
         
                $html .= '<li ><a href="' . $link . '"><span>' . $name . '</span></a>';
                
         		if($setting['hlevel'] > 2) {
         			$activeChildren = $this->model_catalog_category->getCategories($child['category_id']);
	                $activeChildren = $this->getCategoryByLevelMax($activeChildren,$setting);
	                if(count($activeChildren) > 0) {
		                $html .= '<span class="ttclose"><a href="javascript:void(0)"></a></span>';
		            }
	                if (count($activeChildren) > 0) {
	                    $html.= '<ul >';
	                    //$html.= $this->drawMenuItem($activeChildren, $level + 1);
						$html.= $this->drawMenuItemMobile($activeChildren, $level + 1, $lang_id,$setting);
	                    $html.= '</ul></li>';
	                }
         		}
                
            }
        }

        return $html;
    }
	
	private function getMenuItems($setting = array()){
		$hactive = $setting['hactive'];
		return explode(',',$hactive);
	}

	 public function seperateColumns($parentCates, $num, $lang_id,$setting) {
			$countChildren = 0;
			foreach ($parentCates as $cat => $childCat) {
			         $activeChildCat = $this->model_catalog_category->getCategories($childCat['category_id']);		
					$activeChildCat = $this->getCategoryByLevelMax($activeChildCat,$setting);
				if ($activeChildCat) {
					$countChildren++;
				}
			}
			$count = count($parentCates);
			if ($countChildren == 0) {
				if($count > 20){
					$num = $num;
				}elseif($count < 20 && $count > 10){
					$num = 3;
				}elseif($count <=10 && $count > 5){
					$num = 2;
				}else{
					$num = 1;
				}				
			}
			if ($count)
				$parentCates = $this->partition_element($parentCates,$num);

        return $parentCates;
    }
	
	function partition_element(Array $list, $p) {
		$listlen = count($list);
		$partlen = floor($listlen / $p);
		$partrem = $listlen % $p;
		$partition = array();
		$mark = 0;
		for($px = 0; $px < $p; $px ++) {
			$incr = ($px < $partrem) ? $partlen + 1 : $partlen;
			$partition[$px] = array_slice($list, $mark, $incr);
			$mark += $incr;
		}
		return $partition;
	}

	 public function drawColumns($children, $id, $lang_id,$setting) {
			$html = '';
			// --- explode by columns ---
			$columns = $setting['hdepth'];
			if ($columns < 1)
				$columns = 1;
			$chunks = $this->seperateColumns($children, $columns, $lang_id,$setting);
			$columChunk = count($chunks);
			// --- draw columns ---
			$classSpecial = '';
			$keyLast = 0;
			foreach ($chunks as $key => $value) {
				if (count($value))
					$keyLast++;
			}
			$blockHtml = '';
			//$id_shop = (int) Context::getContext()->shop->id;
			$blockId = sprintf('pt_menu_idcat_%d', $id);
			$staticBlock = $this->getCmsBlockContent($blockId);
			$blockIdRight = sprintf('pt_menu_idcat_%d_right', $id);
			$staticBlockRight = $this->getCmsBlockContent($blockIdRight);
			// --- Static Block ---
			$blockHtml = $staticBlock;
			/* check block right */
			$blockHtmlRight = $staticBlockRight;

			foreach ($chunks as $key => $value) {
				   if (!count($value))
						continue;
					if ($key == $keyLast - 1) {
						$classSpecial = ($blockHtmlRight && $blockHtml) ? '' : ' last';
					} elseif ($key == 0) {
						$classSpecial = ' first';
					} else {
						$classSpecial = '';
					}
			
				$html.= '<div class="column' . $classSpecial . ' col' . ($key + 1) . '">';
				$html.= $this->drawMenuItem($value, 1, $columChunk, $lang_id,$setting);

				$html.= '</div>';
			}
			return $html;
		}	
	
  public function drawMenuItem($children, $level = 0, $columChunk = 0, $lang_id = 1,$setting) {
		$lang_id = (int)$this->config->get('config_language_id');
		$this->load->model('catalog/category');
		
        $html = '<div class="itemMenu level' . $level . '">';
     
        $countChildren = 0;
        $ClassNoChildren = '';

        foreach ($children as $child) {
            $activeChildCat = $this->model_catalog_category->getCategories($child['category_id']);		
            $activeChildCat = $this->getCategoryByLevelMax($activeChildCat,$setting);
            if ($activeChildCat) {
                $countChildren++;
            }
        }
        if ($countChildren == 0 && $columChunk == 1) {
            $ClassNoChildren = ' nochild';
        }

        foreach ($children as $child) {
             $info =  $this->model_catalog_category->getCategory($child['category_id']);
            $level = (int)  $this->getCategoryLevelByCateId($child['category_id']);
            $active = '';
            //$currentCate = $this->getCurrentCategoriesId($lang_id);
            $cate_id = (int) $child['category_id'];
          //  if (in_array($cate_id, $currentCate)) {
                if ($this->haveCateChildren($cate_id, $lang_id)) {
                    $active = ' actParent';
                } else {
                    $active = ' act';
                }
            //}
            // --- format category name ---
            $name = strip_tags($child['name']);
            if (count($child) > 0) {
                $parameters = null;
                $link = $this->url->link('product/category', "path=".$child['category_id']);
         
                $html.= '<a class="itemMenuName level' . $level . $active . $ClassNoChildren . '" href="' . $link . '"><span>' . $name .'-level-'.$level.'</span></a>';
         		
         		if($setting['hlevel'] > 2 && $level <= $setting['hlevel'] ) {
         			$activeChildren = $this->model_catalog_category->getCategories($child['category_id']);		
	                $activeChildren = $this->getCategoryByLevelMax($activeChildren,$setting);
	                if (count($activeChildren) > 0) {
	                    $html.= '<div class="itemSubMenu level' . $level . '">';
	                    //$html.= $this->drawMenuItem($activeChildren, $level + 1);
						$html.= $this->drawMenuItem($activeChildren, $level ,$columChunk, $lang_id,$setting);
	                    $html.= '</div>';
	                }
         		}
                
            }
        }
        $html.= '</div>';
        return $html;
    }
	
	 public function getCategoryByLevelMax($cates = NULL,$setting) {
			if(isset($setting['hlevel'])) {
				$this->_show_level = $setting['hlevel'];
			} else {
				$this->_show_level = 4; 
			}	
			
        if (count($cates) < 1)
            return array();
        $cateArray = array();
        foreach ($cates as $key => $cate) {
            $cate_id = $cate['category_id'];
			$cateObject = $this->model_catalog_category->getCategory((int) $cate_id); 
			//echo "<pre>"; print_r($cateObject); echo "</pre>";
            $cate_level = $this->getCategoryLevelByCateId($cate_id);
            $cateArray[$key] = $cate;
         
        }

        if ($cateArray)
            return $cateArray;
        return array();
    }
	
	 public function getCurrentCategoriesId($lang_id = NULL) {
        if (isset($_GET['category_id'])) {
            $lastCateId = $_GET['category_id'];
        } else {
            $lastCateId = 0;
        }

        $lastCate = $this->model_catalog_category->getCategory((int) $lastCateId);
        //echo $lastCate->name[1]; echo '--------';
        $parentCate = $lastCate->getParentsCategories($lang_id);
        $arrayCateCurrent = array();
        foreach ($parentCate as $pcate) {
            $arrayCateCurrent[] = $pcate['id_category'];
        }
        return $arrayCateCurrent;
    }
	
	public function haveCateChildren($cate_id = NULL, $lang_id = NULL) {
       	$childCates =   $this->model_catalog_category->getCategories($cate_id);	
        if (count($childCates) > 0)
            return true;
        return false;
    }
	
	    //mobile megamenu 
     public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0) {
        if (is_null($id_category))
            $id_category = 0;

        $children = array();
	//	echo $id_category; 
	//	echo '--'; echo "<pre>";print_r($resultParents[0]);  echo "</pre>";
	
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
            foreach ($resultParents[$id_category] as $subcat)
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['category_id'], $currentDepth + 1);
				
		
        // if (!isset($resultIds[$id_category]))
            // return false;
		$name = ""; 
		$des =  "";
		if($id_category !=0) $name = $resultIds[$id_category]['name']; 
		if($id_category !=0) $des = $resultIds[$id_category]['description']; 
        $return = array(
					'id' => $id_category, 
					'link' => '#',
		 			'name' => $name, 
					'desc' => $des,
					'children' => $children
		); 

        return $return;
    }
	
	 public function getblockCategTree() {
		$parent_id = 0;
      	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE  cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		$result =  $query->rows;
		 

        $resultParents = array();
        $resultIds = array();
      // echo "<pre>"; print_r($result); die; 
        foreach ($result as $row) {
            $resultParents[$row['parent_id']][] = $row;
            $resultIds[$row['category_id']] = $row;
			//echo "<pre>"; print_r($row); echo "</pre>"; 
        }
		 
	//die;
        $blockCategTree = $this->getTree($resultParents, $resultIds, 4);
//		echo "<pre>";print_r($blockCategTree);  echo "</pre>";
      //  unset($resultParents, $resultIds);
	//	echo "<pre>";print_r($blockCategTree);  echo "</pre>";
        return $blockCategTree;
    }

	
}
?>