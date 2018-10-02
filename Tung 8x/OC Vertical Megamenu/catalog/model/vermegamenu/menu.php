<?php
class ModelVermegamenuMenu extends Model {	
    private $_show_level = 4;
	private $_menuLink = '';
	private $pattern = '/^([A-Z_]*)[0-9]+/';
	
	 public function getMenuCustomerLink($lang_id = NULL,$setting = array()) {
		$menu_items = $this->getMenuItems($setting);
		$item1=0;
		$lang_id = (int)$this->config->get('config_language_id');
		//$id_shop = (int)Shop::getContextShopID();
		$showhome= $setting['hhome'];
        $item_number = 20;
        if ($showhome) {
			$act = '';
			 // if ($this->request->get['route'] == 'common/home') {
							// $act = ' act';
			 // }
			 $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $id = "_home";
            $this->_menuLink .= '<div id="pt_ver_menu' . $id . '" class="pt_ver_menu' . $act . '">';
            $this->_menuLink .= '<div class="parentMenu">';
            $this->_menuLink .= '<a href="'.$url.'">';
            $this->_menuLink .= '<span>' . $this->language->get("text_home"). '</span>';
            $this->_menuLink .= '</a>';
            $this->_menuLink .= '</div>';
            $this->_menuLink .= '</div>';
        }
		
		$count = 0;
        $setting['is_over'] = false;
        $over_class = "";

		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			$id = (int)substr($item, strlen($value[1]), strlen($item));

            if($count >= $item_number) {
                $over_class = ' over-menu';
                $setting['is_over'] = true;
            }

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
                        $this->_menuLink .= '<div id ="pt_ver_menu_link" class ="pt_ver_menu '. $over_class .'"><div class="parentMenu" ><a href="'.($link['link']).'"><span>'.$link['title'].'</span></a></div></div>'.PHP_EOL;
                    }
				case 'CMS': 
					$this->load->model('catalog/information');
					$cms = $this->model_catalog_information->getInformation($id);
					//echo "<pre>"; print_r($cms); echo "</pre>";
                    if(isset($cms['information_id'])) {
                        $cms_link = $this->url->link('information/information', 'information_id=' . $cms['information_id']);
                        $this->_menuLink .= '<div id ="pt_ver_menu_cms" class ="pt_ver_menu '. $over_class .'"><div class="parentMenu" ><a href="'.$cms_link.'"><span>'.$cms['title'].'</span></a></div></div>'.PHP_EOL;
                    }
			}
            $count++;        
		}
		
        $this->load->language('extension/module/vermegamenu');

         // block customer menu link
         $blockCustomer = $this->getCmsBlockContent(null, 'item');
         foreach ($blockCustomer as $bc) {
             if($bc['status']) {
                 $this->_menuLink .= $this->drawCustomMenuBlock($bc['identify'], $bc, $setting);
             }
         }

        if($setting['is_over']) {
            $this->_menuLink .= '<div class="more-wrap pt_ver_menu"><div class="parentMenu"><a href="javascript:void(0);" class="more-ver-menu"><span>'.$this->language->get('text_more_vertical_menu').'</span></a></div></div>';
            $this->_menuLink .= '<div class="close-wrap pt_ver_menu over-menu"><div class="parentMenu"><a href="javascript:void(0);" class="close-ver-menu"><span>'.$this->language->get('text_close_vertical_menu').'</span></a></div></div>';
        }

		return 	$this->_menuLink ; 

	}
	
	
    public function drawCustomMenuBlock($blockId, $bc, $setting) {
        $html = array();
        $id = '_' . $blockId;
        if(isset($setting['is_over']) && $setting['is_over']) {
            $over_class = 'over-menu';
        } else {
            $over_class = '';
        }
		$blockHtml = null;
		$blockHtml = nl2br(html_entity_decode($bc['description'], ENT_QUOTES, 'UTF-8'));
        $drawPopup = $blockHtml;
        if ($drawPopup) {
            $html[] = '<div id="pt_ver_menu' . $id . '" class="pt_ver_menu '. $over_class .'">';
        } else {
            $html[] = '<div id="pt_ver_menu' . $id . '" class="pt_ver_menu '. $over_class .'">';
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
            $html[] = '<div id="popup' . $id . '" class="popup cmsblock" style="display: none; width: 904px;"><div class="arrow-left-menu"></div>';
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
	
		public function getMobileLink($setting = array()) {
		
		$this->_menuLink = null;
		$menu_items = $this->getMenuItems($setting);
		$item1=0;
		$lang_id = (int)$this->config->get('config_language_id');
		//$id_shop = (int)Shop::getContextShopID();
		 $showhome= $setting['hhome'];
		
		

		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			if(!isset($value[1])) continue;
			$id = (int)substr($item, strlen($value[1]), strlen($item));

			switch (substr($item, 0, strlen($value[1])))
			{
				case 'LINK': 
					$link = $this->model_vermegamenu_menu->getTopLinks($id);
					if($link) {
						$link = $link[0];
						//echo "<pre>"; print_r($cms); echo "</pre>";
						$this->_menuLink .= '<li><a href="' . ($link['link']) . '"><span>' . $link['title'] . '</span></a></li>' . PHP_EOL;
					}
				case 'CMS': 
					$this->load->model('catalog/information');
					$cms = $this->model_catalog_information->getInformation($id);
                    if(isset($cms['information_id'])) {
    					//echo "<pre>"; print_r($cms); echo "</pre>"; 
    					$cms_link = $this->url->link('information/information', 'information_id=' . $cms['information_id']);
    					$this->_menuLink .= '<li><a href="'.$cms_link.'"><span>'.$cms['title'].'</span></a></li>'.PHP_EOL;
                    }
			}
	
		}
		return 	$this->_menuLink ;
		
	
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
	
	public function getInformations($identify = null) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.identify = '".$identify."'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
	}
	
	public function getTopLinks($identify = null) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.cmsblock_id = '".$identify."'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
	}
	
	
	public function getCustomerLinks() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmsblock i LEFT JOIN " . DB_PREFIX . "cmsblock_description id ON (i.cmsblock_id = id.cmsblock_id) LEFT JOIN " . DB_PREFIX . "cmsblock_to_store i2s ON (i.cmsblock_id = i2s.cmsblock_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.identify like 'pt_item_vmenu" . "%'  ORDER BY i.sort_order, LCASE(id.title) ASC");
		return $query->rows;
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

	
	public function drawCustomMenuItem($category, $level = 0, $last = false, $item, $lang_id,$setting = array()) {
        if($setting['status']) {
            $this->_show_level = $setting['hlevel'];
        } else {
            $this->_show_level = 4;
        }
        //$cateCurrent = $this->getCurrentCategoriesId($lang_id);

        $html = array();
        $blockHtml = '';
        $id_shop = 1;
        $id = $category;
        $blockId = sprintf('pt_ver_menu_idcat_%d', $id);
        $staticBlock = $this->getCmsBlockContent($blockId);
        $blockIdRight = sprintf('pt_ver_menu_idcat_%d_right', $id);
        $staticBlockRight = $this->getCmsBlockContent($blockIdRight);
        // --- Static Block ---
        $blockHtml = $staticBlock;
        /* check block right */
        $blockHtmlRight = $staticBlockRight;

        if ($blockHtmlRight)
            $blockHtml = $blockHtmlRight;
        // --- Sub Categories ---
          $activeChildren = $this->model_catalog_category->getCategories($id);		
         $activeChildren = $this->getCategoryByLevelMax($activeChildren);
        // --- class for active category ---
		//echo "<pre>"; print_r($_GET	); 
        $active = '';
		$category_id = $this->getCurrentCatetgoryID($this); 
		if($category_id == $id) $active = ' act'; 
        //if (isset($cateCurrent[0]) && in_array($category, array($cateCurrent[0])))
        // --- Popup functions for show ---
        $drawPopup = ($blockHtml || count($activeChildren));
        
        $over_class = '';
        if($setting['is_over']) {
            $over_class = ' over-menu';
        }
        if ($drawPopup) {
            $html[] = '<div id="pt_ver_menu' . $id . '" class="pt_ver_menu' . $active . $over_class .' nav-' . $item . ' had-child">';
        } else {
            $html[] = '<div id="pt_ver_menu' . $id . '" class="pt_ver_menu' . $active . $over_class .' nav-' . $item . ' pt_ver_menu_no_child">';
        }
		//echo $category;
        //$cate = new Category((int) $category);
		$cate = $this->model_catalog_category->getCategory($id);
        //$link = $categoryObject->getLinkRewrite($category, $lang_id);
        $parameters = "";
		$link = $this->url->link('product/category', "path=".$id);
		if(isset($cate['thumbnail_image'])) {
            $thumbnail_image =  $this->model_tool_image->resize($cate['thumbnail_image'], 20,20);
            $html[] = '<img src="'.$thumbnail_image.'"  alt ="thumbnail_image" />';
        }
		$name = ""; 
		if(isset($cate['name'])) {
        // --- Top Menu Item ---
        $html[] = '<div class="parentMenu">';
        $html[] = '<a href="' . $link . '">';
		
			$name = strip_tags($cate['name']);
			$name = str_replace('&nbsp;', ' ', $name);
		
			$name = $name;
			$html[] = '<span>' . $name . '</span>';
			$html[] = '</a>';
			$html[] = '</div>';
		}
		    // --- Add Popup block (hidden) ---
        if ($drawPopup) {
            if ($this->_show_level >= 2) {
                // --- Popup function for hide ---
                $html[] = '<div id="popup' . $id . '" class="popup" style="display: none; width:815px"><div class="content-popup"><div class="arrow-left-menu"></div>';
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
                $html[] = '</div></div>';
            }
        }

 
        $html[] = '</div>';
		//echo "<pre>"; print_r($html); echo "</pre>";
        $html = implode("\n", $html);
        return $html;
    }
	
	private function getMenuItems($setting = array()){
	//	echo "<pre>"; print_r($setting) ; echo "</pre>";
	//	$this->getInfomatinOptions();
		return explode(',',$setting['h_active_cate']);
	}

	 public function seperateColumns($parentCates, $num, $lang_id) {
			$countChildren = 0;
			foreach ($parentCates as $cat => $childCat) {
			         $activeChildCat = $this->model_catalog_category->getCategories($childCat['category_id']);		
					$activeChildCat = $this->getCategoryByLevelMax($activeChildCat);
				if ($activeChildCat) {
					$countChildren++;
				}
			}
			if ($countChildren == 0) {
				$num = 1;
			}


			$count = count($parentCates);
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

	 public function drawColumns($children, $id, $lang_id,$setting = array()) {
			$html = '';
			// --- explode by columns ---
			if(isset($setting)) {
				$columns =$setting['hdepth'];
			} else {
				$columns =4;
			}
			
			if ($columns < 1)
				$columns = 1;
			$chunks = $this->seperateColumns($children, $columns, $lang_id);
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
			$blockId = sprintf('pt_ver_menu_idcat_%d', $id);
			$staticBlock = $this->getCmsBlockContent($blockId);
			$blockIdRight = sprintf('pt_ver_menu_idcat_%d_right', $id);
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
	
  public function drawMenuItem($children, $level = 1, $columChunk = 0, $lang_id = 1,$setting) {
		$lang_id = (int)$this->config->get('config_language_id');
		$this->load->model('catalog/category');
		
        $html = '<div class="itemMenu level' . $level . '">';
     
        $countChildren = 0;
        $ClassNoChildren = '';

        foreach ($children as $child) {
            $activeChildCat = $this->model_catalog_category->getCategories($child['category_id']);		
            $activeChildCat = $this->getCategoryByLevelMax($activeChildCat);
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
            $name = str_replace(' ', '&nbsp;', $name);

            if (count($child) > 0) {
                $parameters = null;
                $link = $this->url->link('product/category', "path=".$child['category_id']);
         
                $html.= '<a class="itemMenuName level' . $level . $active . $ClassNoChildren . '" href="' . $link . '"><span>' . $name . '</span></a>';

                if($setting['hlevel'] > 2) {
                    $activeChildren = $this->model_catalog_category->getCategories($child['category_id']);
                    $activeChildren = $this->getCategoryByLevelMax($activeChildren);
                    if (count($activeChildren) > 0) {
                        $html.= '<div class="itemSubMenu level' . $level . '">';
                        //$html.= $this->drawMenuItem($activeChildren, $level + 1);
                        $html.= $this->drawMenuItem($activeChildren, $level + 1,$columChunk, $lang_id, $setting);
                        $html.= '</div>';
                    }
                }

            }
        }
        $html.= '</div>';
        return $html;
    }
	
	 public function getCategoryByLevelMax($cates = NULL) {
        if (count($cates) < 1)
            return array();
        $cateArray = array();
        foreach ($cates as $key => $cate) {
            $cate_id = $cate['category_id'];
			$cateObject = $this->model_catalog_category->getCategory((int) $cate_id);
            $cate_level = $cateObject['top'];
           
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