<?php

class IRP_AppOptions extends IRP_Options {
    public function __construct() {
    }

    public function getMarginTop() {
        return $this->getOption('MarginTop', '0em');
    }
    public function setMarginTop($value) {
        $this->setOption('MarginTop', $value);
    }
    public function getMarginBottom() {
        return $this->getOption('MarginBottom', '1em');
    }
    public function setMarginBottom($value) {
        $this->setOption('MarginBottom', $value);
    }

    public function hasRelatedPostsIds() {
        $array=$this->getRequest('RelatedPostsIds', array());
        return (is_array($array) && count($array)>0);
    }
    public function initRelatedPostsIds($ids) {
        $this->setRequest('RelatedPostsIds', $ids);
        if($ids) {
            shuffle($ids);
        }
        $this->setRequest('ToShowPostsIds', $ids);
        $this->setRequest('ShownPostsIdsSequence', array());
        $this->setRewriteBoxesWritten(0);
    }
    public function refreshRelatedPostsIds() {
        $ids=$this->getRequest('RelatedPostsIds', array());
        $this->initRelatedPostsIds($ids);
    }
    //if you pass maxIds as a number this function take next [maxIds] posts to show as related
    //if you pass maxIds as an array of postsIds will return this array as posts to show as related
    public function getToShowPostsIds($maxIds, $repeat=FALSE) {
        $result=array();
        $maxIds=intval($maxIds);
        if(!$this->hasRelatedPostsIds()) {
            return $result;
        }

        $toShow=$this->getRequest('ToShowPostsIds', array());
        if(is_numeric($maxIds)) {
            if(!is_array($toShow) || (count($toShow)==0 && !$repeat)) {
                return $result;
            }
            while($maxIds>0) {
                if(count($toShow)==0) {
                    if($repeat) {
                        //i can use again the posts shown
                        $toShow=$this->getRequest('RelatedPostsIds');
                        shuffle($toShow);
                    } else {
                        break;
                    }
                }

                $postId=array_pop($toShow);
                $result[]=$postId;
                --$maxIds;
            }
        } elseif(is_array($maxIds)) {
            $toShow=array_diff($toShow, $maxIds);
            $result=$maxIds;
        }
        $this->setRequest('ToShowPostsIds', $toShow);

        //update the sequence shown
        $array=$this->getRequest('ShownPostsIdsSequence', array());
        $array[]=$result;
        $this->setRequest('ShownPostsIdsSequence', $array);

        return $result;
    }
    public function getShownPostsIdsSequence() {
        return $this->getRequest('ShownPostsIdsSequence', array());
    }

    public function getPostShown() {
        return $this->getRequest('PostShown', NULL);
    }
    public function setPostShown($value) {
        $this->setRequest('PostShown', $value);
    }
    public function isPostShownExcluded() {
        global $irp;
        $array=$this->getExcludedPostsIds();
        $post=$this->getPostShown();

        $result=FALSE;
        if(!$post || !isset($post->ID)) {
            $result=TRUE;
        } elseif(in_array($post->ID, $array)) {
            $irp->Log->info('POST ID=%s IN RELATED POSTS EXCLUDE LIST', $post->ID);
            $result=TRUE;
        } else {
            $result=FALSE;
        }
        return $result;
    }
    public function isShortcodeUsed() {
        return $this->getRequest('ShortcodeUsed', 0);
    }
    public function setShortcodeUsed($value) {
        $this->setRequest('ShortcodeUsed', $value);
    }

