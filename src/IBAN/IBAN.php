<?php

namespace IBAN;

class IBAN
{
	/*
	private $ibanAsString;
	private $locale;
	private $checksum;
	private $bankcode;
	private $accountType
	private $accountNumber;
	private $controlNumber;
	private $regionCode;
	private $branchCode;
	*/
	
	private $iban;

	private $letterMapping = array(
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 
        5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H',
        9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 
        13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 
        17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 
        21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 
        25 => 'Y', 26 => 'Z'
    );

	private $ibanFormatMap = array(
	    'AL'=>'[0-9]{8}[0-9A-Z]{16}', 
		'AD'=>'[0-9]{8}[0-9A-Z]{12}',
	    'AT'=>'[0-9]{16}',
	    'BE'=>'[0-9]{12}',
	    'BA'=>'[0-9]{16}',
	    'BG'=>'[A-Z]{4}[0-9]{6}[0-9A-Z]{8}',
	    'HR'=>'[0-9]{17}',
	    'CY'=>'[0-9]{8}[0-9A-Z]{16}',
	    'CZ'=>'[0-9]{20}',
	    'DK'=>'[0-9]{14}',
	    'EE'=>'[0-9]{16}',
	    'FO'=>'[0-9]{14}',
	    'FI'=>'[0-9]{14}',
	    'FR'=>'[0-9]{10}[0-9A-Z]{11}[0-9]{2}',
	    'GE'=>'[0-9A-Z]{2}[0-9]{16}',
	    'DE'=>'[0-9]{18}',
	    'GI'=>'[A-Z]{4}[0-9A-Z]{15}',
	    'GR'=>'[0-9]{7}[0-9A-Z]{16}',
	    'GL'=>'[0-9]{14}',
	    'HU'=>'[0-9]{24}',
	    'IS'=>'[0-9]{22}',
	    'IE'=>'[0-9A-Z]{4}[0-9]{14}',
	    'IL'=>'[0-9]{19}',
	    'IT'=>'[A-Z][0-9]{10}[0-9A-Z]{12}',
	    'KZ'=>'[0-9]{3}[0-9A-Z]{3}[0-9]{10}',
	    'KW'=>'[A-Z]{4}[0-9]{22}',
	    'LV'=>'[A-Z]{4}[0-9A-Z]{13}',
	    'LB'=>'[0-9]{4}[0-9A-Z]{20}',
	    'LI'=>'[0-9]{5}[0-9A-Z]{12}',
	    'LT'=>'[0-9]{16}',
	    'LU'=>'[0-9]{3}[0-9A-Z]{13}',
	    'MK'=>'[0-9]{3}[0-9A-Z]{10}[0-9]{2}',
	    'MT'=>'[A-Z]{4}[0-9]{5}[0-9A-Z]{18}',
	    'MR'=>'[0-9]{23}',
	    'MU'=>'[A-Z]{4}[0-9]{19}[A-Z]{3}',
	    'MC'=>'[0-9]{10}[0-9A-Z]{11}[0-9]{2}',
	    'ME'=>'[0-9]{18}',
	    'NL'=>'[A-Z]{4}[0-9]{10}',
	    'NO'=>'[0-9]{11}',
	    'PL'=>'[0-9]{24}',
	    'PT'=>'[0-9]{21}',
	    'RO'=>'[A-Z]{4}[0-9A-Z]{16}',
	    'SM'=>'[A-Z][0-9]{10}[0-9A-Z]{12}',
	    'SA'=>'[0-9]{2}[0-9A-Z]{18}',
	    'RS'=>'[0-9]{18}',
	    'SK'=>'[0-9]{20}',
	    'SI'=>'[0-9]{15}',
	    'ES'=>'[0-9]{20}',
	    'SE'=>'[0-9]{20}',
	    'CH'=>'[0-9]{5}[0-9A-Z]{12}',
	    'TN'=>'[0-9]{20}',
	    'TR'=>'[0-9]{5}[0-9A-Z]{17}',
	    'AE'=>'[0-9]{19}',
	    'GB'=>'[A-Z]{4}[0-9]{14}'
	);


	public function __construct($iban)	{
		if (!empty($iban)) {
			$this->iban = $iban;
		} else {
			throw \InvalidArgumentException('iban is missing');
		}
	}


	public function isValid() {	
		if (!$this->hasIbanValidLenght()) {
			return false;
		}
		
		if (!$this->hasIbanValidLocaleCode()) {
			return false;
		}
		
		if (!$this->hasIbanValidFormat()) {
			return false;
		}
		
		if (!$this->hasIbanValidChecksum())	{
			return false;
		}
		
		return true;
	}

	private function hasIbanValidLenght() {
		return strlen($this->iban) < 15 ? false : true;
	}
	
	private function hasIbanValidLocaleCode() {
		$localeCode = $this->getLocaleCode();
		return !(isset($this->ibanFormatMap[$localeCode]) === false);
	}
	
	private function hasIbanValidFormat() {
		$localeCode = $this->getLocaleCode();
		$accountIdentification = $this->getAccountIdentification();
        return !(preg_match('/' . $this->ibanFormatMap[$localeCode] . '/', $accountIdentification) !== 1);
	}
	
	private function hasIbanValidChecksum() {
		$localeCode = $this->getLocaleCode();
		$numericLocalCode = $this->getNumericLocaleCode($localeCode);
		$accountIdentification = $this->getAccountIdentification();
		$numericAccountIdentification = $this->getNumericAccountIdentification($accountIdentification);
		$checksum = $this->getChecksum();
        $invertedIban = $numericAccountIdentification . $numericLocalCode . $checksum;        
		return bcmod($invertedIban, 97) === '1';
	}

	private function getLocaleCode() {
		return substr($this->iban, 0, 2);
	}
	
	private function getChecksum() {
		return substr($this->iban, 2, 2);
	}
	
	private function getAccountIdentification() {
		return substr($this->iban, 4);
	}
	
	private function getNumericLocaleCode($localeCode) {
		$numericLocaleCode = '';
        foreach(str_split($localeCode) as $char) {
            $numericLocaleCode .= array_search($char, $this->letterMapping) + 9;
        }
        return $numericLocaleCode;
    }
    
    private function getNumericAccountIdentification($accountIdentification) {
    	$accountIdentificationWithReplacedLetters = '';
    	foreach(str_split($accountIdentification) as $char) {
    		if (array_search($char, $this->letterMapping)) {
    			$accountIdentificationWithReplacedLetters .= array_search($char, $this->letterMapping) + 9;
    		} else {
    			$accountIdentificationWithReplacedLetters .= $char;
    		}
    	}
    	return $accountIdentificationWithReplacedLetters;
    }
}
