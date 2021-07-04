<?php
class mapsModelUms extends modelUms {
	function __construct() {
		$this->_setTbl('maps');
	}
	public function getAllMaps($d = array(), $withMarkers = false, $markersWithGroups = false, $withShapes = false, $withHeatmap = false){
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameUms::_()->getTable('maps')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		if(isset($d['orderBy']) && !empty($d['orderBy'])) {
			frameUms::_()->getTable('maps')->orderBy( $d['orderBy'] );
		}
		// global $wpdb;
		// $maps = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_maps AS toe_m WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
		global $wpdb;
		$maps = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_maps", ARRAY_A);
		//$maps = frameUms::_()->getTable('maps')->get('*', $d);
		if($maps && isset($d['simple']) && $d['simple']) {
			return $maps;
		}
		$markerModule = frameUms::_()->getModule('marker');
		foreach($maps as &$map) {
			$map['html_options'] = utilsUms::unserialize($map['html_options']);
			$map['params'] = utilsUms::unserialize($map['params']);
			if($withMarkers) {
				$map['markers'] = $markerModule->getModel()->getMapMarkers($map['id'], $markersWithGroups);
			}
			if($withShapes && frameUms::_()->getModule('shape')) {
				$map['shapes'] = frameUms::_()->getModule('shape')->getModel()->getMapShapes($map['id']);
			}
			if($withHeatmap && frameUms::_()->getModule('heatmap')) {
				$map['heatmap'] = frameUms::_()->getModule('heatmap')->getModel()->getByMapId($map['id']);
			}
			$map = $this->_afterSimpleGet( $map );
		}
		return $maps;
	}
	private function _afterSimpleGet($map) {
		if($map['params'] && isset($map['params']['map_stylization'])) {
			$map['params']['map_stylization_data'] = $this->getModule()->getStylizationByName( $map['params']['map_stylization'] );
		}
		if($map['params'] && isset($map['params']['center_on_cur_user_pos_icon'])) {
			$icon_data = frameUms::_()->getModule('icons')->getModel()->getIconFromId($map['params']['center_on_cur_user_pos_icon']);
			$map['params']['center_on_cur_user_pos_icon_path'] = $icon_data['path'];
		}
		// This is for posibility to show multy maps with same ID on one page
		$map['original_id'] = $map['id'];
		$map['view_id'] = $map['id']. '_'. mt_rand(1, 99999);
		$map['view_html_id'] = 'ultimate_maps_'. $map['view_id'];

		$map['params']['view_id'] = $map['view_id'];
		$map['params']['view_html_id'] = $map['view_html_id'];
		$map['params']['id'] = $map['id'];
		$map = apply_filters('ums_after_simple_get', $map);
		return $map;
	}
	public function getParamsList() {
		$mapOptKeys = dispatcherUms::applyFilters('mapParamsKeys',
				array('width_units', 'membershipEnable', 'adapt_map_to_screen_height',
					'type' /*used "map_type" insted - as this was already nulled*/, 'map_type', 'map_display_mode', 'map_center', 'language',
					'enable_zoom', 'enable_mouse_zoom' /*we used "mouse_wheel_zoom" insted of this - same reason as prev. one*/, 'mouse_wheel_zoom',
					'zoom_type', 'zoom', 'zoom_mobile', 'zoom_min', 'zoom_max', 'navigation_bar_mode', 'zoom_control', 'dbl_click_zoom',
					'street_view_control', 'pan_control', 'overview_control', 'draggable', 'map_stylization',
					'marker_title_color', 'marker_title_size', 'marker_title_size_units',
					'marker_desc_size', 'marker_desc_size_units', 'hide_marker_tooltip', 'center_on_cur_marker_infownd',
					'marker_infownd_type', 'marker_infownd_hide_close_btn', 'marker_infownd_width', 'marker_infownd_width_units',
					'marker_infownd_height', 'marker_infownd_height_units', 'marker_infownd_bg_color',
					'marker_clasterer', 'marker_clasterer_icon', 'marker_clasterer_icon_width', 'marker_clasterer_icon_height', 'marker_clasterer_grid_size', 'marker_clasterer_background_color', 'marker_clasterer_border_color', 'marker_clasterer_text_color',
					'marker_filter_color', 'marker_filter_button_title', 'marker_hover',

               'slider_simple_table_width_dimension', 'slider_simple_table_width_title', 'slider_simple_table_width_address',
               'slider_simple_table_width_description', 'slider_simple_table_width_getdirection',

					// Maybe PRO params - but let them be here - to avoid dublications
					'markers_list_type', 'markers_list_color', 'is_static', 'hide_empty_block', 'autoplay_slider', 'slide_duration'));
		return $mapOptKeys;
	}
	public function getHtmlOptionsList() {
		return array('width', 'height'/*, 'align', 'margin', 'border_width', 'border_color'*/);
	}
	public function prepareParams($params){
		$htmlKeys = $this->getHtmlOptionsList();
		$htmlOpts = array();
		foreach($htmlKeys as $k){
			$htmlOpts[$k] = isset($params[$k]) ? $params[$k] : null;
		}
		$mapOptKeys = $this->getParamsList();
		$mapOpts = array();
		foreach($mapOptKeys as $k){
			$mapOpts[$k] = isset($params[$k]) ? $params[$k] : null;
		}
		$insert = array(
			'title'			=> trim($params['title']),
			'html_options'	=> utilsUms::serialize($htmlOpts),
			'params'		=> utilsUms::serialize($mapOpts),
			'create_date'	=> date('Y-m-d H:i:s')
		);
		if(isset($params['engine_from_req'])) {
			$insert['engine'] = $params['engine_from_req'];
		}
		return $insert;
	}
	private function _validateSaveMap($map) {
		if(empty($map['title'])) {
			$this->pushError(__('Please enter Map Name'), 'map_opts[title]', UMS_LANG_CODE);
		}
		return !$this->haveErrors();
	}
	public function updateMap($params){
		$data = $this->prepareParams($params);
		if($this->_validateSaveMap($data)) {
			global $wpdb;
			$tableName = $wpdb->prefix . "ums_maps";
			$data_update = array('title' => $data['title'] ,'html_options' => $data['html_options'], 'params' => $data['params']);
			$data_where = array('id' => $params['id']);
			$res = $wpdb->update($tableName , $data_update, $data_where);
			if($res) {
				dispatcherUms::doAction('afterMapUpdate', $params['id'], $data);
			}
			return $res;
		}
		return false;
	}
	public function saveNewMap($params){
		if(!empty($params)) {
			$insertData = $this->prepareParams($params);
			if($this->_validateSaveMap($insertData)) {
				global $wpdb;
				$tableName = $wpdb->prefix . "ums_maps";
				$title = $insertData['title'];
				$html_options = $insertData['html_options'];
				$params = $insertData['params'];
				$create_date = $insertData['create_date'];
				$res = $wpdb->insert($tableName, array(
				    'title' => $title,
						'html_options' => $html_options,
						'params' => $params,
						'create_date' => $create_date,
				));
				if($res) {
					$newMapId = $wpdb->insert_id;;
				}
				if($newMapId){
					dispatcherUms::doAction('afterMapInsert', $newMapId, $params);
					return $newMapId;
				} else {
					$this->pushError(frameUms::_()->getTable('maps')->getErrors());
				}
			}
		} else
			$this->pushError(__('Empty Params', UMS_LANG_CODE));
		return false;
	}
	public function remove($mapId){
		$mapId = (int) $mapId;
		if(!empty($mapId)) {
			global $wpdb;
			frameUms::_()->getModule('marker')->getModel()->removeMarkersFromMap($mapId);
			$tableName = $wpdb->prefix . "ums_maps";
			$data_where = array('id' => $mapId);
			return $res = $wpdb->delete($tableName, $data_where);
		} else
			$this->pushError (__('Invalid Map ID', UMS_LANG_CODE));
		return false;
	}
	public function removeGroup($ids) {
		 $ids = array_map('intval', $ids);
		 global $wpdb;
		 foreach ($ids as $id) {
				$tableName = $wpdb->prefix . "ums_maps";
				$data_where = array(
					 'id' => $id
				);
				$res = $wpdb->delete($tableName, $data_where);
		 }
		 if ($res) {
				return true;
		 }
	}
	public function cloneMapGroup($ids) {
		if(!is_array($ids)) $ids = array($ids);

		$ids = array_filter(array_map('intval', $ids));	// Remove all empty values

		if(!empty($ids)) {
			foreach($ids as $id) {
				global $wpdb;
				$map = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_maps AS toe_m WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
				//if($map = frameUms::_()->getTable('maps')->get('*', array('id' => (int)$id), '', 'row')) {
				if($map) {
					$mapId = $map['id'];
					$map = $this->prepareDataToClone($map, false, true);

					global $wpdb;
					$tableName = $wpdb->prefix . "ums_maps";
					$res = $wpdb->insert($tableName, array(
					    'title' => $map['title'],
							'engine' => $map['engine'],
							'html_options' => $map['html_options'],
							'params' => $map['params'],
							'create_date' => $map['create_date'],
					));
					if($res) {
						$clonedMapId = $wpdb->insert_id;;
					}
					//if($clonedMapId = frameUms::_()->getTable('maps')->insert($map)) {
					if($clonedMapId) {
						// Markers
						$markers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare("map_id = %s", $mapId) . " ORDER BY sort_order ASC", ARRAY_A);
						if($markers) {
							foreach ($markers as $marker) {
								$marker = $this->prepareDataToClone($marker, $clonedMapId, true);

								$tableName = $wpdb->prefix . "ums_markers";
								$dbMarkerRes = $wpdb->insert($tableName, array(
								    'title' => $marker['title'],
										'description' => $marker['description'],
										'icon' => $marker['icon'],
										'address' => $marker['address'],
				            'coord_x' => $marker['coord_x'],
				            'coord_y' => $marker['coord_y'],
				            'params' => $marker['params'],
				            'map_id' => $clonedMapId,
				            'marker_group_id' => $marker['marker_group_id'],
				            'create_date' => $marker['create_date'],
				            'sort_order' => $marker['sort_order'],
								));
								if($dbMarkerRes) {
									$dbMarkerResId = $wpdb->insert_id;;
								}
								// if(!frameUms::_()->getTable('marker')->insert($marker)) {
								if(!$dbMarkerRes) {
									$this->pushError(frameUms::_()->getTable('marker')->getErrors());
									return $this->haveErrors();	// To break foreach cycle
								}
							}
						} else
							$this->pushError(frameUms::_()->getTable('marker')->getErrors());
						// Shapes
						if(frameUms::_()->getModule('shape') && $shapes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_shapes WHERE " . $wpdb->prepare("map_id = %s", $mapId) . " ORDER BY sort_order ASC", ARRAY_A) ) {
							foreach ($shapes as $shape) {
								$shape = $this->prepareDataToClone($shape, $clonedMapId);
									global $wpdb;
									$tableName = $wpdb->prefix . "ums_shapes";
									$dbRes = $wpdb->insert($tableName, array(
									    'id' => $shape['id'],
											'title' => $shape['title'],
											'description' => $shape['description'],
											'coords' => $shape['coords'],
											'type' => $shape['type'],
											'map_id' => $shape['map_id'],
											'params' => $shape['params'],
											'sort_order' => $shape['sort_order'],
									));
									if($dbRes) {
										$dbResId = $wpdb->insert_id;;
									}
								if(!$dbRes) {
								//if(!frameUms::_()->getTable('shape')->insert($shape)) {
									$this->pushError(frameUms::_()->getTable('shape')->getErrors());
									return $this->haveErrors();	// To break foreach cycle
								}
							}
						} else
							$this->pushError(frameUms::_()->getTable('shape')->getErrors());
						// Heatmap layer
						if(frameUms::_()->getModule('heatmap') && $heatmap = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_heatmaps WHERE " . $wpdb->prepare("map_id = %s", $mapId), ARRAY_A)) {
							$heatmap = $this->prepareDataToClone($heatmap, $clonedMapId);

							// if(!frameUms::_()->getTable('heatmap')->insert($heatmap))
							// 	$this->pushError(frameUms::_()->getTable('heatmap')->getErrors());
						} else
							$this->pushError(frameUms::_()->getTable('heatmap')->getErrors());
					} else
						$this->pushError(frameUms::_()->getTable('map')->getErrors());

				} else
					$this->pushError(frameUms::_()->getTable('map')->getErrors());
			}
		} else
			$this->pushError(__('Invalid ID', UMS_LANG_CODE));

