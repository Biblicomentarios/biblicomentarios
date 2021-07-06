<?php ob_start();?>
<?php
if (Swift_Performance::check_option('server-push',1)){
      $prefix = (defined('SWIFT_PERFORMANCE_PUSH_PREFIX') ? SWIFT_PERFORMANCE_PUSH_PREFIX : '');
      $server_push = array();

      if (Swift_Performance::check_option('enable-cdn', 1)){
            $siteurl = apply_filters('style_loader_src', Swift_Performance::site_url(), 'swift-performance-server-push');
      }
      else {
            $siteurl =trailingslashit(Swift_Performance::home_dir());
      }

      if (Swift_Performance::check_option('merge-styles',1) && Swift_Performance::check_option('critical-css',1) && Swift_Performance::check_option('inline_critical_css',0)){
            $server_push[] = '<'. $siteurl .'%{cache_base_uri}e/css/' . apply_filters('swift_performance_critical_css_filename', $prefix . '%PREFIX%critical.css') . '>; rel=preload; as=style';
      }
      if (Swift_Performance::check_option('merge-styles',1) && Swift_Performance::check_option('inline_full_css',0) && Swift_Performance::check_option('load-full-css-on-scroll',0)){
            $server_push[] = '<'. $siteurl .'%{cache_base_uri}e/css/' . apply_filters('swift_performance_css_filename', $prefix . '%PREFIX%full.css', 'all') . '>; rel=preload; as=style';
      }
      if (Swift_Performance::check_option('merge-scripts',1) && Swift_Performance::check_option('inline-merged-scripts',0)){
            $server_push[] = '<'. $siteurl .'%{cache_base_uri}e/js/' . apply_filters('swift_performance_js_filename', $prefix . '%PREFIX%scripts.js') . '>; rel=preload; as=script';
      }
}

$cookies          = (array)Swift_Performance::get_option('exclude-cookies');
$cookie_list      = (!empty($cookies) ? '|' . trim(implode('|', $cookies), '|') : '');
$excluded_ua      = array_filter((array)Swift_Performance::get_option('exclude-useragents'));

?>
<?php if (Swift_Performance::check_option('proxy-cache', 1) && Swift_Performance::is_feature_available('proxy_cache')) :?>
<IfModule mod_headers.c>
    <filesMatch "\.(html|htm)$">
        Header set Cache-Control "s-maxage=<?php echo Swift_Performance::get_option('proxy-cache-maxage');?>, max-age=0, public, must-revalidate"
    </filesMatch>
</IfModule>
<?php endif;?>

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase <?php echo parse_url(home_url('/'), PHP_URL_PATH)."\n";?>
<?php if (!empty($excluded_ua)):?>
RewriteCond %{HTTP_USER_AGENT} !(<?php echo str_replace(' ','+', implode('|', $excluded_ua));?>) [NC]
<?php endif;?>
RewriteCond %{REQUEST_METHOD} !POST
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP:Cookie} !^.*(wordpress_logged_in<?php echo $cookie_list;?>).*$
RewriteCond %{REQUEST_URI} !^/<?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>([^/]*)/assetproxy
<?php if (Swift_Performance::check_option('mobile-support',1)):?>
RewriteCond %{HTTP_USER_AGENT} (Mobile|Android|Silk|Kindle|BlackBerry|Opera+Mini|Opera+Mobi) [NC]
RewriteCond <?php echo trailingslashit(self::get_option('cache-path')).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/mobile/unauthenticated/index.html -f
RewriteRule (.*) <?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/mobile/unauthenticated/index.html [L]

<?php if (Swift_Performance::check_option('server-push',1) && !empty($server_push)):?>
<IfModule mod_headers.c>
RewriteCond %{HTTP_USER_AGENT} (Mobile|Android|Silk|Kindle|BlackBerry|Opera+Mini|Opera+Mobi) [NC]
RewriteRule (.*)/mobile/unauthenticated/(.*).html - [E=cache_base_uri:$1]
Header set Link "<?php echo implode(',',array_map(function($str){return str_replace('%PREFIX%', 'mobile-', $str);}, $server_push))?>" env=cache_base_uri
</IfModule>
<?php endif;?>

<?php if (!empty($excluded_ua)):?>
RewriteCond %{HTTP_USER_AGENT} !(<?php echo str_replace(' ','+', implode('|', $excluded_ua));?>) [NC]
<?php endif;?>
RewriteCond %{REQUEST_METHOD} !POST
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP:Cookie} !^.*(wordpress_logged_in).*$
RewriteCond %{REQUEST_URI} !^/<?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>([^/]*)/assetproxy
RewriteCond %{HTTP_USER_AGENT} !(Mobile|Android|Silk|Kindle|BlackBerry|Opera+Mini|Opera+Mobi) [NC]
RewriteCond <?php echo trailingslashit(self::get_option('cache-path')).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/desktop/unauthenticated/index.html -f
RewriteRule (.*) <?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/desktop/unauthenticated/index.html [L]

<?php if (Swift_Performance::check_option('server-push',1) && !empty($server_push)):?>
<IfModule mod_headers.c>
RewriteCond %{HTTP_USER_AGENT} !(Mobile|Android|Silk|Kindle|BlackBerry|Opera+Mini|Opera+Mobi) [NC]
RewriteRule (.*)/desktop/unauthenticated/(.*).html - [E=cache_base_uri:$1]
Header set Link "<?php echo implode(',',array_map(function($str){return str_replace('%PREFIX%', 'desktop-', $str);}, $server_push))?>" env=cache_base_uri
</IfModule>
<?php endif;?>

<?php else:?>
RewriteCond <?php echo trailingslashit(self::get_option('cache-path')).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/desktop/unauthenticated/index.html -f
RewriteRule (.*) <?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>%{HTTP_HOST}%{REQUEST_URI}/desktop/unauthenticated/index.html [L]

<?php if (Swift_Performance::check_option('server-push',1) && !empty($server_push)):?>
<IfModule mod_headers.c>
RewriteRule (.*)/desktop/unauthenticated/(.*).html - [E=cache_base_uri:$1]
Header set Link "<?php echo implode(',',array_map(function($str){return str_replace('%PREFIX%', 'desktop-', $str);}, $server_push))?>" env=cache_base_uri
</IfModule>
<?php endif;?>

<?php endif;?>
</IfModule>
<?php return ob_get_clean();?>
