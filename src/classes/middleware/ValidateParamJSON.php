<?php 
/**
 * This class is a part of middleware reSlim project to make validation for body parameter in JSON request using Regular Expression
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes\middleware;
use \classes\CustomHandlers as CustomHandlers;
use \classes\Cors as Cors;
use \classes\JSON as JSON;
    /**
     * A class for validation the body parameter in JSON request using Regular Expression
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class ValidateParamJSON
    {
        private $parameter,$between,$regex,$message,$length,$error;

        /**
         * Constructor
         * 
         * @var parameter is the body of the request parameter
         * @var between is the min and max chars length of the parameter. Default is empty means unlimited chars and allow empty chars.
         * @var regex is to validate the value of the parameter. See the validateRegex function for the shortcut regex. Default is empty.
         */
        public function __construct($parameter,$between='',$regex=''){
            $this->parameter = $parameter;
            $this->regex = $regex;
            $this->between = $between;
        }

        public function __invoke($request, $response, $next){
            if($this->validate($request,$this->parameter,$this->between,$this->regex)){
                $response = $next($request, $response);    
                return $response;
            } else {
                $body = $response->getBody();
                if (empty($this->message)){
                    if (empty($this->error)){
                        if ($this->regex == 'json'){
                            $body->write(json_encode(['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801'), 'description' => 'If the value is in json format, You have to encode it.']));
                        } else {
                            $body->write(json_encode(['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801')]));
                        }
                    } else {
                        $body->write(json_encode(['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801'), 'description' => $this->error]));
                    }
                } else {
                    if (empty($this->length)){
                        $body->write(json_encode(['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801'),'parameter' => $this->message]));
                    } else {
                        $body->write(json_encode(['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801'),'parameter' => $this->message,'length' => $this->length]));
                    }
                }
                return Cors::modify($response,$body,400);
            }
        }

        private function validateRegex($regex,$key,$value){
            switch($regex){
                case 'required':
                    $regex = '/.*\S.*/';
                    $msg = 'This field is required. Blank, empty or whitespace value is not allowed!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'alphanumeric':
                    $regex = '/^[a-zA-Z0-9]+$/';
                    $msg = 'The value is not alphanumeric!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'alphabet':
                    $regex = '/^[a-zA-Z]+$/';
                    $msg = 'The value is not alphabet!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'decimal':
                    $regex = '/^[+-]?[0-9]+(?:\.[0-9]+)?$/';
                    $msg = 'The value is not decimal!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'notzero':
                    $regex = '/^[1-9][0-9]*$/';
                    $msg = 'Only zero value is not allowed!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'numeric':
                    $regex = '/^[0-9]+$/';
                    $msg = 'The value is not numeric!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'double':
                    $regex = '/^[+-]?[0-9]+(?:,[0-9]+)*(?:\.[0-9]+)?$/';
                    $msg = 'The value is not numeric (double)!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'username':
                    $regex = '/^[a-zA-Z0-9]{3,20}$/';
                    $msg = 'The value is not username allowed format!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'email':
                    $regex = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
                    $msg = 'The value is not email address allowed format!';
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'json':
                    $msg = 'The value is not valid json format!';
                    return $this->jsonTest($key,$value,$msg);
                default:
                    $regex = $regex;
                    $msg = 'The value is not using valid format!';
                    return $this->regexTest($regex,$key,$value,$msg);
            }
        }

        private function regexTest($regex,$key,$value,$msg){
            if(!preg_match($regex, $value)){
                $this->message[$key] = $msg;
                return false;
            }
            return true;
        }

        private function jsonTest($key,$value,$msg){
            if (JSON::isValid($value) == false){
                $this->message[$key] = $msg;
                return false;
            }
            return true;
        }

        private function validateBetween($key,$value,$between=''){
            $between = str_replace(' ','',$between);
            if(!empty($between)){
                if(strpos($between,'-') !== false){
                    if(substr_count($between, '-') == 1){
                        $data = explode('-',$between);
                        if (!empty($data[0])){
                            $min = $data[0];
                            $max = $data[1];
                            $total = strlen($value);
                            if ($total >= $min && $total <= $max){
                                return true;
                            } else {
                                $this->message[$key] = 'Chars length is should be between '.$min.' - '.$max.' only!';
                                $this->length[$key] = $total;
                                return false;
                            }
                        } else {
                            $this->message[$key] = 'Failed to determine between chars length!';
                            return false;
                        }
                    } else {
                        $this->message[$key] = 'Wrong format between occured! The format is "min-max".';
                        return false;
                    }
                } else {
                    $this->message[$key] = 'Wrong format between occured! The format is "min-max".';
                    return false;
                }
            }
            return true;
        }

        private function valueTest($parameter,$data,$between='',$regex=''){
            $count = 0;
            if (is_array($parameter)){
                $aa = 0;
                foreach ($parameter as $singleparam){
                    $tt = 0;
                    foreach ($data as $key => $value) {
                        if (is_array($value)){
                            $r = $this->valueTest($parameter,$value,$between,$regex);
                            if ($r > 0){
                                $tt += 1;
                            }
                        } else if(is_string($value)){
                            if ($key==$singleparam){
                                if ($this->validateBetween($key,$value,$between)){
                                    if (!empty($regex)){
                                        if($this->validateRegex($regex,$key,$value)){
                                            $tt += 1;    
                                        }
                                    } else {
                                        $tt += 1;
                                    }
                                }
                            }
                        }
                    }
                    if($tt == 0) $aa += 1;
                }
                if($aa > 0) {
                    $count += 0;
                } else {
                    $count += 1;
                }
            } else if(is_string($parameter)) {
                $tt=0;
                foreach ($data as $key => $value) {
                    if (is_array($value)){
                        $r = $this->valueTest($parameter,$value,$between,$regex);
                        if ($r > 0){
                            $tt += 1;
                        }
                    } else if (is_string($value)){
                        if ($key==$parameter){
                            if (validateBetween($key,$value,$between)){
                                if (!empty($regex)){
                                    if($this->validateRegex($regex,$key,$value)){
                                        $tt += 1;    
                                    }
                                } else {
                                    $tt += 1;
                                }
                            }
                        }
                    }
                }
                if($tt > 0){
                    $count += 1;
                } else {
                    $count += 0;
                }
            }
            return $count;
        }

        private function validate($request,$parameter,$between='',$regex=''){
            $data = $request->getBody();
            if (empty($data)) {
                $this->error = 'The body of request json is empty! Maybe you got the wrong way to send your json request into our server.';
            } else {
                $parsedBody = json_decode($data,true);
                if (empty($parsedBody)) {
                    $this->error = ['info'=>'Corrupted, malformed or empty request json!','debug' => json_decode(JSON::debug_decode($data,true)),'encoded_request'=>$data];
                } else {
                    if ($this->valueTest($parameter,$parsedBody,$between,$regex) > 0) return true;
                }
            }
            $this->error = ['info'=>'Some parameter is required!','required'=>$parameter];
            return false;
        }
    }