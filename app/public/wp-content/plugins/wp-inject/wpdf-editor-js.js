
// to do: test, write short post

var resultCount = (function (module) {
	var state = {};
	var pub = {};

	pub.changeState = function (newstate,module) {
		state[module] = newstate;
		state[module + "run"] = state[module + "run"] + 1;
	};

	pub.getState = function(module) {
		if(state[module] == undefined) {state[module] = 0;}
		return state[module];
	}
	
	pub.getRun = function(module) {
		if(state[module + "run"] == undefined) {state[module + "run"] = 1;}
		return state[module + "run"];
	}		

	return pub;
}());	

function wpdf_set_message(message, error, show_load, replace_load) {

	if(replace_load == 0) {
		var randomnumber = Math.floor(Math.random() * 99) + 1;
		if(error == 1) {var eclass = "wpdf_error";} else {var eclass = "wpdf_msg";}
		jQuery('#wpdf_message_box').append('<div id="wpdf_m' + randomnumber + '" class="' + eclass + '">' + message + '</div>');
		jQuery('#wpdf_message_box #wpdf_m' + randomnumber).slideDown(400);
	} else {
		var randomnumber = replace_load;
		if(error == 1) {
			jQuery('#wpdf_message_box #wpdf_m' + randomnumber).removeClass("wpdf_msg");
			jQuery('#wpdf_message_box #wpdf_m' + randomnumber).addClass("wpdf_error");
		}
		jQuery('#wpdf_message_box #wpdf_m' + randomnumber).html(message);	
	}
	if(show_load == 0) {
		setTimeout(function() {jQuery("#wpdf_m" + randomnumber).slideUp(400, function() {jQuery(this).remove();});}, 5600);
	}
	
	return randomnumber;
}

function wpdf_parse_content(content, attribution, feat_end) {

	//var win = window.dialogArguments || opener || parent || top;
	//win.send_to_editor(content);
	
	if(typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
		if(content != "") {
			if(content == "FIMG") {content = "";}	
		
			var el = wp.element.createElement;
			// var name = 'core/paragraph';
			var name = 'core/freeform';
			insertedBlock = wp.blocks.createBlock(name, {
				content: content,
			});
			wp.data.dispatch('core/editor').insertBlocks(insertedBlock);	
		}		
	} else {
		if(content != "") {
			if(content == "FIMG") {content = "";}
			if(jQuery("#content").is(":visible")) {
				// HTML editor: always place at the end
				document.getElementById('content').value += content;
				document.getElementById('content').value += attribution;
			} else {
				if(wpdf_attr_location == "image" && feat_end != 1) { // determine attribution placement
					content = content + attribution;
					tinyMCE.execCommand('mceInsertContent',false,content);
				} else {
					tinyMCE.execCommand('mceInsertContent',false,content);

					var curcont = tinyMCE.activeEditor.getContent(); // tinymce.editors.content.getContent();
					tinyMCE.execCommand('mceSetContent',false,curcont + attribution);
				}
			}
		}	
	}

}

function wpdf_get_image_size_url(imgurl, imgsize) {

	if(imgsize == "large") {
		imgurl = imgurl.replace('_m.jpg','_b.jpg');	
	} else if(imgsize == "medium") {
		imgurl = imgurl.replace('_m.jpg','.jpg');	
	} else if(imgsize == "orig") {
		imgurl = imgurl.replace('_m.jpg','_o.jpg');	
	} else if(imgsize == "square") {
		imgurl = imgurl.replace('_m.jpg','_q.jpg');	
	}		

	return imgurl;
}

function wpdf_parse_attribution_multi(content, module) {

	if(module == "pixabay" || wpdf_attr_location == "caption") {
		return "";
	} else {
		var template = wpdf_attr_template_multi;	
		template = template.replace('{linklist}', content);

		if(wpdf_wpi_attr == "1") {	
			if (template.indexOf('Photos') > -1) {
				template = template.replace('Photos', '<a style="text-decoration: none;" href="http://wpinject.com/" title="Photo inserted by the ImageInject WordPress plugin">Photos</a>');
			} else {
				template = template + '<small> via <a style="text-decoration: none;" href="http://wpinject.com/" title="Free WordPress plugin to insert images into posts">ImageInject</a></small>';
			}		
		}
		return template;
	}
}

