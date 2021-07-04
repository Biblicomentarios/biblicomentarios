<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IRP_Utils {

    function format($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        if($v1 || $v2 || $v3 || $v4 || $v5) {
            $message=sprintf($message, $v1, $v2, $v3, $v4, $v5);
        }
        return $message;
    }
    function startsWith($haystack, $needle) {
        $result=FALSE;
        if (is_array($needle)) {
            foreach($needle as $w) {
                if ($this->startsWith($haystack, $w)) {
                    $result=TRUE;
                    break;
                }
            }
        } elseif($needle!='') {
            if(is_array($haystack)) {
                foreach($haystack as $h) {
                    if($this->startsWith($h, $needle)) {
                        $result=TRUE;
                        break;
                    }
                }
            } elseif($haystack!='') {
                $length = strlen($needle);
                $result = (substr($haystack, 0, $length) === $needle);
            }
        }
        return $result;
    }
    function endsWith($haystack, $needle) {
        $result=FALSE;
        if (is_array($needle)) {
            foreach($needle as $w) {
                if ($this->endsWith($haystack, $w)) {
                    $result=TRUE;
                    break;
                }
            }
        } elseif($needle!='') {
            if(is_array($haystack)) {
                foreach($haystack as $h) {
                    if($this->endsWith($h, $needle)) {
                        $result=TRUE;
                        break;
                    }
                }
            } elseif($haystack!='') {
                $length = strlen($needle);
                $start = $length * -1; //negative
                $result=(substr($haystack, $start) === $needle);
            }
        }
        return $result;
    }
    function substr($text, $start=0, $end=-1) {
        if($end<0) {
            $end=strlen($text);
        }
        $length=$end-$start;
        return substr($text, $start, $length);
    }

    //WOW! $end is passed as reference due to we can change it if we found \n character after
    //substring to avoid having these characters after or before
    function substrln($text, $start=0, &$end=-1) {
        if($end<0) {
            $end=strlen($text);
        }

        do {
            $loop=FALSE;
            $c=substr($text, $end, 1);
            if($c=="\n" || $c=="\r" || $c==".") {
                $end += 1;
                $loop=TRUE;
            }
        } while($loop);

        $length=$end-$start;
        return substr($text, $start, $length);
    }

    function toCommaArray($array, $isNumeric=TRUE, $isTrim=TRUE) {
        if(is_string($array)) {
            if(trim($array)=='') {
                $array=array();
            } else {
                $array=explode(',', $array);
            }
        } elseif(is_numeric($array)) {
            $array=array($array);
        }
        if(!is_array($array)) {
            $array=array();
        }
        for($i=0; $i<count($array); $i++) {
            if($isTrim) {
                $array[$i]=trim($array[$i]);
            }
            if($isNumeric) {
                $array[$i]=floatval($array[$i]);
            }
        }
        return $array;
    }
    //verifica se il parametro needle è un elemento dell'array haystack
    //se il parametro needle è a sua volta un array verifica che almeno un elemento
    //sia contenuto all'interno dell'array haystack
    function inArray($needle, $haystack) {
        if (is_string($haystack)) {
            //from string to numeric array
            $temp = explode(',', $haystack);
            $haystack = array();
            foreach ($temp as $v) {
                $v = trim($v);
                $v = intval($v);
                if ($v > 0) {
                    $haystack[] = $v;
                }
            }
        }

        $result = FALSE;
        foreach ($haystack as $v) {
            $v = intval($v);
            //if one element of the array have -1 value means i select "all" option
            if ($v < 0) {
                $result = TRUE;
                break;
            }
        }

        if ($result) {
            return TRUE;
        }

        $result = FALSE;
        if (is_array($needle)) {
            foreach ($needle as $v) {
                $v = trim($v);
                $v = intval($v);
                if (in_array($v, $haystack)) {
                    $result = TRUE;
                    break;
                }
            }
        } else {
            //built-in comparison
            $result = in_array($needle, $haystack);
        }
        return $result;
    }

    function is($name, $compare, $default='', $ignoreCase=TRUE) {
        $what=$this->qs($name, $default);
        $result=FALSE;
        if(is_string($compare)) {
            $compare=explode(',', $compare);
        }
        if($ignoreCase){
            $what=strtolower($what);
        }

        foreach($compare as $v) {
            if($ignoreCase){
                $v=strtolower($v);
            }
            if($what==$v) {
                $result=TRUE;
                break;
            }
        }
        return $result;
    }

    public function twitter($name) {
        ?>
        <a href="https://twitter.com/<?php echo $name?>" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @<?php echo $name?></a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    <?php
    }

    function aqs($prefix, $defaults=array()) {
        global $irp;

        $removePrefix=TRUE;
        $args=array();
        $array=$this->merge(TRUE, $_POST, $_GET);
        foreach($array as $k=>$v) {
            if($this->startsWith($k, $prefix)) {
                if($removePrefix) {
                    $k=substr($k, strlen($prefix));
                }
                $args[$k]=$v;
            }
        }
        $args=$irp->Utils->parseArgs($args, $defaults);
        return $args;
    }
    function iqs($name, $default = 0) {
        return intval($this->qs($name, $default));
    }
    //per ottenere un campo dal $_GET oppure dal $_POST
    function qs($name, $default = '') {
        $result = $default;
        if (isset($_GET[$name])) {
            $result = $_GET[$name];
        } elseif (isset($_POST[$name])) {
            $result = $_POST[$name];
        }

        if (is_string($result)) {
            $result = urldecode($result);
            $result = trim($result);
        }

        return htmlspecialchars(strip_tags($result), ENT_QUOTES);
    }

    function query($query, $args = NULL) {
        global $irp;

        $defaults = array('post_type' => '', 'all' => FALSE, 'select' => FALSE);
        $args = wp_parse_args($args, $defaults);

        $result = $irp->Options->getCache('Query', $query . '_' . $args['post_type']);
        if (!is_array($result) || count($result) == 0) {
            $q = NULL;
            $id = 'ID';
            $name = 'post_title';
            $function='';
            switch ($query) {
                case IRP_QUERY_POSTS_OF_TYPE:
                    $options = array('posts_per_page' => -1, 'post_type' => $args['post_type']);
                    $q = get_posts($options);
                    $function='get_permalink';
                    break;
                case IRP_QUERY_CATEGORIES:
                    $options = array('posts_per_page' => -1);
                    $q = get_categories($options);
                    $id = 'cat_ID';
                    $name = 'cat_name';
                    $function='get_category_link';
                    break;
                case IRP_QUERY_TAGS:
                    $q = get_tags();
                    $id = 'term_id';
                    $name = 'name';
                    $function='get_tag_link';
                    break;
            }

            $result = array();
            if ($q) {
                foreach ($q as $v) {
                    $result[] = array('id' => $v->$id, 'name' => $v->$name);
                }
            } elseif ($query == IRP_QUERY_POST_TYPES) {
                //$options = array('public' => TRUE, '_builtin' => FALSE);
                //$q = get_post_types($options, 'names');
                $q=array();
                $q = array_merge($q, array('post', 'page'));
                sort($q);
                foreach ($q as $v) {
                    $result[] = array('id' => $v, 'name' => $v);
                }
            }

            if($function!='' && function_exists($function)) {
                for($i=0; $i<count($result); $i++) {
                    $v=$result[$i];
                    $v['url']=call_user_func_array($function, array($v['id']));
                    $result[$i]=$v;
                }
            }
            $irp->Options->setCache('Query', $query . '_' . $args['post_type'], $result);
        }

        if ($args['all']) {
            $first = array();
            $first[] = array('id' => -1, 'name' => '[' . $irp->Lang->L('All') . ']', 'url'=>'');
            $result = array_merge($first, $result);
        }
        if ($args['select']) {
            $first = array();
            $first[] = array('id' => 0, 'name' => '[' . $irp->Lang->L('Select') . ']', 'url'=>'');
            $result = array_merge($first, $result);
        }

        return $result;
    }

    function remoteGet($uri, $options) {
        global $irpp;
        $result=FALSE;
        $uri=add_query_arg($options, $uri);
        //$uri=str_replace('https://', 'http://', $uri);

        $args=array('timeout'=>15, 'sslverify'=>false);
        $irpp->Log->debug('REMOTEGET: URI=%s', $uri);
        $response=FALSE;//wp_remote_get($uri, $args);
        if (FALSE && !is_wp_error($response)) {
            // decode the license data
            $body=wp_remote_retrieve_body($response);
            if($body!==FALSE && $body!='') {
                $irpp->Log->debug('REMOTEGET: RETRIEVEBODY=%s', $body);
                $result=json_decode($body);
                if(!$result) {
                    $irpp->Log->error('REMOTEGET: UNDECODABLE BODY');
                    if(strpos($body, 'debug')!==FALSE || strpos($body, 'fatal')!==FALSE || strpos($body, 'warning')!==FALSE || strpos($body, 'error')!==FALSE) {
                        $irpp->Options->pushErrorMessage('XdebugException');
                    }
                }
            } else {
                $irpp->Log->debug('REMOTEGET: RETRIEVEBODY ERROR');
                $result=FALSE;
            }
        } else {
            $irpp->Log->debug('REMOTEGET: RESULT=ERROR');
        }
        if($result===FALSE) {
            $irpp->Log->debug('file_get_contents: URI=%s', $uri);

            $ch=curl_init();
            $timeout=900;
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $body=curl_exec($ch);
            curl_close($ch);

            $irpp->Log->debug('file_get_contents: RETRIEVEBODY=%s', $body);
            if($body!==FALSE && $body!='') {
                $irpp->Log->debug('REMOTEGET: RETRIEVEBODY=%s', $body);
                $result=json_decode($body);
                if(!$result) {
                    $irpp->Log->error('REMOTEGET: UNDECODABLE BODY');
                    if(strpos($body, 'xdebug')!==FALSE) {
                        $irpp->Options->pushErrorMessage('XdebugException');
                    }
                }
            } else {
                $irpp->Log->debug('REMOTEGET: RETRIEVEBODY ERROR');
                $result=FALSE;
            }
        }
        if(!$result) {
            $result=FALSE;
        }
        return $result;
    }
    function remotePost($action, $data = '') {
        global $irp;

        $data['secret'] = 'WYSIWYG';
        $response = wp_remote_post(IRP_INTELLYWP_ENDPOINT.'?iwpm_action=' . $action, array(
            'method' => 'POST'
            , 'timeout' => 20
            , 'redirection' => 5
            , 'httpversion' => '1.1'
            , 'blocking' => TRUE
            , 'body' => $data
            , 'user-agent' => 'IRPP/' . IRP_PLUGIN_VERSION . '; ' . get_bloginfo('url')
        ));
        $data = json_decode(wp_remote_retrieve_body($response), TRUE);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200
            || !isset($data['success']) || !$data['success']
        ) {
            $irp->Log->error('ERRORS SENDING REMOTE-POST ACTION=%s DUE TO REASON=%s', $action, $response);
            $data = FALSE;
        } else {
            $irp->Log->debug('SUCCESSFULLY SENT REMOTE-POST ACTION=%s RESPONSE=%s', $action, $data);
        }
        return $data;
    }

    function shortcodeAtts($defaults, $atts) {
        if(!is_array($atts)) {
            $atts=array();
        }

        $array=array();
        foreach($defaults as $k=>$v) {
            if (array_key_exists($k, $atts) ) {
                $array[$k] = $atts[$k];
            } elseif (array_key_exists(strtolower($k), $atts) ) {
                $array[$k] = $atts[strtolower($k)];
            } else {
                $array[$k] = $v;
            }
        }
        return $array;
    }

    //wp_parse_args with null correction
    function parseArgs($args, $defaults) {
        if (is_null($args) || !is_array($args)) {
            $args = array();
        }
        foreach ($args as $k => $v) {
            if (is_null($args[$k])) {
                //so can take the default value
                unset($args[$k]);
            } elseif (is_string($args[$k]) && $args[$k] == '' && isset($defaults[$k]) && is_array($defaults[$k])) {
                //a very strange case, i have a blank string for rappresenting an empty array
                unset($args[$k]);
            }
        }
        foreach ($args as &$value) {
            $value = htmlspecialchars(strip_tags($value), ENT_QUOTES);
        }
        $result = wp_parse_args($args, $defaults);
        return $result;
    }

    function redirect($location) {
        if(!headers_sent()) {
            wp_redirect($location);
        }
        ?>
        <script> window.location.replace('<?php echo $location?>'); </script>
    <?php }

    //return the element inside array with the specified key
    function getArrayValue($key, $array, $value='') {
        $result=FALSE;
        if (isset($array[$key])) {
            $result=$array[$key];
            $result['name']=$key;
        }
        if($result!==FALSE && $value!='') {
            if(isset($result[$value])) {
                $result=$result[$value];
            }
        }
        return $result;
    }

    var $_sortField;
    var $_ignoreCase;
    function aksort(&$array, $sortField='name', $ignoreCase=TRUE) {
        $this->_sortField=$sortField;
        $this->_ignoreCase=$ignoreCase;
        usort($array, array($this, "aksortCompare"));
    }
    //not thread-safe!
    private function aksortCompare($a, $b) {
        if ($a===$b || $a==$b) {
            return 0;
        }

        $result=0;
        $a=$a[$this->_sortField];
        $b=$b[$this->_sortField];
        if(is_numeric($a) && is_numeric($b)) {
            $result=($a < $b) ? -1 : 1;
        } else {
            $a.='';
            $b.='';
            if($this->_ignoreCase) {
                $result=strcasecmp($a, $b);
            } else {
                $result=strcmp($a.'', $b);
            }
        }
        return $result;
    }

    function printScriptCss() {
        global $irp;
        $uri=get_bloginfo('wpurl');
        $irp->Tabs->enqueueScripts();
        //wp_enqueue_style('buttons', $uri.'/wp-includes/css/buttons.min.css');
        //wp_enqueue_style('editor', $uri.'/wp-includes/css/editor.min.css');
        //wp_enqueue_style('jquery-ui-dialog', $uri.'/wp-includes/css/jquery-ui-dialog.min.css');
        $styles='dashicons,admin-bar,buttons,media-views,wp-admin,wp-auth-check,wp-color-picker';
        $styles=explode(',', $styles);
        foreach($styles as $v) {
            wp_enqueue_style($v);
        }

        remove_all_actions('wp_print_scripts');
        print_head_scripts();
        print_admin_styles();
    }

    public function sort($isAssociative, $a1, $a2=NULL, $a3=NULL, $a4=NULL, $a5=NULL) {
        $array=$this->merge($isAssociative, $a1, $a2, $a3, $a4, $a5);
        ksort($array);
        return $array;
    }
    public function merge($isAssociative, $a1, $a2=NULL, $a3=NULL, $a4=NULL, $a5=NULL) {
        $result=array();
        if($isAssociative) {
            $array=array($a1, $a2, $a3, $a4, $a5);
            foreach($array as $a) {
                if(!is_array($a)) {
                    continue;
                }

                foreach($a as $k=>$v) {
                    if(!isset($result[$k])) {
                        $result[$k]=$v;
                    }
                }
            }
        } else {
            $result=array_merge($a1, $a2, $a3, $a4, $a5);
        }
        return $result;
    }
    function get($array, $name, $default='') {
        $result=$default;
        if(isset($array[$name])) {
            $result=$array[$name];
        }
        return $result;
    }
    function geti($array, $name, $default='') {
        $result=$default;
        $name=strtolower($name);
        foreach($array as $k=>$v) {
            if(strtolower($k)==$name) {
                $result=$v;
                break;
            }
        }
        if(isset($array[$name])) {
            $result=$array[$name];
        }
        return $result;
    }
    function iget($array, $name, $default='') {
        return intval($this->get($array, $name, $default));
    }
    function isTrue($value) {
        $result=FALSE;
        if(is_bool($value)) {
            $result=(bool)$value;
        } elseif(is_numeric($value)) {
            $result=floatval($value)>0;
        } elseif(is_string($value)) {
            $result=strtolower($value);
            if($result=='ok' || $result=='yes' || $result=='true') {
                $result=TRUE;
            }
        }
        return $result;
    }
    function trimCode($code) {
        $code=str_replace("\t", "", $code);
        $code=str_replace("\r", "", $code);
        $code=str_replace("\n", "", $code);
        while(strpos($code, "  ")!==FALSE) {
            $code=str_replace("  ", " ", $code);
        }
        $code=str_replace("> <", "><", $code);
        $code=trim($code);
        return $code;
    }
    function getUUID($options) {
        $buffer='';
        if(is_string($options)) {
            $buffer=$options;
        } elseif(is_array($options)) {
            foreach($options as $k=>$v) {
                $buffer.=', '.$k.'='.$v;
            }
        }
        if($buffer!='') {
            $buffer='u'.md5($buffer);
        }
        return $buffer;
    }

    function isAdminUser() {
        //https://wordpress.org/support/topic/how-to-check-admin-right-without-include-pluggablephp
        return TRUE;
        /*
        if (!function_exists('wp_get_current_user')) {
            require_once(ABSPATH . 'wp-includes/pluggable.php');
        }
        return (is_multisite() || current_user_can('manage_options'));
        */
    }
    function isPluginPage() {
        $page=$this->qs('page');
        $result=(stripos($page, IRP_PLUGIN_SLUG)!==FALSE);
        return $result;
    }
}
