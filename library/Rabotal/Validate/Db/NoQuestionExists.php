<?php

class Rabotal_Validate_Db_NoQuestionExists extends Rabotal_Validate_Db_Abstract {
    public function isValid($value) {
        $normalizaer = new Rabotal_Filter_Question_Normalize;
        $url = $normalizaer->filter($value);
        
        $valid = true;
        $this->_setValue($value);

        $result = $this->_query($url);
        if ($result) {
            $valid = false;
            $this->_error(self::ERROR_QUESTION_FOUND);
        }

        return $valid;
    }
}