function wpdf_parse_attribution(item, module, nocap) {

	if(wpdf_attr_location == "caption" && nocap != 1) {
		return "";
	}
	
	if(module == "pixabay") {
		return "";
	} else {
		var template = wpdf_attr_template;

		var owner_name = jQuery('#' + item).find(".wpdf_result_item_save .name").text(); 
		var owner_link = jQuery('#' + item).find(".wpdf_result_item_save .link").text(); 
		
		var license = jQuery('#' + item).find(".wpdf_result_item_save .license").text(); 
		if(license == "0") {
			var license_name = "All Rights Reserved";
			var license_link = "";
		} else if(license == "1") {
			var license_name = "Attribution-NonCommercial-ShareAlike License";
			var license_link = "http://creativecommons.org/licenses/by-nc-sa/2.0/";
		} else if(license == "2") {
			var license_name = "Attribution-NonCommercial License";
			var license_link = "http://creativecommons.org/licenses/by-nc/2.0/";
		} else if(license == "3") {
			var license_name = "Attribution-NonCommercial-NoDerivs License";
			var license_link = "http://creativecommons.org/licenses/by-nc-nd/2.0/";
		} else if(license == "4") {
			var license_name = "Attribution License";
			var license_link = "http://creativecommons.org/licenses/by/2.0/";
		} else if(license == "5") {
			var license_name = "Attribution-ShareAlike License";
			var license_link = "http://creativecommons.org/licenses/by-sa/2.0/";
		} else if(license == "6") {
			var license_name = "Attribution-NoDerivs License";
			var license_link = "http://creativecommons.org/licenses/by-nd/2.0/";
		} else {
			var license_name = "";
			var license_link = "";	
		}

		if(license_name != "" && license_link != "") {
			var cc_icon = '<a rel="nofollow" href="' + license_link + '" target="_blank" title="' + license_name + '"><img src="' + wpdf_plugin_url + '/images/cc.png" /></a>';
		} else {
			var cc_icon = '';
		}
		
		template = template.replace('{keyword}', jQuery('#wpdf_keyword').val());		
		template = template.replace('{author}', owner_name);
		template = template.replace('{link}', owner_link);
		template = template.replace('{cc_icon}', cc_icon);
		template = template.replace('{license_name}', license_name);
		template = template.replace('{license_link}', license_link);
		
		if(wpdf_wpi_attr == "1") {
			template = template.replace('Photo', '<a rel="nofollow" style="text-decoration: none;" href="http://wpinject.com/" title="Image inserted by the ImageInject WordPress plugin">Photo</a>');
		}

		return template;
	}
}

