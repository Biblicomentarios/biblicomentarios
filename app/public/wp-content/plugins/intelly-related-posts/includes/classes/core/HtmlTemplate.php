<?php
if (!defined('ABSPATH')) exit;

class IRP_HtmlTemplate {
    var $templates;
    var $defaults;

    public function __construct() {
        $this->templates=array();
        $this->defaults=array();
    }

    public function getTemplates() {
        return $this->templates;
    }
    public function getDefaults() {
        return $this->defaults;
    }
    public function getTemplatesNames() {
        $array=array_keys($this->templates);
        asort($array);
        return $array;
    }
    public function getFields($name) {
        return $this->getFieldsOrBody($name);
    }
    public function getBody($name, $values=array()) {
        return $this->getFieldsOrBody($name, $values);
    }
    private function getFieldsOrBody($name, $values=array()) {
        global $irp;

        if(!is_array($values)) {
            $values=array();
        }

        $buffer='';
        $fields=array();
        $fields['template']='template';
        if(isset($this->templates[$name])) {
            $body=$this->templates[$name];
            $len=strlen($body);
            $previous=0;
            while($previous<$len) {
                $start=strpos($body, '{', $previous);
                if($start!==FALSE) {
                    if($previous!=$start) {
                        $text=$irp->Utils->substr($body, $previous, $start);
                        $buffer.=$text;
                    }
                    $end=strpos($body, '}', $start+1);
                    $another=strpos($body, '{', $start+1);

                    if($end!==FALSE) {
                        if($another!==FALSE && $another<$end) {
                            $text=$irp->Utils->substr($body, $start, $another);
                            $buffer.=$text;
                            $previous=$another;
                        } else {
                            $k=$irp->Utils->substr($body, $start+1, $end);
                            if(trim($k)!='' && strpos($k, "\n")===FALSE) {
                                if($irp->Utils->startsWith($k, 'if ')) {
                                    $k=substr($k, 3);
                                    $fields[$k]=$k;
                                    $v=$irp->Utils->get($values, $k, '#'.$k.'??#');
                                    if($irp->Utils->isTrue($v)) {
                                        //ok continue analysing the rest of template
                                        //WARN: currently no nested if are allowed
                                    } else {
                                        $if='{/if}';
                                        $start=strpos($body, $if, $end+1);
                                        if($start===FALSE) {
                                            $irp->Log->error("CHECK THE TEMPLATE. NO {/if} FOUND FOR {if %s}", $k);
                                            break;
                                        } else {
                                            //skip all the text between {if xxx} and {/if}
                                            $end=$start+strlen($if)-1;
                                        }
                                    }
                                } elseif($irp->Utils->startsWith($k, '/if')) {
                                    //nothing
                                } else {
                                    $fields[$k]=$k;
                                    $v=$irp->Utils->get($values, $k, '#'.$k.'??#');
                                    $buffer.=$v;
                                }
                            } else {
                                $text=$irp->Utils->substr($body, $start, $end+1);
                                $buffer.=$text;
                            }
                            $previous=$end+1;
                        }
                    } else {
                        $text=substr($body, $previous);
                        $buffer.=$text;
                        break;
                    }
                } else {
                    $text=substr($body, $previous);
                    $buffer.=$text;
                    break;
                }
            }
        }
        $fields=array_keys($fields);
        $buffer=str_replace("\n\n", "\n", $buffer);
        $result=$buffer;
        if(!is_array($values) || $values==NULL) {
            $result=$fields;
        }
        return $result;
    }