    public function getTemplateStyle() {
        global $irp;

        $defaults=array(
            'template'=>''
            , 'linkRel'=>$this->getOption('LinkRel', 'nofollow')
            , 'linkTarget'=>$this->getOption('LinkTarget', '_blank')
            , 'ctaText'=>$this->getOption('RelatedText', 'READ')
            , 'ctaTextColor'=>$this->getOption('TemplateRelatedTextColor', '')
            , 'postTitleColor'=>$this->getOption('TemplateRelatedTextColor', '')
            , 'boxColor'=>$this->getOption('TemplateBackgroundColor', '')
            , 'borderColor'=>$this->getOption('TemplateBorderColor', '')
            , 'hasShadow'=>$this->getOption('TemplateShadow', FALSE)
            , 'hasPoweredBy'=>$this->getOption('ShowPoweredBy', FALSE)
            , 'boxOpacity'=>100
        );
        $result=$this->getOption('TemplateStyle', $defaults);
        $names=$irp->HtmlTemplate->getTemplatesNames();
        if($result['template']=='' || !in_array($result['template'], $names)) {
            if(count($names)>0) {
                $result['template']='Minimalist';//$names[0];
            }
        }
        return $result;
    }
    public function setTemplateStyle($value) {
        $this->setOption('TemplateStyle', $value);
    }

    //is related posts active in posts without any [irl] shortcodes defined
    public function isRewriteActive() {
        return $this->getOption('RewriteActive', 1);
    }
    public function setRewriteActive($value) {
        $this->setOption('RewriteActive', $value);
    }
    public function getExcludedPostsIds() {
        return $this->getOption('ExcludedPostsIds', array());
    }
    public function setExcludedPostsIds($value) {
        $value=array_unique($value);
        $this->setOption('ExcludedPostsIds', $value);
    }
    public function getMetaboxPostTypes($create=TRUE) {
        global $irp;
        $result=$this->getOption('MetaboxPostTypes', array());
        if($create) {
            $types=$irp->Utils->query(IRP_QUERY_POST_TYPES);
            foreach($types as $v) {
                $v=$v['id'];
                if(!isset($result[$v]))  {
                    $result[$v]=($v=='post' ? 1 : 0);
                }
            }
        }
        return $result;
    }
    public function setMetaboxPostTypes($values) {
        $this->setOption('MetaboxPostTypes', $values);
    }
    //is integrated with which post types?
    public function getRewritePostTypes($create=TRUE) {
        global $irp;
        $result=$this->getOption('RewritePostTypes', array());
        if($create) {
            $types=$irp->Utils->query(IRP_QUERY_POST_TYPES);
            foreach($types as $v) {
                $v=$v['id'];
                if(!isset($result[$v]))  {
                    $result[$v]=($v=='post' ? 1 : 0);
                }
            }
        }
        return $result;
    }
    public function setRewritePostTypes($values) {
        $this->setOption('RewritePostTypes', $values);
    }
    //how many related posts boxes we have to include?
    public function getRewriteBoxesCount() {
        return intval($this->getOption('RewriteBoxesCount', 3));
    }
    public function setRewriteBoxesCount($value) {
        $this->setOption('RewriteBoxesCount', $value);
    }
    //how many related posts we see in each box?
    public function getRewritePostsInBoxCount() {
        //return $this->getOption('RewritePostsInBoxCount', 1);
        return 1;
    }
    public function setRewritePostsInBoxCount($value) {
        $this->setOption('RewritePostsInBoxCount', $value);
    }
    public function getRewritePostsDays() {
        return intval($this->getOption('RewritePostsDays', 0));
    }
    public function setRewritePostsDays($value) {
        $this->setOption('RewritePostsDays', intval($value));
    }
    //how many words we have to "wait" before inserting a related box
    public function getRewriteThreshold() {
        return $this->getOption('RewriteThreshold', 250);
    }
    public function setRewriteThreshold($value) {
        $this->setOption('RewriteThreshold', $value);
    }
    //include also a related box in the end?
    public function isRewriteAtEnd() {
        return $this->getOption('RewriteAtEnd', TRUE);
    }
    public function setRewriteAtEnd($value) {
        $this->setOption('RewriteAtEnd', $value);
    }
    //how many boxes are already been written?
    public function getRewriteBoxesWritten() {
        return $this->getRequest('RewriteBoxesWritten', 0);
    }
    public function setRewriteBoxesWritten($value) {
        $this->setRequest('RewriteBoxesWritten', $value);
    }

