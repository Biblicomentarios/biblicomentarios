<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class IRP_Form {
    var $prefix='Form';
    var $labels=TRUE;
    var $leftLabels=TRUE;
    var $newline;
    var $helps=FALSE;
    var $selectIdField='id';

    var $tags=FALSE;
    var $tagNew=FALSE;
    var $leftTags=FALSE;

    public function __construct() {
    }

    //args can be a string or an associative array if you want
    private function getTextArgs($args, $defaults, $excludes=array()) {
        $result=$args;
        if(is_string($excludes)) {
            $excludes=explode(',', $excludes);
        }
        if(is_array($result) && count($result)>0) {
            $result='';
            foreach($args as $k=>$v) {
                if(count($excludes)==0 || !in_array($k, $excludes)) {
                    $result.=' '.$k.'="'.$v.'"';
                }
            }
        } elseif(!$args) {
            $result='';
        }
        if(is_array($defaults) && count($defaults)>0) {
            foreach($defaults as $k=>$v) {
                if(count($excludes)==0 || !in_array($k, $excludes)) {
                    if(stripos($result, $k.'=')===FALSE) {
                        $result.=' '.$k.'="'.$v.'"';
                    }
                }
            }
        }
        return $result;
    }

    public function help($name) {
        global $irp;
        if(!$this->helps) return;

        $k=$this->prefix.'.'.$name.'.Tooltip';
        $label=$irp->Lang->L($k);
        ?>
        <img src="<?php echo IRP_PLUGIN_IMAGES.'question-mark.png'?>" class="irp-help" id="<?php echo $name?>-help" alt="<?php echo esc_attr($label)?>" />
    <?php
    }
    public function label($name, $args='') {
        global $irp;
        $defaults=array('class'=>'');
        $otherText=$this->getTextArgs($args, $defaults, array('label', 'id'));

        $k=$this->prefix.'.'.$name;
        if(!is_array($args)) {
            $args=array();
        }
        if(isset($args['label']) && $args['label']) {
            $k=$args['label'];
        }
        $label=$irp->Lang->L($k);
        $for=(isset($args['id']) ? $args['id'] : $name);

        //check if is a mandatory field by checking the .txt language file
        $k=$this->prefix.'.'.$name.'.check';
        if($irp->Lang->H($k)) {
            $label.=' (*)';
        }

        $aClass='';
        ?>
        <label for="<?php echo $for?>" <?php echo $otherText?> >
            <?php if($this->leftTags) {
                $this->tag();
            }?>
            <span style="float:left; margin-right:5px;" class="<?php echo $aClass?>" id="<?PHP echo $for?>Label"><?php echo $label?></span>
            <?php if(!$this->leftTags) {
                $this->tag();
            }?>
        </label>
    <?php }

    public function leftInput($name, $args='') {
        if(!$this->labels) return;

        if($this->leftLabels) {
            $this->help($name);
            $this->label($name, $args);
        }

        if($this->newline) {
            $this->newline();
        }
    }

    public function newline() {
        ?><div class="irp-form-newline"></div><?php
    }

    public function rightInput($name, $args='') {
        if(!$this->labels) return;
        if (!$this->leftLabels) {
            $this->label($name, $args);
        }
        $this->newline();
    }

    public function formStarts($method='post', $action='', $args=NULL) {
        //$defaults=array('style'=>'margin:1em 0; padding:1px 1em; background:#fff; border:1px solid #ccc;'
        $defaults=array('class'=>'irp-form');
        $other=$this->getTextArgs($args, $defaults);
        ?>
        <form method="<?php echo $method?>" action="<?php echo $action?>" <?php echo $other?> >
    <?php }

    public function formEnds($clearfix = true) { ?>
        </form>
        <?php if ($clearfix == true) : ?>
        <div style="clear:both;"></div>
    <?php 
		endif;
	}

    public function divStarts($args=array()) {
        $defaults=array();
        $other=$this->getTextArgs($args, $defaults);
        ?>
        <div <?php echo $other?>>
    <?php }
    public function divEnds() { ?>
        </div>
        <div style="clear:both;"></div>
    <?php }

    public function p($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        global $irp;
        ?>
        <p style="font-weight:bold;">
            <?php
            $irp->Lang->P($message, $v1, $v2, $v3, $v4, $v5);
            if($irp->Lang->H($message.'Subtitle')) { ?>
                <br/>
                <span style="font-weight:normal;">
                    <i><?php $irp->Lang->P($message.'Subtitle', $v1, $v2, $v3, $v4, $v5)?></i>
                </span>
            <?php } ?>
        </p>
    <?php }

    public function textarea($name, $value='', $args=NULL) {
        if(is_array($value) && isset($value[$name])) {
            $value=$value[$name];
        }
        $defaults=array('rows'=>10, 'class'=>'irp-textarea');
        $other=$this->getTextArgs($args, $defaults);

        $args=array('class'=>'irp-label', 'style'=>'width:auto;');
        $this->newline=TRUE;
        $this->leftInput($name, $args);
        ?>
            <textarea dir="ltr" dirname="ltr" id="<?php echo $name ?>" name="<?php echo $name?>" <?php echo $other?> ><?php echo $value ?></textarea>
        <?php
        $this->newline=FALSE;
        $this->rightInput($name, $args);
    }

    public function number($name, $value='', $options=NULL) {
        if(!$options) {
            $options=array();
        }
        $options['type']='number';
        $options['autocomplete']='off';
        $options['style']='width:100px;';
        if(!isset($options['min'])) {
            $options['min']=0;
        }
        //if(!isset($options['step'])) {
        //    $options['step']=1;
        //}
        return $this->text($name, $value, $options);
    }
    public function text($name, $value='', $options=NULL) {
        if(is_array($value) && isset($value[$name])) {
            $value=$value[$name];
        }
        $type='text';
        if(isset($options['type'])) {
            $type=$options['type'];
        }
        $defaults=array('class'=>'irp-'.$type);
        $other=$this->getTextArgs($options, $defaults, 'type');

        $args=array('class'=>'irp-label');
        $this->leftInput($name, $args);
        ?>
            <input type="<?php echo $type?>" id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" <?php echo $other?> />
        <?php
        $this->rightInput($name, $args);
    }

    public function hidden($name, $value='', $args=NULL) {
        if(is_array($value) && isset($value[$name])) {
            $value=$value[$name];
        }
        $defaults=array();
        $other=$this->getTextArgs($args, $defaults);
        ?>
        <input type="hidden" id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" <?php echo $other?> />
    <?php }

    public function nonce($action=-1, $name='_wpnonce', $referer=true, $echo=true) {
        wp_nonce_field($action, $name, $referer, $echo);
    }

    public function colorSelect($name, $value, $options, $multiple=FALSE, $args=NULL) {
        $array=array();
        foreach($options as $k=>$v) {
            $color=(isset($v['color']) ? $v['color'] : '');
            $fontColor=(isset($v['fontColor']) ? $v['fontColor'] : 'white');
            $style='';
            if($color!='') {
                if($style!='') {
                    $style.='; ';
                }
                $style.='background-color:'.$color;
            }
            if($fontColor!='') {
                if($style!='') {
                    $style.='; ';
                }
                $style.='color:'.$fontColor.'; font-weight:bold';
            }
            $v['id']=$color;
            $v['name']=$k;
            $v['style']=$style;
            $array[]=$v;
        }
        if($args) {
            $args=array();
        }
        $args['class']='irpColorSelect';
        return $this->select($name, $value, $array, $multiple, $args);
    }
    public function select($name, $value, $options, $multiple=FALSE, $args=NULL) {
        global $irp;
        if(is_array($value) && isset($value[$name])) {
            $value=$value[$name];
        }
        $defaults=array('class'=>'irp-select');
        $other=$this->getTextArgs($args, $defaults);

        if(!is_array($value)) {
            $value=array($value);
        }
        if(is_string($options)) {
            $options=explode(',', $options);
        }
        if(is_array($options) && count($options)>0) {
            if(is_string($options[0]) || !isset($options[0][$this->selectIdField])) {
                //this is a normal array so I use the values for "id" field and the "name" into the txt file
                $temp=array();
                foreach($options as $v) {
                    $item=array();
                    $item[$this->selectIdField]=$v;
                    $item['name']=$v;
                    if($irp->Lang->H($this->prefix.'.'.$name.'.'.$v)) {
                        $item['name']=$irp->Lang->L($this->prefix.'.'.$name.'.'.$v);
                    }
                    $temp[]=$item;
                }
                $options=$temp;
            }
        }

        $args=array('class'=>'irp-label');
        $this->leftInput($name, $args);
        ?>
            <select id="<?php echo $name ?>" name="<?php echo $name?><?php echo ($multiple ? '[]' : '')?>" <?php echo ($multiple ? 'multiple' : '')?> <?php echo $other?> >
                <?php
                foreach($options as $v) {
                    $style='';
                    if(isset($v['style'])) {
                        $style=$v['style'];
                    }

                    $selected='';
                    if(in_array($v[$this->selectIdField], $value)) {
                        $selected=' selected="selected"';
                    }
                    ?>
                    <option value="<?php echo $v[$this->selectIdField]?>" <?php echo $selected?> style="<?php echo $style?>"><?php echo $v['name']?></option>
                <?php } ?>
            </select>
        <?php
        $this->rightInput($name, $args);
    }

    public function submit($value='', $args=NULL) {
        global $irp;
        $defaults=array();
        $other=$this->getTextArgs($args, $defaults);
        if($value=='') {
            $value='Send';
        }
        $this->newline();
        ?>
            <input type="submit" class="button-primary irp-button irp-submit" value="<?php $irp->Lang->P($value)?>" <?php echo $other?>/>
    <?php }

    public function delete($id, $action='delete', $args=NULL) {
        global $irp;
        $defaults=array();
        $other=$this->getTextArgs($args, $defaults);
        ?>
            <input type="button" class="button irp-button" value="<?php $irp->Lang->P('Delete?')?>" onclick="if (confirm('<?php $irp->Lang->P('Are you sure you want to delete?')?>') ) window.location='<?php echo IRP_TAB_BUILDER_URI?>&action=<?php echo $action?>&id=<?php echo $id ?>&amp;irp_nonce=<?php echo esc_attr(wp_create_nonce('irp_delete')); ?>';" <?php echo $other?> />
            &nbsp;
        <?php
    }

    public function radio($name, $current=1, $value=1, $args=NULL) {
        if(!is_array($args)) {
            $args=array();
        }
        $args['radio']=TRUE;
        $args['id']=$name.'_'.$value;
        return $this->checkbox($name, $current, $value, $args);
    }
    public function checkbox($name, $current=1, $value=1, $args=NULL) {
        global $irp;
        if(is_array($current) && isset($current[$name])) {
            $current=$current[$name];
        }

        /*
            $defaults=array('class'=>'irp-checkbox', 'style'=>'margin:0px; margin-right:4px;');
            if($this->premium && !$irp->License->hasPremium()) {
                $defaults['disabled']='disabled';
                $value='';
            }
        */
        if(!is_array($args)) {
            $args=array();
        }

        $label=$name;
        $type='checkbox';
        if(isset($args['radio']) && $args['radio']) {
            $type='radio';
            $label.='_'.$value;
        }

        $defaults=array(
            'class'=>'irp-checkbox'
            , 'style'=>'margin:0px; margin-right:4px;'
            , 'id'=>$name
        );
        $other=$this->getTextArgs($args, $defaults, 'radio,label,type');
        $prev=$this->leftLabels;
        $this->leftLabels=FALSE;

        $label=(isset($args['label']) ? $args['label'] : $this->prefix.'.'.$label);
        $id=(isset($args['id']) ? $args['id'] : $name);
        $args=array(
            'class'=>''
            , 'style'=>'margin-top:-1px;'
            , 'label'=>$label
            , 'id'=>$id
        );
        $this->leftInput($name, $args);
        ?>
        <input type="<?php echo $type ?>" name="<?php echo $name?>" value="<?php echo $value?>" <?php echo($current==$value ? 'checked="checked"' : '') ?> <?php echo $other?> >
        <?php
        $this->rightInput($name, $args);
        $this->leftLabels=$prev;
    }

    public function checkText($nameActive, $nameText, $value) {
        global $irp;

        $args=array('class'=>'irp-hideShow irp-checkbox'
        , 'irp-hideIfTrue'=>'false'
        , 'irp-hideShow'=>$nameText.'Text');
        $this->checkbox($nameActive, $value, 1, $args);
        ?>
        <div id="<?php echo $nameText?>Text" style="float:left;">
            <?php
            $prev=$this->labels;
            $this->labels=FALSE;
            $args=array();
            $this->text($nameText, $value, $args);
            $this->labels=$prev;
            ?>
        </div>
    <?php }

    //create a checkbox with a left select visible only when the checkbox is selected
    public function checkSelect($nameActive, $nameArray, $value, $values) {
        global $irp;
        ?>
        <div id="<?php echo $nameArray?>Box" style="float:left;">
            <?php
            $args=array('class'=>'irp-hideShow irp-checkbox'
                , 'irp-hideIfTrue'=>'false'
                , 'irp-hideShow'=>$nameArray.'Tags');
            $this->checkbox($nameActive, $value, 1, $args);
            if(TRUE) { ?>
                <div id="<?php echo $nameArray?>Tags" style="float:left;">
                    <?php
                    $prev=$this->labels;
                    $this->labels=FALSE;
                    $args=array('class'=>'irp-select irpLineTags');
                    $this->select($nameArray, $value, $values, TRUE, $args);
                    $this->labels=$prev;
                    ?>
                </div>
            <?php } ?>
        </div>
    <?php
        $this->newline();
    }

    public function br() { ?>
        <br/>
    <?php }
    public function tag($overridePremium=FALSE) {
        global $irp;
        /*
            $premium=($overridePremium || $this->premium);
            if((!$overridePremium && !$this->tags) || $irp->License->hasPremium() || ($this->onlyPremium && !$premium)) return;

            $tagClass='irp-tag-free';
            $tagText='FREE';
            if($premium) {
                $tagClass='irp-tag-premium';
                $tagText='<a href="'.irp_PAGE_PREMIUM.'" target="_new">PRO</a>';
            }
        */

        if(!$this->tags || !$this->tagNew) {
            return;
        }

        $tagClass='irp-tag-free';
        $tagText='NEW!';
        ?>
        <div style="float:left;" class="irp-tag <?php echo $tagClass?>"><?php echo $tagText?></div>
    <?php
    }
}