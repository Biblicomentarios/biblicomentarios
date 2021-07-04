<?php
$source_infos = array(
	"sources" => array(			
		"flickr" => array(
			"request" => 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=251f306e19c56bb3c8a2a9b2bd7a076a&text={keyword}&sort={sort}&content_type=&license={license}&extras=date_taken%2C+license%2C+owner_name%2C+url_sq%2C+url_t%2C+url_s%2C+url_m%2C+url_l%2C+url_o%2C+url_q%2C+description&per_page={num}&page={start}',	
			//"request" => 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key={appid}&text={keyword}&sort={sort}&content_type={cont}&license={license}&extras=date_taken%2C+owner_name%2C+icon_server%2C+geo%2C+tags%2C+machine_tags%2C+media%2C+path_alias%2C+url_q%2C+url_n%2C+url_c%2C+url_z%2C+url_sq%2C+url_t%2C+url_s%2C+url_m%2C+url_l%2C+url_o%2C+description&per_page={num}&page={start}',
			"limits" => array("request" => 500, "total" => 4000),		
			"title" => "title",		
			"unique" => "id",	
			"error" => "err",
			"level1" => "photos",
			"selector" => "photo",		
			"categories" => array("media","comments"), 
			"icon" => "", 
			"signup" => "http://www.flickr.com/services/",
			"tags" => array(
				"author" => "ownername", 
				"date" => "datetaken", 
				"description" => "description", 
				),
			"templates" => array(
				"default" => array(
					"name" => "Medium Image",
					"content" => '<div class="owner">{owner}</div><div class="title">{title}</div><div class="license">{license}</div>
					<div class="sizes">
					<div class="square"><strong>SQ</strong> <span>{width_q}</span> x {height_q}px</div>
					<div class="small"><strong>S</strong> <span>{width_s}</span> x {height_s}px</div>
					<div class="medium"><strong>M</strong> <span>{width_m}</span> x {height_m}px</div>
					[IF:height_l]<div class="large"><strong>L</strong> <span>{width_l}</span> x {height_l}px</div>[/IF:height_l]
					</div>
					<div class="name">{ownername}</div><div class="id">{id}</div><div class="date">{datetaken}</div><div class="description">{description}</div><div class="img">{url_s}</div><div class="link">http://www.flickr.com/photos/{owner}/{id}</div>'
				)		
			)				
		),
		
		"pixabay" => array(
			//"request" => 'http://pixabay.com/api/?username=thoefter&key=47b0f746df60162dfc2c&search_term={keyword}&image_type={image_type}',	
			"request" => 'https://pixabay.com/api/?key=281886-58afb50cd9c4019517ce11b78&search_term={keyword}&image_type={image_type}&response_group=high_resolution&per_page={num}&page={start}',			
			"limits" => array("request" => 100, "total" => 1000),		
			"title" => "tags",		
			"unique" => "id",	
			"error" => "error",
			"level1" => "hits",
			"json" => 1,
			"selector" => "node",		
			"categories" => array("media"), 
			"icon" => "", 
			"signup" => "http://pixabay.com",
			"tags" => array(
				"author" => "user", 
				"date" => "datetaken", 
				"description" => "description", 
				),
			"templates" => array(
				"default" => array(
					"name" => "Medium Image",
					"content" => '
					<div class="owner">{user}</div>
					<div class="title">Photo</div>
					<div class="sizes">
						<div class="small"><strong>S</strong> {previewWidth} x {previewHeight}px</div>
						<div class="medium"><strong>M</strong> {webformatWidth} x {webformatHeight}px</div>
						<div class="large"><strong>L</strong> 1280px</div>
					</div>
					<div class="name">{user}</div>
					<div class="id">{id}</div>
					<div class="img_small">{previewURL}</div>
					<div class="img_medium">{webformatURL}</div>
					<div class="img_large">{largeImageURL}</div>
					<div class="link">http://pixabay.com/en/users/{user}</div>'
				)		
			)				
		),		
		
	),		
);

