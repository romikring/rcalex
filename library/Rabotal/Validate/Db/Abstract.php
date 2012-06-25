<?php

abstract class Rabotal_Validate_Db_Abstract extends Zend_Validate_Db_Abstract {
 
    const ERROR_NO_QUESTION_FOUND = 'noQuestionFound';
    const ERROR_QUESTION_FOUND    = 'questionFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_QUESTION_FOUND => "Вопрос с названием '%value%' не найден",
        self::ERROR_QUESTION_FOUND    => "Вопрос '%value%' уже задан"
    );   
}