		return $this->haveErrors();
	}
	public function prepareDataToClone($data, $newMapId = false, $date = false) {
		unset($data['id']);
		if($newMapId)
			$data['map_id'] = $newMapId;	// We do not need to set map id for maps
		if($date)
			$data['create_date'] = date('Y-m-d H:i:s');	// We need to set date here only for maps amd markers
		else
			unset($data['create_date']);
		foreach($data as &$d) {
			//$d = addslashes($d);
		}
		return $data;
	}
	public function getMapByTitle($title) {
		$map = frameUms::_()->getTable('maps')->get('*', array('title' => $title), '', 'row');
		if(!empty($map)) {
			$map['html_options'] = utilsUms::unserialize($map['html_options']);
			$map['params']= utilsUms::unserialize($map['params']);
			$map = $this->_afterSimpleGet( $map );
			return $map;
		}
		return false;
	}
	public function getMapById($id = false, $withMarkers = true, $withGroups = false, $withShapes = true, $withHeatmap = true){
		if(!$id){
			return false;
		}
		//$map = frameUms::_()->getTable('maps')->get('*', array('id' => (int)$id), '', 'row');
		global $wpdb;
		$map = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_maps AS toe_m WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);

		if(!empty($map)){
			if($withMarkers){
			   $map['markers'] = frameUms::_()->getModule('marker')->getModel()->getMapMarkers($map['id'], $withGroups);
			}
			if($withShapes && frameUms::_()->getModule('shape')) {
				$map['shapes'] = frameUms::_()->getModule('shape')->getModel()->getMapShapes($map['id']);
			}
			// if($withHeatmap && frameUms::_()->getModule('heatmap')) {
			// 	$map['heatmap'] = frameUms::_()->getModule('heatmap')->getModel()->getByMapId($map['id']);
			// }
			$map['html_options'] = utilsUms::unserialize($map['html_options']);
			$map['params']= utilsUms::unserialize($map['params']);
			$map = $this->_afterSimpleGet( $map );
			return $map;
		} else
			$this->pushError (__('Invalid Map ID', UMS_LANG_CODE));
		return false;
	}
	public function existsId($id){
		if($id){
			global $wpdb;
			$map = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_maps AS toe_m WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
			//$map = frameUms::_()->getTable('maps')->get('*', array('id' => (int)$id), '', 'row');
			if(!empty($map)){
				return true;
			}
		}
		return false;
	}
	public function constructMapOptions(){
		$params = array();
		$params['zoom']=array();
		for($i = 0; $i < 22; $i++){
			$params['zoom'][$i] = $i;
		}
		$params['type']= array(
			'ROADMAP'=>'Map',
			'TERRAIN'=>'Relief',
			'HYBRID'=>'Hybrid',
			'SATELLITE'=>'Satellite',
		);
		$params['language'] = array(
			'ar'=>'ARABIC',
			'bg'=>'BULGARIAN',
			'cs'=>'CZECH',
			'da'=>'DANISH',
			'de'=>'GERMAN',
			'el'=>'GREEK',
			'en'=>'ENGLISH',
			'en-AU'=>'ENGLISH (AUSTRALIAN)',
			'en-GB'=>'ENGLISH (GREAT BRITAIN)',
			'es'=>'SPANISH',
			'fa'=>'FARSI',
			'fil'=>'FILIPINO',
			'fr'=>'FRENCH',
			'hi'=>'HINDI',
			'hu'=>'HUNGARIAN',
			'id'=>'INDONESIAN',
			'it'=>'ITALIAN',
			'ja'=>'JAPANESE',
			'kn'=>'KANNADA',
			'ko'=>'KOREAN',
			'lv'=>'LATVIAN',
			'nl'=>'DUTCH',
			'no'=>'NORWEGIAN',
			'pt'=>'PORTUGUESE',
			'pt-BR'=>'PORTUGUESE (BRAZIL)',
			'pt-PT'=>'PORTUGUESE (PORTUGAL)',
			'rm'=>'ROMANSCH',
			'ru'=>'RUSSIAN',
			'sv'=>'SWEDISH',
			'zh-CN'=>'CHINESE (SIMPLIFIED)',
			'zh-TW'=>'CHINESE (TRADITIONAL)',
		);
		$params['align'] = array('top' => 'top', 'right' => 'right', 'bottom' => 'bottom', 'left' => 'left');
		$params['display_mode'] = array('map' => 'Display Map', 'popup' => 'Display Map Icon');
		return $params;
	}
	public function getCount($d = array()) {
		return frameUms::_()->getTable('maps')->get('COUNT(*)', $d, '', 'one');
	}
	public function resortMarkers($d = array()) {
		$mapId = isset($d['map_id']) ? (int) $d['map_id'] : 0;
		$markersList = isset($d['markers_list']) ? $d['markers_list'] : false;
		if(!$markersList && $mapId) {
			$markersList = frameUms::_()->getModule('marker')->getModel()->getMapMarkersIds($mapId);
		}
		if($markersList) {
			$i = 1;
			foreach($markersList as $mId) {
				global $wpdb;
				$tableName = $wpdb->prefix . "ums_markers";
				$data_update = array('sort_order' => $i++);
				$data_where = array('id' => $mId);
				$res = $wpdb->update($tableName , $data_update, $data_where);
			}
		}
		return true;
	}
	public function resortShapes($d = array()) {
		if(!frameUms::_()->getModule('shape'))
			return true;	// Why always true?
		$mapId = isset($d['map_id']) ? (int) $d['map_id'] : 0;
		$shapesList = isset($d['shapes_list']) ? $d['shapes_list'] : false;
		if(!$shapesList && $mapId) {
			$shapesList = frameUms::_()->getModule('shape')->getModel()->getMapShapesIds($mapId);
		}
		if($shapesList) {
			$i = 1;
			foreach($shapesList as $mId) {
				global $wpdb;
				$tableName = $wpdb->prefix . "ums_shapes";
				$data_update = array('sort_order' => $i++);
				$data_where = array('id' => $mId);
				$res = $wpdb->update($tableName , $data_update, $data_where);
			}
		}
		return true;
	}
	public function changeEngine($d) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		$d['engine'] = isset($d['engine']) ? $d['engine'] : false;
		if($id) {
			if($d['engine']) {
				global $wpdb;
				$tableName = $wpdb->prefix . "ums_maps";
				$data_update = array('engine' => $d['engine']);
				$data_where = array('id' => $id);
				return $res = $wpdb->update($tableName , $data_update, $data_where);
			} else
				$this->pushError (__('Invalid Engine', UMS_LANG_CODE));
		} else
			$this->pushError (__('Invalid ID', UMS_LANG_CODE));
		return false;
	}
	public function getUsedEngines() {
		global $wpdb;
		return $wpdb->get_var("SELECT `engine`, COUNT(*) AS `total` FROM {$wpdb->prefix}ums_maps GROUP BY `engine`");
	}
	public function getTotalCountBySearch($search) {
      global $wpdb;
      if (!empty($search)) {
         $count = (int)$wpdb->get_var("SELECT COUNT(*) AS total FROM {$wpdb->prefix}ums_maps WHERE " . $wpdb->prepare("(id = %s OR label = %s)", $search, $search));
      }
      else {
         $count = (int)$wpdb->get_var("SELECT COUNT(*) AS total FROM {$wpdb->prefix}ums_maps");
      }
      return $count;
   }
   public function getListForTblBySearch($search, $limitStart, $rowsLimit) {
      global $wpdb;
      if (!empty($search)) {
         $data = $wpdb->get_results("SELECT id, title, create_date FROM {$wpdb->prefix}ums_maps WHERE " . $wpdb->prepare(" (id = %s OR label = %s) ORDER BY id ASC LIMIT %1s,%1s", $search, $search, (int)$limitStart, (int)$rowsLimit) , ARRAY_A);
      }
      else {
         $data = $wpdb->get_results("SELECT id, title, create_date FROM {$wpdb->prefix}ums_maps " . $wpdb->prepare(" ORDER BY id ASC LIMIT %1s,%1s", (int)$limitStart, (int)$rowsLimit) , ARRAY_A);
      }
      return $data;
   }
}