$modulearray = array(
	"flickr" => array(
		"enabled" => 1,
		"name" => "Flickr",
		"options" => array(
			//"appid" => array("value" => "251f306e19c56bb3c8a2a9b2bd7a076a", "display" => "none", "name" => "API Key", "type" => "text", "verified" => 0, "signup" => ''),			
			"license" => array("value" => "4,5,6,7", "name" => "License", "type" => "select", "values" => array("1,2,3,4,5,6,7" => "Non-Commercial Use Only, Attribution Required", "4,5,6,7" => "Commercial Use Allowed, Attribution Required", "7" => "Commercial Use Allowed, No Attribution Required", "0,1,2,3,4,5,6,7" => "All Licenses (not recommended)")),					
			"sort" => array("value" => "relevance", "name" => "Order Images By", "type" => "select", "values" => array("relevance" => "Relevance", "date-posted-asc" => "Date posted, ascending", "date-posted-desc" => "Date posted, descending","date-taken-asc" => "Date taken, ascending", "date-taken-desc" => "Date taken, descending", "interestingness-desc" => "Interestingness, descending", "interestingness-asc" => "Interestingness, ascending")),					
		),	
	),
	"pixabay" => array(
		"enabled" => 1,
		"name" => "Pixabay",
		"options" => array(
			//"appid" => array("value" => "", "name" => "API Key", "type" => "text", "verified" => 0, "signup" => ''),	
			//"username" => array("value" => "", "name" => "Username", "type" => "text"),	
			"image_type" => array("value" => "all", "name" => "Image Types", "type" => "select", "values" => array("all" => "All", "photo" => "Photos", "vector" => "Vector","clipart" => "Clipart")),								
		),	

	),	
	"general" => array(
		"enabled" => 2,
		"name" => "General Settings",
		"options" => array(
			"save_images" => array("value" => 1, "name" => "Save Images to Server", "type" => "checkbox", "info" => "Yes"),	
			"feat_img_size" => array("value" => "medium", "name" => "Featured Image Size", "type" => "select", "values" => array("square" => "Square (150px)", "small" => "Small (240px)", "medium" => "Medium (500px)", "large" => "Large (1024px)")),									
			"default_align" => array("value" => "none", "name" => "Default Image Alignment", "type" => "select", "values" => array("none" => "None", "left" => "Left", "right" => "Right", "center" => "Center")),						
			"attr_location" => array("value" => "caption", "name" => "Attribution Location", "type" => "select", "values" => array("caption" => "WordPress caption next to image", "bottom" => "Bottom of Post", "image" => "Next to the Image")),						
			"items_per_req" => array("value" => "40", "name" => "Results per Search", "type" => "select", "values" => array("20" => "20", "30" => "30", "40" => "40", "50" => "50", "60" => "60", "80" => "80", "100" => "100", "100" => "100", "150" => "150", "200" => "200", "300" => "300")),									
			"wpi_attr" => array("value" => 0, "name" => "Enable ImageInject Link", "type" => "checkbox", "info" => "Yes (adds an unobtrusive link to ImageInject inside the author attribution. If you do not use this please consider sharing in another way, e.g. via social media or by blogging about it. Thanks!)"),	
		)
	),	
	"advanced" => array(
		"enabled" => 2,
		"name" => "Advanced Settings",
		"options" => array(
			"img_template" => array("value" => '<img title="{title} by {author}" alt="{keyword} photo" src="{src}" />', "name" => "Image Template", "type" => "textarea"),	
			"attr_template" => array("value" => '<small>Photo by <a href="{link}" target="_blank">{author}</a> {cc_icon}</small>', "name" => "Attribution Template", "type" => "textarea"),	
			"attr_template_multi" => array("value" => '<small>Photos by {linklist}</small>', "name" => "Multi Photo Insert Attribution", "type" => "textarea"),	
			"filename_template" => array("value" => '{filename}_{keyword}', "name" => "Filename Template", "type" => "textarea"),	
		)
	),		

/*
        X            [url_t] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9_t.jpg
        X            [height_t] => 100
        X            [width_t] => 67
                    [url_s] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9_m.jpg
                    [height_s] => 240
                    [width_s] => 160
        X            [url_n] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9_n.jpg
        X            [height_n] => 320
        X            [width_n] => 213					
                    [url_m] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9.jpg
                    [height_m] => 500
                    [width_m] => 333
        X            [url_z] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9_z.jpg
        X            [height_z] => 640
        X            [width_z] => 427					
                    [url_l] => http://farm3.staticflickr.com/2463/3659583366_39572f3be9_b.jpg
                    [height_l] => 1024
                    [width_l] => 683
                    [url_o] => http://farm3.staticflickr.com/2463/3659583366_b12a21095a_o.jpg
                    [height_o] => 1875
                    [width_o] => 1250
*/
);
?>