<?php
if (!defined('ABSPATH')) exit;

class IRP_HTMLContext {
    var $root; //MainTag
    var $buffer; //IRP_Stack

    var $uncuttable;
    var $withoutNextState;
    var $withoutNextWords;

    var $currentWords;
    var $lastBoxWords;
    //last time that the plugin insert the related posts box counter values
    var $wordsThreshold;
    var $skipWordsCount=FALSE;

    public function __construct() {
        $this->clearBuffer();
    }

    public function setWithoutNextBox() {
        $this->withoutNextState=TRUE;
        $this->withoutNextWords=$this->currentWords;
    }
    public function isSkipCurrentBox() {
        $result=FALSE;
        if($this->withoutNextState) {
            //if i already had written some words i can unlock the withoutNext lock
            $result=($this->currentWords<=$this->withoutNextWords);
            if(!$result) {
                $this->withoutNextState=FALSE;
                $this->withoutNextWords=0;
            }
        }
        return $result;
    }
    public function clearSkipNext() {
        $this->withoutNextState=FALSE;
        $this->withoutNextWords=0;
    }

    public function setUncuttable($value) {
        $this->uncuttable=$value;
    }
    public function isUncuttable() {
        return $this->uncuttable;
    }

    private function clearBuffer() {
        $this->buffer=new IRP_Stack();
        $this->uncuttable=FALSE;
        $this->isParentTable=FALSE;
        $this->withoutNextState=FALSE;
        $this->currentWords=0;
        $this->lastBoxWords=0;
        $this->withoutNextState=FALSE;
        $this->withoutNextWords=0;
    }
    private function pushTextContent($part, $text, IRP_Stack &$tagsStack) {
        if(!$part) {
            $parent=$tagsStack->peek();
            $part=new IRP_TextContent();
            $parent->pushTag($part);
        }
        $part->append($text);
        return $part;
    }

    public function isWriteRelatedBox() {
        global $irp;
        if($this->isSkipCurrentBox()) {
            return FALSE;
        }
        if($this->isUncuttable()) {
            return FALSE;
        }

        $irp->Log->debug('WriteRelatedBox?');
        $diff=($this->currentWords-$this->lastBoxWords);
        $irp->Log->debug('currentWords[%s]-lastBoxWords[%s]=diff[%s]'
            , $this->currentWords, $this->lastBoxWords, $diff);

        $postLimit=$this->wordsThreshold*($irp->Options->getRewriteBoxesWritten()+1);
        $irp->Log->debug('wordsThreshold[%s]*(RewriteBoxesWritten[%s]+1)=postLimit[%s]'
            , $this->wordsThreshold, $irp->Options->getRewriteBoxesWritten(), $postLimit);

        $minLimit=$irp->Options->getRewriteThreshold();
        $result=($this->currentWords>=$postLimit && $diff>=$minLimit);
        $irp->Log->debug('(currentWords[%s]>=postLimit[%s]) AND (diff[%s]>=minLimit[%s])=%s'
            , $this->currentWords, $postLimit
            , $diff, $minLimit, $result);
        return $result;
    }

    function getTagAttribute($fullTag, $name) {
        $close=strpos($fullTag, '>');
        $attr=strpos($fullTag, ' '.$name.'=');
        $result='';
        if($attr!==FALSE && $close!==FALSE && $attr<$close) {
            $quote=substr($fullTag, $attr+strlen($name)+2, 1);
            $text=substr($fullTag, $attr+strlen($name)+3);
            $end=strpos($text, $quote, 1);
            if($end!==FALSE) {
                $result=substr($text, 0, $end);
            }
        }
        return $result;
    }

