<?php
namespace app\data;

use app\security\Captcha;
use app\security\Token;

final class Validate {
    private $_db = null;
    private $_errors = array();
    private $_passed = false;
    private $_ip = null;
    private $_page = null;
    private $_browser = null;
    
    public function __construct(string $ip, string $page, string $browser) {
        $this->_db = Database::getInstance();
        $this->_ip = $ip;
        $this->_page = $page;
        $this->_browser = $browser;
    }
    
    public function check(array $source, array $items, bool $isToken = false, bool $isCaptcha = false) : void {
        if($isToken && !Token::check(Input::get('token'), $this->_page, $this->_ip, $this->_browser)) {
            $this->addError('Token de seguridad no válido.');
        } else if($isCaptcha && !Captcha::check(Input::get('captcha'))) {
            $this->addError('Captcha de seguridad no válido.');
        } else {
            foreach($items as $item => $rules) {
                foreach($rules as $rule => $ruleValue) {
                    $ruleValue = !empty($ruleValue) && is_string($ruleValue) ? trim($ruleValue) : $ruleValue;
                    $sourceValue = Input::exists($item) && is_string(Input::get($item)) ? trim(Input::get($item)) : Input::get($item);
                    $label = !empty($rules['label']) ? $rules['label'] : $item;
                    if($rule === 'required' && empty($sourceValue)) {
                        $this->addError(sprintf(LANG_REQUIRED_FIELD, $label));
                    } else if(!empty($sourceValue)){
                        switch($rule) {
                            case 'matches':
                                if($sourceValue != $source[$ruleValue]) {
                                    $label2 = !empty($items[$ruleValue]['label']) ? $items[$ruleValue]['label'] : $ruleValue;
                                    $this->addError(sprintf(LANG_VALUE_MUST_MATCH, $label, $label2));
                                }
                                break;
                            case 'max':
                                if(strlen($sourceValue) > $ruleValue) {
                                    $this->addError(sprintf(LANG_FIELD_MAX_LENGTH, $label, $ruleValue));
                                }
                                break;
                            case 'min':
                                if(strlen($sourceValue) < $ruleValue) {
                                    $this->addError(sprintf(LANG_FIELD_MIN_LENGTH, $label, $ruleValue));
                                }
                                break;
                            case 'size':
                                if(strlen($sourceValue) != $ruleValue) {
                                    $this->addError(sprintf(LANG_FIELD_LENGTH, $label, $ruleValue));
                                }
                                break;
                            case 'pattern':
                                $this->_checkPattern($label, $sourceValue, $ruleValue);
                                break;
                            case 'values':
                                $sourceValue = is_array($sourceValue) ? $sourceValue : array($sourceValue);
                                if(!is_array($ruleValue) || is_array($sourceValue) && !$this->_checkArrayValues($sourceValue, $ruleValue)) {
                                    $this->addError(sprintf(LANG_FIELD_ILLEGAL_VALUE, $label));
                                }
                                break;
                        }
                    }
                }
            }
        }
        if(empty($this->_errors)) {
            $this->_passed = true;
        }
    }
    
    private function _checkArrayValues(array $values, array $allowedValues) : bool {
        foreach($values as $value) {
            if(!in_array($value, $allowedValues)) {
                return false;
            }
        }
        return true;
    }
    
    public function errors() : array {
        return $this->_errors;
    }
    
    public function passed() : bool {
        return $this->_passed;
    }
    
    private function addError(string $message) : void {
        $this->_errors[] = $message;
    }
    
    private function _checkPattern(string $inputName, string $inputValue, string $patternName) : void {
        switch($patternName) {
            case 'date':
                if(!\DateTime::createFromFormat('Y-m-d', $inputValue)) {
                    $this->addError(sprintf(LANG_FIELD_INVALID_DATEFORMAT, $inputName));
                }
            break;
            case 'day_of_month':
                if(!preg_match('/^(3[01]|[12][0-9]|[1-9])$/', $inputValue)) {
                    $this->addError(sprintf(LANG_RANGE_NUMBER, $inputName, 1, 31));
                }
            break;
            case 'month_of_year':
                if(!preg_match('/^(1[0-2]|[1-9])$/', $inputValue)) {
                    $this->addError(sprintf(LANG_RANGE_NUMBER, $inputName, 1, 12));
                }
            break;
            case 'integer':
                if(!ctype_digit($inputValue)) {
                    $this->addError(sprintf(LANG_FIELD_NOT_INTEGER, $inputName));
                }
            break;
            case 'password':
                if(!preg_match('#.*^(?=.{8,24})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#', $inputValue)) {
                    $this->addError(sprintf(LANG_FIELD_PASSWORD_FORMAT, $inputName));
                }
            break;
            case 'username':
                if(!preg_match('/^[a-z\d_]{4,20}$/i', $inputValue)) {
                    $this->addError(sprintf(LANG_FIELD_USERNAME_FORMAT, $inputName));
                }
            break;
        }
    }
}
