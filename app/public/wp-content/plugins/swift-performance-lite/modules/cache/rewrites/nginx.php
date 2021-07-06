<?php ob_start();?>
<?php
if (Swift_Performance::check_option('server-push',1)){
      $prefix = (defined('SWIFT_PERFORMANCE_PUSH_PREFIX') ? SWIFT_PERFORMANCE_PUSH_PREFIX : '');
      $server_push = array();
      if (Swift_Performance::check_option('merge-styles',1) && Swift_Performance::check_option('critical-css',1) && Swift_Performance::check_option('inline_critical_css',0)){
            $server_push[] = '<'. Swift_Performance::home_dir() . str_replace(ABSPATH, '/', trailingslashit(trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR) . '$http_host$request_uri/css/' . apply_filters('swift_performance_critical_css_filename', $prefix . '%PREFIX%critical.css') . '>; rel=preload; as=style';
      }
      if (Swift_Performance::check_option('merge-styles',1) && Swift_Performance::check_option('inline_full_css',0) && Swift_Performance::check_option('load-full-css-on-scroll',0)){
            $server_push[] = '<'. Swift_Performance::home_dir() . str_replace(ABSPATH, '/', trailingslashit(trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR) . '$http_host$request_uri/css/' . apply_filters('swift_performance_css_filename', $prefix . '%PREFIX%full.css', 'all') . '>; rel=preload; as=style';
      }
      if (Swift_Performance::check_option('merge-scripts',1) && Swift_Performance::check_option('inline-merged-scripts',0)){
            $server_push[] = '<'. Swift_Performance::home_dir() . str_replace(ABSPATH, '/', trailingslashit(trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR) . '$http_host$request_uri/js/' . apply_filters('swift_performance_js_filename', $prefix . '%PREFIX%scripts.js') . '>; rel=preload; as=script';
      }
}

$cookies          = (array)Swift_Performance::get_option('exclude-cookies');
$cookie_list      = (!empty($cookies) ? '|' . trim(implode('|', $cookies), '|') : '');
$excluded_ua      = array_filter((array)Swift_Performance::get_option('exclude-useragents'));

?>

set $swift_cache 1;
if ($request_method = POST){
	set $swift_cache 0;
}

if ($args != ''){
	set $swift_cache 0;
}

if ($http_cookie ~* "(wordpress_logged_in<?php echo $cookie_list?>)") {
	set $swift_cache 0;
}

if ($request_uri ~ ^/<?php echo str_replace(ABSPATH, '', trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>([^/]*)/assetproxy) {
      set $swift_cache 0;
}

if (!-f "<?php echo trailingslashit(self::get_option('cache-path')).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>$http_host/$request_uri/desktop/unauthenticated/index.html") {
	set $swift_cache 0;
}

<?php if (Swift_Performance::check_option('mobile-support',1)):?>
set $swift_mobile_cache 1;
if (!-f "<?php echo trailingslashit(self::get_option('cache-path')).SWIFT_PERFORMANCE_CACHE_BASE_DIR; ?>$http_host/$request_uri/mobile/unauthenticated/index.html") {
	set $swift_mobile_cache 0;
}

<?php if (!empty($excluded_ua)):?>
if ($http_user_agent ~* (<?php echo str_replace(' ','+', implode('|', $excluded_ua));?>) {
      set $swift_cache 0;
}
<?php endif;?>


if ($http_user_agent ~* (Mobile|Android|Silk|Kindle|BlackBerry|Opera+Mini|Opera+Mobi)) {
      set $swift_cache "{$swift_cache}{$swift_mobile_cache}";
}

if ($swift_cache = 11){
    rewrite .* /<?php echo str_replace(ABSPATH, '', trailingslashit(trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR); ?>$http_host/$request_uri/mobile/unauthenticated/index.html last;
}

<?php if (Swift_Performance::check_option('server-push',1) && !empty($server_push)):?>
location ~ mobile/unauthenticated/index.html {
	add_header Link "<?php echo implode(',',array_map(function($str){return str_replace('%PREFIX%', 'mobile-', $str);}, $server_push))?>";
}
<?php endif;?>

<?php endif;?>
if ($swift_cache = 1){
    rewrite .* /<?php echo str_replace(ABSPATH, '', trailingslashit(trailingslashit(self::get_option('cache-path'))).SWIFT_PERFORMANCE_CACHE_BASE_DIR); ?>$http_host/$request_uri/desktop/unauthenticated/index.html last;
}

<?php if (Swift_Performance::check_option('server-push',1) && !empty($server_push)):?>
location ~ desktop/unauthenticated/index.html {
	add_header Link "<?php echo implode(',',array_map(function($str){return str_replace('%PREFIX%', 'desktop-', $str);}, $server_push))?>";
}
<?php endif;?>

<?php return ob_get_clean();?>
