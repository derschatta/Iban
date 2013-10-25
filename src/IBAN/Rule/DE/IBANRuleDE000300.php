<?php

namespace IBAN\Rule\DE;

class IBANRuleDE000300 extends \IBAN\Rule\DE\IBANRuleDE000000
{    
    public function generateIban() {
    	if (strcmp($this->bankAccountNumber, '6161604670') == 0) {
            return '';
        } else {
            return parent::generateIban();
        }
    }
}
