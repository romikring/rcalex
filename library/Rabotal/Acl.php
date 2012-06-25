<?php

class Rabotal_Acl extends Zend_Acl {
    
    public function canEditQuestion(Zend_Db_Table_Row $question, Zend_Db_Table_Row $user = NULL) {
        if ( NULL === $user )
            return FALSE;
        
        if ( $question->owner == $user->id ) {
            return TRUE;
        }
        
        if ( $this->isAllowed($user->role, 'operations', 'question-moderate') ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function canCommenting(Zend_Db_Table_Row $question, Zend_Db_Table_Row $user = NULL) {
        if ( NULL === $user )
            return FALSE;
        
        if ( $question->status != 'active' || !$this->isAllowed($user->role, 'question', 'add-answer') ) {
            return FALSE;
        }
        
        if ( $this->isAllowed($user->role, 'operations', 'question-moderate') ) {
            return TRUE;
        }
        
        if ( $question->answers_to_moderate > 0 ) {
            $answersTable = new Rabotal_Model_Answers;
            $answers = $answersTable->fetchAll('question = '.$question->id, 'date DESC', $question->answers_to_moderate);
            $count = $answers->count();
            if ( $count && $answers[$count - 1]->status != 'active' ) {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}
