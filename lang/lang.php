<?php
/* copyright: Stefan Müller
 * created: 2017-01-14
 * Quick start:
 * 1) copy / paste dir "lang" into the root of your application
 * 2) open bin/config.php and edit to your needs
 * 3) paste in your /index.php
 *          require __DIR__ . "/lang/lang.php";
 *          $lang = new lang(__DIR__);
 * 4) edit language files in /lang/lang and add new languages to your $conf in config.php
 * Do write a text from a language-file edit $lang->write("STRING") with STRING = Your expression-static
 * Do return a text for later use edit $lang->get("STRING")
 * To use dynamic elements:
 * a) edit string in language files and set {} for the dynamic element
 * b) use $lang->write or $lang->get to use the text. Edit offset with "(STRING_TO_REPLACE|DYNAMIC_VALUE)" e.g. "(AMOUNT|4)"
 * c) to use formatted values follow b) with offset "(STRING(FORMAT|DYNAMIC_VALUE))" e.g. "(APPOINTMENT(DATE|" . time() . "))"
 */
class lang {
    public static $lang;
    public $string;
    private static $conf;
    private $lang_arr;
    private $path;
    private $mode;
    public function __construct($root) {
        $this->path = $root . "/lang/";
        require_once $this->path . 'ini/config.php';
        $this->lang_arr = $conf;
        $this->mode = $mode;
        if($this->mode == "get" && isset($_GET["lang"]) 
                && isset($this->lang_arr[filter_input(INPUT_GET,"lang")])){
            $accept_lang = filter_input(INPUT_GET,"lang");
        }
        else if($this->mode == "cookie" && isset($_COOKIE["lang"]) && filter_input(INPUT_COOKIE,"lang") != ""){
            if(isset($_GET["lang"]) 
                && isset($this->lang_arr[filter_input(INPUT_GET,"lang")])){
                $accept_lang = filter_input(INPUT_GET,"lang");
                setcookie("lang",$accept_lang,time()+36000);
            }else{
                $accept_lang = filter_input(INPUT_COOKIE,"lang");
            }
        }
        else if(!isset($accept_lang)){
            $accept_lang = explode("-",explode(";",filter_input(INPUT_SERVER,"HTTP_ACCEPT_LANGUAGE"))[0])[1];
            if(!isset($this->lang_arr[$accept_lang])){
                reset($this->lang_arr);
                $accept_lang = key($this->lang_arr);
            }
            if($this->mode == "cookie"){
                setcookie("lang",$accept_lang,time()+36000);
            }
        }
        $this->conf = $this->lang_arr[$accept_lang];
        $this->lang = file_get_contents($this->path . "/lang/" . $this->conf . ".lang") ;
    }
    
    public function get($search) {
        if(strpos($search,"(") !== FALSE){
            $start_0 = strpos($search,"(");
            $end_0 = strrpos($search,")");
            $str_0 = substr($search,$start_0+1,$end_0-1 - $start_0);
            if(strpos($str_0,"(") !== FALSE){
                $start_1 = strpos($str_0,"(");
                $end_1 = strrpos($str_0,")");
                $str_1 = substr($str_0,$start_1+1,$end_1-1 - $start_1);
                if(strpos($str_1,"|") !== FALSE){
                    $str_arr = explode("|",$str_1);
                    $format = $str_arr[0];
                    $value = $str_arr[1];
                }else{
                    $format = "";
                    $value = $str_1;
                }
            }
            $search = substr($search,$start_0+1,$start_1);
        }
        $start = strpos($this->lang,"'",strpos($this->lang,"\n" . $search . "="));
        $end = strpos($this->lang,"'",$start + 1);
        $string = substr($this->lang,$start,$end - $start);
        $this->string = str_replace("'", "", $string);
        if(strpos($this->string,"{}") === FALSE){
            return $this->string;
        }else{
            $substring = explode("{}", $this->string);
            if($format != ""){
                return $substring[0] . $this->$format($value) . $substring[1];
            }else{
                return $substring[0] . $value . $substring[1];
            }
        }
    }

    public function write($search) {
        if(strpos($search,"(") !== FALSE){
            $start_0 = strpos($search,"(");
            $end_0 = strrpos($search,")");
            $str_0 = substr($search,$start_0+1,$end_0-1 - $start_0);
            if(strpos($str_0,"(") !== FALSE){
                $start_1 = strpos($str_0,"(");
                $end_1 = strrpos($str_0,")");
                $str_1 = substr($str_0,$start_1+1,$end_1-1 - $start_1);
                if(strpos($str_1,"|") !== FALSE){
                    $str_arr = explode("|",$str_1);
                    $format = $str_arr[0];
                    $value = $str_arr[1];
                }else{
                    $format = "";
                    $value = $str_1;
                }
            }
            $search = substr($search,$start_0+1,$start_1);
        }
        $start = strpos($this->lang,"'",strpos($this->lang,"\n" . $search . "="));
        $end = strpos($this->lang,"'",$start + 1);
        $string = substr($this->lang,$start,$end - $start);
        $this->string = str_replace("'", "", $string);
        if(strpos($this->string,"{}") === FALSE){
            print_r($this->string);
        }else{
            $substring = explode("{}", $this->string);
            if($format != ""){
                print_r($substring[0] . $this->$format($value) . $substring[1]);
            }else{
                print_r($substring[0] . $value . $substring[1]);
            }
        }
    }
    
    private function seperator(){
        $start = strpos($this->lang,"'",strpos($this->lang,"%SEPERATOR="));
        $end = strpos($this->lang,"'",$start + 1);
        $seperator = substr($this->lang, $start,$end - $start);
        $result = str_replace("'", "", $seperator);
        return $result;
    }
    private function decimal(){
        $start = strpos($this->lang,"'",strpos($this->lang,"%DECIMAL="));
        $end = strpos($this->lang,"'",$start + 1);
        $decimal = substr($this->lang, $start,$end - $start);
        $result = str_replace("'", "", $decimal);
        return $result;
    }
    
    private function currency($value){
        $form_s = strpos($this->lang,"'",strpos($this->lang,"%CURRENCY" . "="));
        $form_e = strpos($this->lang,"'",$form_s + 1);
        $form_pattern = substr($this->lang, $form_s,$form_e - $form_s);
        $form_pattern = str_replace("'", "", $form_pattern);
        return number_format($value, 2 , $this->decimal(), $this->seperator()) . " " . $form_pattern;
    }

    private function date($value){
        $form_s = strpos($this->lang,"'",strpos($this->lang,"%DATE" . "="));
        $form_e = strpos($this->lang,"'",$form_s + 1);
        $form_pattern = substr($this->lang, $form_s,$form_e - $form_s);
        $form_pattern = str_replace("'", "", $form_pattern);
        return date($form_pattern, $value);
    }
    
    public function lang_switch($choice){
        $switch = file_get_contents($this->url . "/ini/switch.php");
        $get = $_GET;
        //var_dump($_SERVER);
        $url = filter_input(INPUT_SERVER,"PHP_SELF");
        if(count($get) > 0){
            $url .= "?";
            foreach($get as $k => $v){
                if($k != "lang") $url .= $k . "=" . $v . "&lang=";
            }
        }else{
            $url .= "?lang=";
        }
        $html = "";
        foreach($choice as $k => $v){
            $html .= '<a href="' . $url . $k . '">' . $v . '</a><br>';
        }
        print_r($html);
        
        
        
    }
    
}