    //get the tag name to lower case, could be that is not defined
    //so this function return '' could also be that
    function getTagName($fullTag) {
        global $irp;

        $start=1;
        $p=strpos($fullTag, ' ');
        if($p===FALSE) {
            $p=strpos($fullTag, '/');
            if($p!==FALSE && $p==1) {
                $start=2;
                $p=FALSE;
            }
            if($p===FALSE) {
                $p=strpos($fullTag, '>');
                if($p===FALSE) {
                    $irp->Log->error('UNABLE TO DECODE TAG %s', $fullTag);
                    return '';
                }
            }
        }
        $tag=trim(substr($fullTag, $start, $p-$start));
        $tag=strtolower($tag);
        return $tag;
    }
    function getHtmlTag($fullTag){
        global $irp;

        $tag=$this->getTagName($fullTag);
        $tagId=$this->getTagAttribute($fullTag, 'id');
        $tagClasses=$this->getTagAttribute($fullTag, 'class');
        if($tagClasses!=='') {
            $tagClasses=explode(' ', $tagClasses);
        } else {
            $tagClasses=array();
        }

        if($tag=='') {
            return NULL;
        }

        $result=new IRP_OtherTag();
        if(in_array($tag, array('h1','h2','h3','h4','h5','h6'))) {
            $result=new IRP_BehaviourTag();
            $result->allowBoxBefore=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->ensureWithoutNextBox=TRUE;
        } elseif(in_array($tag, array('ul', 'ol', 'dl'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutPreviousBox=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->allowBoxAfter=TRUE;
        } elseif(in_array($tag, array('area','base','col','br','command','embed','hr','img','input','link','meta','param','source'))) {
            $result=new IRP_SingletonTag();
        } elseif(in_array($tag, array('iframe'))) {
            $result=new IRP_BehaviourTag();
            $result->allowBoxBefore=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->allowBoxAfter=TRUE;
            $result->skipWordsCount=TRUE;
        } elseif(in_array($tag, array('table'))) {
            $result=new IRP_BehaviourTag();
            $result->allowBoxBefore=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->allowBoxAfter=TRUE;
        } elseif(in_array($tag, array('span'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutPreviousBox=TRUE;
            $result->ensureWithoutNextBox=TRUE;
        } elseif(in_array($tag, array('figure'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutNextBox=TRUE;
            $result->skipWordsCount=TRUE;
            $result->ensureUncuttable=TRUE;
        } elseif(in_array($tag, array('div'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutPreviousBox=TRUE;
            $result->ensureWithoutNextBox=TRUE;

            $ids=explode('|', 'optinforms-|wpautbox-|wpurp-container-recipe-');
            if($irp->Utils->startsWith($tagId, $ids)) {
                $result->skipWordsCount=TRUE;
                $result->ensureUncuttable=TRUE;
            }

            $classes=explode('|', 'yuzo_related_post|shareaholic-|wp-caption|wordpress-post-tabs|amp-wp-article-content|title-wrapper|sa-source-wrapper|et_pb_text_inner|wprm-recipe-name|toc_light_blue|ast-oembed-container|depiction|wc-tab|gkblock-3|et_pb_toggle_content|et_pb_module|aawp-product__description|fusion-text|fusion-alert-content|mv-create-instructions|recipe-ingredients-wrap|guide-box|underPostOptin|x-columnize');
            if($irp->Utils->startsWith($tagClasses, $classes)) {
                $result->skipWordsCount=TRUE;
                $result->ensureUncuttable=TRUE;
            }
        } elseif(in_array($tag, array('p'))) {
            $result=new IRP_BehaviourTag();
            $result->allowBoxBefore=TRUE;
            $result->allowBoxAfter=TRUE;
        } elseif(in_array($tag, array('blockquote', 'cite', 'code', 'em', 'pre'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutPreviousBox=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->ensureWithoutNextBox=TRUE;
        } elseif(in_array($tag, array('irp', 'script', 'style'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureWithoutPreviousBox=TRUE;
            $result->ensureUncuttable=TRUE;
            $result->ensureWithoutNextBox=TRUE;
            $result->skipWordsCount=TRUE;
        } elseif(in_array($tag, array('sub', 'sup'))) {
            $result=new IRP_BehaviourTag();
            $result->ensureUncuttable=TRUE;
        }
        return $result;
    }

    public function decode($all) {
        global $irp;

        $this->root=new IRP_MainTag();
        $this->clearBuffer();

        if(!$all || trim($all)=='') return FALSE;
                
        //qui sono a ricercare tutti i tag per poi farci delle verifiche
        $tagsStack=new IRP_Stack();
        $tagsStack->push($this->root);
        
        $errors=FALSE;
        $part=NULL;
        $previous=0;
            
        try {
            do {
                $less=strpos($all, '<', $previous);
                if($less===FALSE) {
                    if($previous!=strlen($all)) {
                        $text=$irp->Utils->substrln($all, $previous);
                        $part=$this->pushTextContent($part, $text, $tagsStack);
                    }
                    break;
                }
                
                if($previous!=$less) {
                    $text=$irp->Utils->substrln($all, $previous, $less);
                    $part=$this->pushTextContent($part, $text, $tagsStack);
                }

                $more=strpos($all, '>', $less+1);
                $another=strpos($all, '<', $less+1);
                if($more===FALSE) {
                    //only open tag so I considate all as text (in this case is better
                    //to use html code like &gt; or &lt;
                    $text=$irp->Utils->substrln($all, $previous);
                    $part=$this->pushTextContent($part, $text, $tagsStack);
                    break;
                }
                
                if($another!==FALSE && $another<$more) {
                    //something like this <bla bla bla <bla bla> so we considerate
                    //from the last < before >
                    $previous=$another;
                    $text=$irp->Utils->substrln($all, $less, $previous);
                    $part=$this->pushTextContent($part, $text, $tagsStack);
                    continue;
                }

                $previous=$more+1;
                $text=$irp->Utils->substrln($all, $less, $previous);
                $parent=$tagsStack->peek();
                $tag=$this->getOpenTag($text);
                if($tag) {
                    //detected tag open
                    $part=NULL;
                    $parent->pushTag($tag);
                    if($tag->hasTagContent()) {
                        //this tag contains a content
                        $tagsStack->push($tag);
                    }
                } else {
                    //detected tag close
                    $tag=$this->getCloseTag($text, $tagsStack);
                    if($tag) {
                        $part=NULL;
                        $compare=$tagsStack->pop();
                        while(!$tagsStack->isEmpty() && $compare!=$tag) {
                            //security check: close each tag until i found my tag
                            $irp->Log->error('WHAT? UNABLE TO FIND OPENED TAG..TRY CLOSING AND RETRYING AGAIN');
                            $compare->closeTag='</'.$compare->tag.'>';
                            $compare=$tagsStack->pop();
                        }
                        if($compare!=$tag) {
                            $irp->Log->error('WHAT? UNABLE TO FIND OPENED TAG');
                            $errors=TRUE;
                        } else {
                            $compare->closeTag=$text;
                        }
                    } else {
                        //simply text
                        $part=$this->pushTextContent($part, $text, $tagsStack);
                    }
                }
            }
            while($previous<strlen($all));
        } catch(Exception $ex) {
            $irp->Log->exception($ex);
        }
        
        return !$errors;
    }
    
    private function getOpenTag($openTag) {
        global $irp;
        if($irp->Utils->startsWith($openTag, '</')) {
            return NULL;
        }

        $name=$this->getTagName($openTag);
        $tag=$this->getHtmlTag($openTag);
        if($tag) {
            $tag->tag=$name;
            $tag->openTag=$openTag;
        }
        return $tag;
    }
    private function getCloseTag($closeTag, IRP_Stack $tagsStack) {
        global $irp;
        if(!$irp->Utils->startsWith($closeTag, '</')) {
            return NULL;
        }

        $compare=$this->getTagName($closeTag);
        if($compare=='') return NULL;

        $tos=$tagsStack->peek();
        if(!$tos || !isset($tos->tag) || strcasecmp($tos->tag, $compare)!=0) {
            $irp->Log->error('CHECK YOU BODY TAG %s ENDS WITH %s TAG', $tos->tag, $compare);
            return NULL;
        }

        $tos->closeTag=$closeTag;
        return $tos;
    }

    public function execute($body) {
        global $irp;
        if(!$irp->Options->isRewriteActive()) {
            return $body;
        }

        $this->decode($body);
        $this->root->analyseText($this);
        $t=intval($this->currentWords/($irp->Options->getRewriteBoxesCount()+1));
        if($t<$irp->Options->getRewriteThreshold()) {
            $t=$irp->Options->getRewriteThreshold();
        }
        /*
        if($t<$irp->Options->getRewriteThreshold()) {
            if($irp->Options->isRewriteAtEnd()) {
                //this avoid to write a box just before the end of the posts
                //where, due the isRewriteAtEnd flag we will write another box
                $t=$this->wordsCounter+1;
            } else {
                $t=$irp->Options->getRewriteThreshold();
            }
        }
        */
        $this->wordsThreshold=$t;

        $this->clearBuffer();
        $this->root->write($this);

        $result='';
        foreach($this->buffer->array as $v) {
            $result.=$v->getText();
        }
        return $result;
    }
    public function write($text, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        global $irp;
        if(!$text || $text=='') return;
        $text=$irp->Utils->format($text, $v1, $v2, $v3, $v4, $v5);

        $v=NULL;
        $new=$this->buffer->isEmpty();
        if(!$new) {
            $v=$this->buffer->peek();
            if(!$v->isBufferText()) {
                $new=TRUE;
            }
        }

        if($new) {
            $v=new IRP_BufferText();
            $this->buffer->push($v);
        }
        $v->appendText($text);
    }
    public function writeRelatedBoxIfNeeded() {
        $result=FALSE;
        //write the box at the end of the tag only if this is not an <Hn> tag
        if($this->isWriteRelatedBox()) {
            $result=$this->writeRelatedBox();
        }
        return $result;
    }
    public function writeRelatedBox($forceBox=FALSE) {
        global $irp;

        $written=$irp->Options->getRewriteBoxesWritten();
        $max=$irp->Options->getRewriteBoxesCount();
        if(!$forceBox && $written>=$max) {
            $irp->Log->error('MAX BOX=%s REACHED', $max);
            return FALSE;
        }

        $irp->Log->debug('WRITING BOX=%s/%s', $written+1, $max);
        $result=FALSE;
        $count=1;//$irp->Options->getRewritePostsInBoxCount();
        $ids=$irp->Options->getToShowPostsIds($count, TRUE);

        $comment="INLINE RELATED POSTS %s/%s";
        $comment=sprintf($comment, $written+1, $max);
        $options=array(
            'comment'=>$comment
            , 'shortcode'=>FALSE
        );
        $box=irp_ui_get_box($ids, $options);
        if($box!='') {
            $this->pushRelatedBox($box);
            $result=TRUE;
        } else {
            $result=FALSE;
            $irp->Log->error('NO BOX TO WRITE WITH IDS=%s', $ids);
        }
        return $result;
    }
    //append related-box element to buffer
    public function pushRelatedBox($box) {
        global $irp;

        $v=new IRP_BufferBox();
        $v->appendText($box);
        $v->currentBoxWords=$this->currentWords;
        $v->previousBoxWords=$this->lastBoxWords;
        $this->buffer->push($v);

        $written=$irp->Options->getRewriteBoxesWritten()+1;
        $irp->Options->setRewriteBoxesWritten($written);
        $this->lastBoxWords=$v->currentBoxWords;
    }
    //remove related-box elements from buffer starting from the end
    public function popRelatedBox($args=NULL) {
        global $irp;
        if($this->buffer->isEmpty()) {
            return FALSE;
        }

        $result=FALSE;
        $defaults=array('last'=>TRUE, 'all'=>FALSE);
        $args=$irp->Utils->parseArgs($args, $defaults);
        if($args['last']) {
            $v=$this->buffer->peek();
            if($v->isBufferBox()) {
                $v=$this->buffer->pop();
                $this->lastBoxWords=$v->previousBoxWords;
                $result=TRUE;

                $w=$irp->Options->getRewriteBoxesWritten()-1;
                $irp->Options->setRewriteBoxesWritten($w);
            }
        } elseif($args['all']) {
            $array=array();
            for($i=0; $i<count($this->buffer->array); $i++) {
                $v=$this->buffer->array[$i];
                if($v->isBufferBox()) {
                    $result=TRUE;
                } else {
                    $array[]=$v;
                }
            }
            $this->buffer->array=$array;
            $this->lastBoxWords=0;
            $irp->Options->setRewriteBoxesWritten(0);
        }
        return $result;
    }
    public function incCounters($text){
        global $irp;
        if($this->skipWordsCount) {
            return;
        }

        $text=trim($text);
        $words=explode(' ', $text);
        $c=0;
        foreach($words as $w) {
            $w=trim($w);
            if($w!='' && strlen($w)>1) {
                $c++;
            }
        }

        $this->currentWords+=$c;
        $irp->Log->debug('TEXT=[%s] INCREMENT WORDS words[%s]/currentWords[%s]'
            , $text, $c, $this->currentWords);
    }
}

class IRP_BufferElement {
    var $text;
    public function __construct() {
        $this->text='';
    }

    public function getText() {
        return $this->text;
    }
    public function appendText($text) {
        $this->text.=$text;
    }

    public function isBufferBox() {
        return FALSE;
    }
    public function isBufferText() {
        return FALSE;
    }
}
class IRP_BufferText extends IRP_BufferElement {
    public function __construct() {
        parent::__construct();
    }

    public function isBufferBox() {
        return FALSE;
    }
    public function isBufferText() {
        return TRUE;
    }
}
class IRP_BufferBox extends IRP_BufferElement {
    var $currentBoxWords;
    var $previousBoxWords;

    public function __construct() {
        parent::__construct();
        $this->currentBoxWords=0;
        $this->previousBoxWords=0;
    }

    public function isBufferBox() {
        return TRUE;
    }
    public function isBufferText() {
        return FALSE;
    }
}