    public function getEngineSearch() {
        return $this->getOption('EngineSearch', IRP_ENGINE_SEARCH_CATEGORIES_TAGS);
    }
    public function setEngineSearch($value) {
        $this->setOption('EngineSearch', $value);
    }

    public function getMaxExecutionTime(){
        return $this->getOption('MaxExecutionTime', -1);
    }
    public function resetMaxExecutionTime(){
        $this->setOption('MaxExecutionTime', -1);
    }
    public function updateMaxExecutionTime($value){
        $now=$this->getMaxExecutionTime();
        if($value>$now) {
            $this->setOption('MaxExecutionTime', $value);
        }
    }

    public function pushCssStyle($code) {
        global $irp;
        if(is_array($code)) {
            $code=implode("\n", $code);
        }
        $code=str_replace('<style>', '', $code);
        $code=str_replace('</style>', '', $code);
        $code=$irp->Utils->trimCode($code);

        $array=$this->getCssStyles();
        $exists=FALSE;
        if(count($array)>0) {
            foreach($array as $v) {
                if(trim($v)==trim($code)) {
                    $exists=TRUE;
                    break;
                }
            }
        }
        if(!$exists) {
            $array[]=$code;
            $this->setRequest('CssStyles', $array);
        }
    }
    public function getCssStyles() {
        return $this->getRequest('CssStyles', array());
    }