function wpdf_parse_template(item, imgsize, img, orientation, module) {

	var template = wpdf_img_template;

	if(img != "" && img != undefined) {
		var imgurl = img;
	} else {
		var imgurl = jQuery('#' + item).find(".wpdf_result_item_save .img").text();
		
		imgurl = wpdf_get_image_size_url(imgurl, imgsize);
	}
	
	template = template.replace('{src}', imgurl);
	template = template.replace('{keyword}', jQuery('#wpdf_keyword').val());
	
	var title = jQuery('#' + item).find(".wpdf_result_item_save .title").text(); 
	var description = jQuery('#' + item).find(".wpdf_result_item_save .description").text(); 
	var owner_name = jQuery('#' + item).find(".wpdf_result_item_save .name").text(); 
	var owner_link = jQuery('#' + item).find(".wpdf_result_item_save .link").text(); 
	var width = jQuery('#' + item).find(".wpdf_result_item_save .sizes ." + imgsize + " span").text(); 
	
	template = template.replace(/\{title\}/g, title);
	template = template.replace('{description}', description);
	template = template.replace('{author}', owner_name);
	template = template.replace('{link}', owner_link);
	template = template.replace('{yoast-keyword}', jQuery('#yoast_wpseo_focuskw').val());

	if(orientation == "left") {
		template = template.replace('<img', '<img class="alignleft"');
	} else if(orientation == "right") {
		template = template.replace('<img', '<img class="alignright"');
	} else if(orientation == "center") {
		template = template.replace('<img', '<img class="aligncenter"');
	}
	
	if(width != "") {
		template = template.replace('<img', '<img width="' + width + '"');	
	}
	
	
	if(wpdf_attr_location == "caption" && width != "" && module != "pixabay") {
	
		if(orientation == "") {
			orientation = wpdf_default_align;
		}	
		
		if(orientation == "left") {
			var aligncaption = "alignleft";
		} else if(orientation == "right") {
			var aligncaption = "alignright";
		} else if(orientation == "center") {
			var aligncaption = "aligncenter";
		} else {
			var aligncaption = "alignnone";
		}	
	
		attribution = wpdf_parse_attribution(item, module, 1);
		template = '[caption id="" align="' + aligncaption + '" width="' + width + '"]' + template + ' ' + attribution + '[/caption]';
	}	
	
	return template;
}