    //load html template from file system stored in .html files
    public function load($file) {
        if(!file_exists($file)) {
            return;
        }

        $matchTemplate='#template';
        $matchDefaults='#defaults';

        $file=file_get_contents($file);
        if($file!=NULL && strlen($file)>0) {
            $file=str_replace("\r\n", "\n", $file);
            $file=str_replace("\n\n", "\n", $file);
            $file=explode("\n", $file);

            $name='';
            $html='';
            foreach($file as $row) {
                if(stripos($row, $matchTemplate)!==FALSE) {
                    if($html!='' && $name!='') {
                        $this->templates[$name]=$html;
                    }

                    $name=substr($row, strlen($matchTemplate)+1);
                    $name=trim($name);
                    $html='';
                    continue;
                }
                elseif(stripos($row, $matchDefaults)!==FALSE && $name!='') {
                    if($html!='' && $name!='') {
                        $this->templates[$name]=$html;
                    }

                    $defaults=substr($row, strlen($matchDefaults)+1);
                    $defaults=str_replace('{', '', $defaults);
                    $defaults=str_replace('}', '', $defaults);
                    $defaults=explode(',', $defaults);
                    $tmp=array();
                    foreach($defaults as $v) {
                        $v=explode('=', $v);
                        if(count($v)>1) {

                        }
                        $tmp[trim($v[0])]=trim($v[1]);
                    }
                    $this->defaults[$name]=$tmp;
                    continue;
                }

                if($name=='') {
                    continue;
                }

                $html.=$row."\n";
            }

            if($html!='' && $name!='') {
                $this->templates[$name]=$html;
            }
        }
    }
    public function html($name, $values, $options='') {
        global $irp;
        $defaults=array(
            'includeCss'=>TRUE
        );
        $options=$irp->Utils->parseArgs($options, $defaults);

        if(!is_array($values)) {
            $values=array();
        }
        $values['template']=str_replace(' ', '-', $name);
        $values['utemplate']=$irp->Options->getTemplateUUID($values['template']);
        $values['assetsImages']=IRP_PLUGIN_ASSETS.'images/';
        $values['assets']=IRP_PLUGIN_ASSETS;
        $values['demoLink']=irp_preview_link();

        if(isset($values['linkRel'])) {
            $values['linkRel']=$values['linkRel'];
            if($values['linkRel']!='nofollow') {
                $values['linkRel']='';
            }
        }
        $values['linkRel']=($values['linkRel']!='' ? 'rel="'.$values['linkRel'].'"' : '');

        $style=array();
        $code='';
        if(isset($this->templates[$name])) {
            foreach($values as $k=>$v) {
                if(stripos($k, 'color')!==FALSE) {
                    $values[$k]=$irp->Options->getColor($values[$k]);
                    if($values[$k]=='') {
                        $values[$k]='inherit';
                    }
                    $values[$k.'Hover']=$irp->Options->getHoverColor($values[$k]);
                    if($values[$k.'Hover']=='') {
                        $values[$k.'Hover']='inherit';
                    }
                } elseif(stripos($k, 'opacity')!==FALSE) {
                    $values[$k]=(intval($values[$k])/100);
                }
                $values[$k.'Display']=($irp->Utils->isTrue($values[$k]) ? 'block' : 'none');
                $values[$k.'Visibility']=($irp->Utils->isTrue($values[$k]) ? 'visible' : 'hidden');
            }
            if(isset($values['comment']) && trim($values['comment'])!=''
                && !$irp->Utils->startsWith($values['comment'], "<!--")) {
                $values['comment']="<!-- ".trim($values['comment'])." //-->";
            }

            if($options['includeCss']) {
                //generate unique id from all the combinations, this grant to use the same theme
                //but with different color using shotcodes
                $values['utemplate']=$irp->Utils->getUUID($values);
            }
            $code=$this->getBody($name, $values);
            if(!$options['includeCss']) {
                $array=explode("\n", $code);
                $code=array();

                $css=FALSE;
                foreach($array as $row) {
                    if (stripos($row, '<style>') !== FALSE) {
                        $css = TRUE;
                        if(!$options['includeCss']) {
                            $style[]=$row;
                            $row='';
                        }
                    } elseif (stripos($row, '</style>') !== FALSE) {
                        $css = FALSE;
                        if(!$options['includeCss']) {
                            $style[]=$row;
                            $row='';
                        }
                    } elseif ($css) {
                        if(!$options['includeCss']) {
                            $style[]=$row;
                            $row='';
                        }
                    }

                    if(trim($row)!='') {
                        $code[]=$row;
                    }
                }
                $code=implode("\n", $code);
                if(count($style)>0) {
                    $irp->Options->pushCssStyle($style);
                }
            }
        }

        $code=$irp->Utils->trimCode($code);
        if($code!='') {
            if(isset($values['hasPoweredBy']) && $values['hasPoweredBy']) {
                $code.='<div style="width:100%; text-align:right; font-weight:normal;">';
                $code.='<div style="font-size:10px;">';
                $code.='<span class="poweredByText">Powered by</span>';
                $code.='&nbsp;';
                $code.='<a rel="nofollow" style="font-weight:bold;" href="https://wordpress.org/plugins/intelly-related-posts/" target="_blank">';
                $code.='Inline Related Posts';
                $code.='</a>';
                $code.='</div>';
                $code.='</div>';
                $code.='<div style="clear:both"></div>';
            }
            $mt=$irp->Options->getMarginTop();
            $mb=$irp->Options->getMarginBottom();
            $code='<div style="clear:both; margin-top:'.$mt.'; margin-bottom:'.$mb.';">'.$code.'</div>';
            //$code.="\n";
            //$code.="<br>";
        }

        $defaults=$irp->HtmlTemplate->getDefaults();
        $defaults=$defaults[$options['template']];
        if($code!='' && isset($defaults['proTheme']) && $irp->Utils->isTrue($defaults['proTheme'])) {
            $code.='<p style="text-align:center;">';
            $code.='<a style="font-size:20px; color:green; font-weight:bold;" href="'.$values['demoLink'].'" rel="nofollow" target="_blank">';
            $code.='Click here to preview your posts with PRO themes ››';
            $code.='</p>';
            $code.='</a>';
        }
        return $code;
    }
}