    public function getColor($color) {
        global $irp;
        $result=$color;
        if(!$irp->Utils->startsWith($color, '#')) {
            $colors=$this->getLegacyColors();
            $v=$irp->Utils->geti($colors, $color, FALSE);
            if($v!==FALSE) {
                $result=$v['color'];
            } else {
                $colors=$this->getColors();
                $v=$irp->Utils->geti($colors, $color, FALSE);
                if($v!==FALSE) {
                    $result=$v['color'];
                }
            }
        }
        return $result;
    }
    public function getHoverColor($color) {
        $color=$this->getColor($color);
        $colors=$this->getLightDarkColors();
        $result='';
        foreach($colors as $k=>$v) {
            if($v['light']==$color) {
                $result=$v['dark'];
                break;
            } elseif($v['dark']==$color) {
                $result=$v['light'];
                break;
            }
        }
        return $result;
    }
    public function getColors($blank='') {
        $array=$this->getLightDarkColors();
        $result=array();
        if($blank!='') {
            $result[$blank]=array('color'=>'', 'fontColor'=>'#464646');
        }
        foreach($array as $k=>$v) {
            if(isset($v['color'])) {
                $a=array();
                $a['color']=$v['color'];
                if(isset($v['fontColor'])) {
                    $a['fontColor']=$v['fontColor'];
                }
                $result[$k]=$a;
            }
            if(isset($v['light'])) {
                $a=array();
                $a['color']=$v['light'];
                if(isset($v['fontLight'])) {
                    $a['fontColor']=$v['fontLight'];
                }
                $result[$k.' Light']=$a;
            }
            if(isset($v['dark'])) {
                $a=array();
                $a['color']=$v['dark'];
                if(isset($v['fontDark'])) {
                    $a['fontColor']=$v['fontDark'];
                }
                $result[$k.' Dark']=$a;
            }
        }

        ksort($result);
        return $result;
    }
    //$colors['(Default)']=array('color'=>'', 'fontColor'=>'#464646');
    public function getLightDarkColors() {
        $colors['WHITE']=array(
            'light'=>'#FFFFFF', 'fontLight'=>'#464646'
            , 'dark'=>'#eaeaea', 'fontDark'=>'#464646'
        );
        $colors['AQUA']=array('light'=>'#1ABC9C', 'dark'=>'#16A085');
        $colors['GREEN']=array('light'=>'#2ECC71', 'dark'=>'#27AE60');
        $colors['VIOLET']=array('light'=>'#9B59B6', 'dark'=>'#8E44AD');
        $colors['BLUE #1']=array('light'=>'#3498DB', 'dark'=>'#2980B9');
        $colors['BLUE #2']=array('light'=>'#34495E', 'dark'=>'#2C3E50');
        $colors['YELLOW']=array('light'=>'#F1C40F', 'dark'=>'#F39C12');
        $colors['ORANGE']=array('light'=>'#E67E22', 'dark'=>'#D35400');
        $colors['RED']=array('light'=>'#E74C3C', 'dark'=>'#C0392B');
        $colors['GREY #1']=array(
            'light'=>'#ECF0F1', 'fontLight'=>'#464646'
            , 'dark'=>'#e6e6e6', 'fontDark'=>'#464646'
        );
        $colors['GREY #2']=array('light'=>'#95A5A6', 'dark'=>'#7F8C8D');
        $colors['BLACK']=array('light'=>'#141414', 'dark'=>'#000000');
        ksort($colors);
        return $colors;
    }
    public function getLegacyColors() {
        $colors=array();
        $colors['(Default)']=array('color'=>'', 'fontColor'=>'#464646');
        $colors['WHITE']=array('color'=>'#FFFFFF', 'fontColor'=>'#464646');
        $colors['LIGHT GREY']=array('color'=>'#ECF0F1', 'fontColor'=>'#464646');
        $colors['DARK GREY']=array('color'=>'#555555');
        $colors['BLACK']=array('color'=>'#000000');

        $colors['(Transparent)']=array('color'=>'', 'fontColor'=>'#464646');
        $colors['WHITE']=array('color'=>'#FFFFFF', 'fontColor'=>'#464646');
        $colors['LIGHT GREY']=array('color'=>'#ECF0F1', 'fontColor'=>'#464646');
        $colors['DARK GREY']=array('color'=>'#555555');
        $colors['BLACK']=array('color'=>'#000000'); //black

        $colors['(Transparent)']=array('color'=>'', 'fontColor'=>'#464646');
        $colors['TURQUOISE']=array('color'=>'#1ABC9C'); //Aqua
        $colors['EMERALD']=array('color'=>'#2ECC71'); //green
        $colors['AMETHYST']=array('color'=>'#9B59B6');//violet
        $colors['PETER RIVER']=array('color'=>'#3498DB');//blue
        $colors['WET ASPHALT']=array('color'=>'#34495E');//blue
        $colors['SUN FLOWER']=array('color'=>'#F1C40F');//yellow
        $colors['CARROT']=array('color'=>'#E67E22');//orange
        $colors['ALIZARIN']=array('color'=>'#E74C3C');//red
        $colors['CLOUDS']=array('color'=>'#ECF0F1', 'fontColor'=>'#464646');//grey
        $colors['CONCRETE']=array('color'=>'#95A5A6');//grey (+grey)

        $colors['(Transparent)']=array('color'=>'', 'fontColor'=>'#464646');
        $colors['GREEN SEA']=array('color'=>'#16A085');
        $colors['NEPHRITIS']=array('color'=>'#27AE60');
        $colors['WISTERIA']=array('color'=>'#8E44AD');
        $colors['BELIZE HOLE']=array('color'=>'#2980B9');
        $colors['MIDNIGHT BLUE']=array('color'=>'#2C3E50');
        $colors['ORANGE']=array('color'=>'#F39C12');
        $colors['PUMPKIN']=array('color'=>'#D35400');
        $colors['POMEGRANATE']=array('color'=>'#C0392B');
        $colors['SILVER']=array('color'=>'#BDC3C7');
        $colors['ASBESTOS']=array('color'=>'#7F8C8D');
        ksort($colors);
        return $colors;
    }

    public function getTemplateUUID($template) {
        $uuid=$this->getRequest("Template>".$template);
        if(!$uuid) {
            $uuid="s".md5($template."-".date("Ymd"));
            $this->setRequest("Template>".$template, $uuid);
        }
        return $uuid;
    }
}