jQuery(document).ready(function($) {

    jQuery('#wpdf_keyword').keypress(function(e){
        if (e.which == 13) { jQuery('#wpdf-searchbutton').click(); return false; }
    });

	jQuery(document).on("click", '.wpdf_select_all', function(e) {	
		var checkedStatus = this.checked;
		var pid = jQuery(this).parent().parent().attr("id");

		jQuery('#' + pid + ' .wpdf_select_item').each(function () {
			jQuery(this).prop('checked', checkedStatus);
		});
		if(checkedStatus == true) {
			jQuery('#wpdf_controls').slideDown(300);
			jQuery('#' + pid + ' .wpdf_result_item' + " img.wpdf_thumb").css('border-color', '#0074A2');
		} else {
			jQuery('#' + pid + ' .wpdf_result_item' + " img.wpdf_thumb").css("border-color", "");
			if(!jQuery('.wpdf_select_item:checked').length) {jQuery('#wpdf_controls').slideUp(300);}
		}	
	});		

	jQuery(document).on("click", 'img.wpdf_thumb', function(e) {	
		jQuery(this).parent().parent().find(".wpdf_select_item").click();
	});	

	jQuery('.wpdf_result_item div input').click(function(e) {
		var toselect = jQuery(this).parent().parent().attr('id');
		var cid = jQuery(this).attr('id');
		var checkedStatus = jQuery("#" + cid).is(':checked'); 

		if(checkedStatus == true) {
			jQuery('#wpdf_controls').slideDown(300);
			jQuery('#' + toselect + " img.wpdf_thumb").css('border-color', '#0074A2');
		} else {
			jQuery('#' + toselect + " img.wpdf_thumb").css("border-color", "");
			if(!jQuery('.wpdf_select_item:checked').length) {jQuery('#wpdf_controls').slideUp(300);}
		}			
	});		
	
	jQuery('a#wpdf_get_seo_keyword').click(function(e) {
		e.preventDefault();	
		jQuery('#wpdf_keyword').val(jQuery('#yoast_wpseo_focuskw').val());
	});		

	jQuery('a#wpdf_get_title').click(function(e) {
		e.preventDefault();	
		jQuery('#wpdf_keyword').val(jQuery('#title').val());
	});	

	jQuery(document).on("click", '.wpdf_remove_results', function(e) {	
		e.preventDefault();	
		jQuery(this).parent().parent().remove();
	});	
	
	jQuery('#wpdf_remove_selected').click(function(e) {
		e.preventDefault();	

		jQuery("input:checkbox[name=wpdf_select_item]:checked").each(function(i) {
			var item = jQuery(this).parent().parent().attr('id');
			jQuery('#' + item).animate({width: 0}, 450, function() {
				jQuery('#' + item).remove();
			});			
		});	

		return false;		
	});			

	/*jQuery('#wpdf_save_keys').click(function(e) {
		e.preventDefault();	
		
		if( !jQuery('#flickr_appid').val()) {return false;}	

		var flickrapikey = jQuery('#flickr_appid').val();

		jQuery('#wpdf_save_keys_form').html('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 16px; height: 16px;margin-bottom: -2px;" /></span>');			
		
		var data = {
			action: 'wpdf_save_keys',
			wpnonce: wpdf_security_nonce.security,
			flickrapi: flickrapikey
		};

		jQuery.ajax ({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json',
			success: function(response) {
				if(response.error != undefined && response.error != "") {
					jQuery('#wpdf_save_keys_form').html(response.error);
				} else {
					jQuery('#wpdf_save_keys_form').remove();
					jQuery('#wpdf_main').show();
				}
			}
		});			

		return false;			
	});	*/	
	
	jQuery('#wpdf_insert_images_normal, #wpdf_insert_images_left, #wpdf_insert_images_right, #wpdf_insert_images_center').click(function(e) {
		e.preventDefault();	

		var orientation = jQuery(this).attr('id').replace('wpdf_insert_images_','');
		var imgsize = jQuery('input:radio[name=wpdf_size]:checked').val();
		var imgcontent = "";
		var attrcontentall = "";
		var pb_err_msg = 0;
		
		if(wpdf_save_images == 1) { // display loading graphic

			var loader = wpdf_set_message('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 16px; height: 16px;margin-bottom: -2px;" /> Saving all images to your server.', 0, 1, 0);
			var all_images = new Array();
			
			jQuery("input:checkbox[name=wpdf_select_item]:checked").each(function() {
				var item = jQuery(this).parent().parent().attr('id');
				var module = item.split('_');
				module = module[3];					
				
				var pb_err = 0;
				if(module == "flickr") {
					var imgurl = jQuery('#' + item).find(".wpdf_result_item_save .img").text(); 
					imgurl = wpdf_get_image_size_url(imgurl, imgsize);
				} else {
					if(imgsize == "square") {
						pb_err = 1;pb_err_msg = 1;
					} else {
						var imgurl = jQuery('#' + item).find(".wpdf_result_item_save .img_" + imgsize).text(); 
					}
				}				

				if(pb_err != 1) {
					all_images.push(imgurl);
				}
			});	
			
			if(pb_err_msg == 1) {
				wpdf_set_message("Error: Pixabay does only small (S) and medium (M) size. Please change your selection.", 1, 0, loader);
			}

			var keyword = jQuery('#wpdf_keyword').val();
			var data = {
				action: 'wpdf_save_multiple_to_server',
				wpnonce: wpdf_security_nonce.security,
				images: all_images,
				post_id: cur_post_id,
				filename: wpdf_filename_template,
				keyword: keyword				
			};

			jQuery.ajax ({
				type: 'POST',
				url: ajaxurl,
				data: data,
				dataType: 'json',
				success: function(response) {
					if(response.error != undefined && response.error != "") {
						wpdf_set_message(response.error, 1, 0, loader);
					} else {
						if(response.result != "" && response.result != undefined) {
							var o = 0;
							jQuery("input:checkbox[name=wpdf_select_item]:checked").each(function() {
								var item = jQuery(this).parent().parent().attr('id');	
								var modulex = item.split('_');
								modulex = modulex[3];								
								imgurl = response.result[o];
								o = o + 1;
								
								//var attrcontent = wpdf_parse_attribution(item);
								var addcontent = wpdf_parse_template(item, imgsize, imgurl, orientation, modulex);
								imgcontent += addcontent;	
								
								if(modulex != "pixabay") {								

									var owner_name = jQuery('#' + item).find(".wpdf_result_item_save .name").text(); 
									var owner_link = jQuery('#' + item).find(".wpdf_result_item_save .link").text(); 			

									attrcontentall += '<a href="' + owner_link + '">' + owner_name + '</a>, ';	
								}
							});	
							
							if(attrcontentall != "") {
								var attr = wpdf_parse_attribution_multi(attrcontentall);
							} else {
								var attr = "";
							}
							
							wpdf_parse_content(imgcontent, attr, 0);
							
							wpdf_set_message("<strong>Selected images have been inserted into the editor.</strong>", 0, 0, loader);								
						} else {
							wpdf_set_message("Error saving images to server:", 1, 0, loader);
						}
					}
				}
			});					

		} else {
			var loader = 0;
			
			jQuery("input:checkbox[name=wpdf_select_item]:checked").each(function() {

				var item = jQuery(this).parent().parent().attr('id');
				
				var modulex = item.split('_');
				modulex = modulex[3];					
				
				var attrcontent = wpdf_parse_attribution(item, modulex, 0);
				var addcontent = wpdf_parse_template(item, imgsize, "", orientation, modulex);
				imgcontent += addcontent;			

				var owner_name = jQuery('#' + item).find(".wpdf_result_item_save .name").text(); 
				var owner_link = jQuery('#' + item).find(".wpdf_result_item_save .link").text(); 			
						
				attrcontentall += '<a href="' + owner_link + '">' + owner_name + '</a>, ';
			});	

			var attr = wpdf_parse_attribution_multi(attrcontentall);

			wpdf_parse_content(imgcontent, attr, 0);
			
			wpdf_set_message("<strong>Selected images have been inserted into the editor.</strong>", 0, 0, loader);			
		}
	});				
	
	jQuery(document).on("click", 'a.wpdf_set_featured', function(e) {	
		e.preventDefault();	

		var jthis = jQuery(this);
		var item = jQuery(this).parents().eq(4).attr('id');
		var module = item.split('_');
		module = module[3];				
		var src = jQuery('#' + item).find(".wpdf_result_item_save .img").text(); 
		var keyword = jQuery('#wpdf_keyword').val();
		
		if(module == "flickr") {
			var src = jQuery('#' + item).find(".wpdf_result_item_save .img").text(); 
			src = wpdf_get_image_size_url(src, wpdf_feat_img_size);
		} else {
			if(wpdf_feat_img_size != "small" && wpdf_feat_img_size != "medium") {wpdf_feat_img_size = "medium";}
			var src = jQuery('#' + item).find(".wpdf_result_item_save .img_" + wpdf_feat_img_size).text(); 
		}		

		var loader = wpdf_set_message('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 16px; height: 16px;margin-bottom: -2px;" /> Loading...', 0, 1, 0);
		jQuery(this).hide();		

		var data = {
			action: 'wpdf_save_to_server',
			wpnonce: wpdf_security_nonce.security,
			src: src,
			post_id: cur_post_id,
			feat_img: 1,
			filename: wpdf_filename_template,
			keyword: keyword
		};		
			
		jQuery.ajax ({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json',
			success: function(response) {
				if(response.error != undefined && response.error != "") {
					jthis.show();
					wpdf_set_message(response.error, 1, 0, loader);
				} else {
					var attribution = wpdf_parse_attribution(item, module, 1); 

					wpdf_parse_content("FIMG", attribution, 1);

					jthis.remove();

					var msg = "<strong>Featured image has been set!</strong> The required attribution has been added to the end of your article.";					
					wpdf_set_message(msg, 0, 0, loader);
				}
			}
		});			

		return false;			
	});	

	jQuery(document).on("click", 'a.wpdf_insert_small, a.wpdf_insert_medium, a.wpdf_insert_large, a.wpdf_insert_square, a.wpdf_insert_orig', function(e) {
		e.preventDefault();	
		
		var loader = wpdf_set_message('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 16px; height: 16px;margin-bottom: -2px;" /> Loading...', 0, 1, 0);

		var imgsize = jQuery(this).attr('class').replace('wpdf_insert_','');	
		var item = jQuery(this).parents().eq(4).attr('id');
		var module = item.split('_');
		module = module[3];		
		var keyword = jQuery('#wpdf_keyword').val();
		var attrcontent = wpdf_parse_attribution(item, module, 0);

		var attrcontent2 = wpdf_parse_attribution(item, module, 1);
		
		if(wpdf_save_images == 1 || module == "pixabay") {
		
			if(module == "flickr") {
				var imgurl = jQuery('#' + item).find(".wpdf_result_item_save .img").text(); 
				imgurl = wpdf_get_image_size_url(imgurl, imgsize);
			} else {
				var imgurl = jQuery('#' + item).find(".wpdf_result_item_save .img_" + imgsize).text(); 
			}
			
			var data = {
				action: 'wpdf_save_to_server',
				wpnonce: wpdf_security_nonce.security,
				src: imgurl,
				post_id: cur_post_id,
				filename: wpdf_filename_template,
				keyword: keyword,
				attr: attrcontent2
			};

			jQuery.ajax ({
				type: 'POST',
				url: ajaxurl,
				data: data,
				dataType: 'json',
				success: function(response) {
					if(response.error != undefined && response.error != "") {
						wpdf_set_message(response.error, 1, 0, loader);
					} else {
						imgurl = response.result;

						var addcontent = wpdf_parse_template(item, imgsize, imgurl, wpdf_default_align, module);
						wpdf_parse_content(addcontent, attrcontent, 0);	

						wpdf_set_message("<strong>Image has been saved to your server and inserted into the editor.</strong>", 0, 0, loader);
					}
				}
			});			
			return false;
		} else {

			var addcontent = wpdf_parse_template(item, imgsize, "", wpdf_default_align, module);
			wpdf_parse_content(addcontent, attrcontent, 0);

			wpdf_set_message("<strong>Image has been inserted into the editor.</strong>", 0, 0, loader);
		}
	});	

	jQuery('.wpdf_result_item').hover(function(e) {
		var r_image = jQuery(this).find(".wpdf_bigger_img");
		var real_imgurl = r_image.attr("data-src"); 
		r_image.attr("src", real_imgurl); 
	});	
	
	jQuery('.wpdf-module').click(function(e) {

		e.preventDefault();

		jQuery("#wpdf-searchbutton").html('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 15px; height: 15px;margin-bottom: -1px;" /> Loading...');
		
		var keyword = jQuery("#wpdf_keyword").val();
		var id_kw = keyword.replace(/ /g, "_"); //encodeURIComponent(keyword);//	
		
		var modules = jQuery(".wpdf_searchwhat input:checkbox:checked").map(function(){
		  return jQuery(this).val();
		}).get();
		
		var allmodules = [];
		jQuery.each(modules, function(index, item) {
			if( !jQuery('#wpdfr-' + item + "-" + id_kw).length ) {
				var module_run = 1;
			} else {
				var module_run = parseInt(jQuery('#wpdf-run-' + id_kw).attr('class')) + 1;
			}		

			var mod = {name: item, module_run: module_run};
			allmodules.push(mod);
		});

		var data = {
			action: 'wpdf_editor',
			wpnonce: wpdf_security_nonce.security,
			modules: allmodules,
			keyword: keyword,
			ajax: 1,
			};
			
		jQuery.ajax ({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json',
			success: function(response) {
			
				jQuery("#wpdf-searchbutton").html('Search');
			
				if(response.error != undefined && response.error != "") {
					wpdf_set_message(response.error, 1, 0, 0);

					jQuery("#" + module).removeAttr("disabled"); 
					jQuery('#' + module + '-load').hide();
				} else {

					for (x in response.result) {

						var module = x;

						if(response.result[x].error != "" && response.result[x].error != undefined) {
							wpdf_set_message(module + " error: " + response.result[x].error, 1, 0, 0);
						} else {
						
							var module_run = response.result[x].modulerun;
							var result_num = resultCount.getState(module);	
					
							if( !jQuery('#wpdfr-' + module + "-" + id_kw).length ) {
								jQuery('#wpdf_results').prepend('<div id="wpdfr-' + module + "-" + id_kw + '"><div id="wpdf-run-' + id_kw + '" class="' + module_run + '" style="display:none;"></div><div class="wpdf-search-title"><input type="checkbox" value="1" class="wpdf_select_all"><span>' + module + '</span> search for "<strong>' + keyword + '</strong>" - <a href="#" class="wpdf_remove_results">Remove</a></div></div>'); // show all results
							} else {
								jQuery('#wpdf-run-' + id_kw).attr('class', module_run);
							}

							for (i in response.result[x]) {

								var clone = jQuery("#wpdf_result_item").clone(true, true);
								clone.attr("id", "wpdf_result_item_" + module + "_" + result_num);					
								clone.find(".wpdf_result_item_save").html(response.result[x][i].content);

								// hide everything except image
								var elem = jQuery('<div>').html(response.result[x][i].content);
								//var imgcheck = elem.find('img').attr('src');							
								//if(imgcheck == undefined || imgcheck == "") {clone.find(".wpdf_set_featured").remove();clone.find(".wpdf_insert_image").remove();clone.find(".wpdf_insert_image_link").remove();}
								//var linkcheck = elem.find('a').attr('href');							
								//if(linkcheck == undefined || linkcheck == "") {clone.find(".wpdf_insert_link").remove();clone.find(".wpdf_insert_image_link").remove();}
					
								if(module == "flickr") {
									var imgurl = elem.find('.img').text();	
									var imgurl_s = imgurl.replace('_m.jpg','_s.jpg');	
									var imgurl_m = imgurl.replace('_m.jpg','.jpg');		
									var datetaken = " on " + elem.find('.date').text();
								}
								
								if(module == "pixabay") {
									var imgurl_s = elem.find('.img_small').text();	
									var imgurl_m = elem.find('.img_medium').text();	
									var datetaken = "";								
								}

								var img_link = elem.find('.link').text();
								var title = elem.find('.title').text();
								var owner = elem.find('.name').text();

								var sizelink = "";						
								elem.find('.sizes div').each(function () {
									var size_text = jQuery(this).html();
									var size_class = jQuery(this).attr('class');
									
									sizelink += '<a title="Click to insert ' + size_class + ' image" class="wpdf_insert_' + size_class + '" href="#">' + size_text + '</a>';
								});		
									
								if(sizelink != "") {	
									sizelink += '<a title="Click to set featured image" class="wpdf_set_featured" href="#" >Featured Image</a>';

									if(module == "pixabay") {var pbl = ' (<a href="http://pixabay.com" target="_blank">Pixabay</a>)';} else {var pbl = "";}
									
									clone.find(".wpdf_result_item_content").html('<img class="wpdf_thumb" src="'+ imgurl_s +'" /><div class="wpdf_big_container"><div class="wpdf_bigger"><div class="wpdf_insert_links">' + sizelink + '</div><a href="' + img_link + '" target="_blank"><img class="wpdf_bigger_img" src="" data-src="'+ imgurl_m +'" /></a><br/><small><em>' + title + '</em> by <a href="' + img_link + '" target="_blank">' + owner + '</a>' + pbl + datetaken + '</small></div></div>');
									
									clone.find(".wpdf_select_item_o").attr('name', "wpdf_select_item");
									clone.find(".wpdf_select_item_o").attr('id', "wpdf_select_" + module + "_" + result_num);							
									clone.find(".wpdf_select_item_o").attr("class", "wpdf_select_item");

									clone.appendTo("#wpdfr-" + module + "-" + id_kw);							
									result_num = result_num + 1;
								}
							}
	
							resultCount.changeState(result_num, module);
						}
					}
					
					if(!jQuery('#wpdf_share_box').is(":visible")) {
						jQuery('#wpdf_share_box').slideDown(400);
					}				
				}
			}
		});			

		return false;
	});		
});