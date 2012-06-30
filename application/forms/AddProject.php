<?php

class Rabotal_Form_AddProject extends Zend_Form
{

    public function init()
    {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('site');
        
        $this
            ->setAction("/project/add")
            ->setMethod('post')
            ->setName('project-add')
            ->setAttrib('id', 'project-add');
        
        $this->addElement('text', 'name', array(
            'required' => true,
            'label' => 'Название проекта',
            'validators' => array(
                'NotEmpty',
                array('StringLength', false, array('max' => 70, 'encoding' => $options['default']['charset']))
            ),
            'filters' => array(
                'StringTrim',
                'StripNewLines',
                'StripTags'
            )
        ));
        
        $this->addElement('select', 'category', array(
            'required' => true,
            'label' => 'Выберите специализацию',
            'validators' => array(
                'NotEmpty',
                'Int'
            ),
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim',
                'Int'
            )
        ));
        $select = $this->getElement('category');
        $select->addMultiOption('', 'Выбрать категорию услуг');
        
        $this->addElement('select', 'sub_category', array(
            'required' => false,
            'label' => 'Уточните специализацию',
            'class' => 'inactive',
            'validators' => array('Int'),
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim',
                'Int'
            )
        ));
        $select = $this->getElement('sub_category');
        $select->addMultiOption('', 'Выбрать подкатегорию');
        
        $this->addElement('textarea', 'description', array(
            'required' => true,
            'label' => 'Описание проекта',
            'validators' => array(
                'NotEmpty',
                array('StringLength', false, array('max' => 550, 'encoding' => $options['default']['charset']))
            ),
            'filters' => array(
                'StripTags',
                'StringTrim'
            )
        ));
        
        $this->addElement('text', 'budget', array(
            'required' => false,
            'label' => 'Бюджет проекта',
            'class' => 'small',
            'validators' => array(
                array('StringLength', false, array('max' => 30, 'encoding' => $options['default']['charset']))
            ),
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim'
            )
        ));
        
        $this->addElement('select', 'period', array(
            'required' => true,
            'label' => 'Период приема заявок',
            'validators' => array(
                'Int'
            ),
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim',
                'Int'
            )
        ));
        $period = $this->getElement('period');
        $period->setMultiOptions(array(
            1 => '1 неделя',
            2 => '2 недели',
            3 => '3 недели',
            4 => '4 недели',
            5 => '5 недель',
            6 => '6 недель'
        ));
        
        $this->addElement('text', 'employee_place', array(
            'required' => false,
            'label' => 'Местонахождение исполнителя',
            'class' => 'small',
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim'
            )
        ));
        
        $this->addElement('text', 'email', array(
            'required' => true,
            'label' => '',
            'placeholder' => 'Электронная почта',
            'class' => 'small',
            'validators' => array(
                'NotEmpty',
                array('StrindLength', false, array('max' => 60, 'encoding' => $options['default']['charset'])),
                'EmailAddress',
                array('Db_NoRecordExists', false, array('table' => 'users', 'field' => 'email')),
            ),
            'filters' => array(
                'StripTags',
                'StripNewLines',
                'StringTrim'
            )
        ));
        
        $this->addElement('password', 'password', array(
            'required' => true,
            'label' => '',
            'placeholder' => 'Пароль',
            'class' => 'small',
            'validators' => array(
                array('NotEmpty', false, array()),
                array('StringLength', false, array('min' => 5))
            ),
            'filters' => array('StringTrim')
        ));
        
        $this->addElement('password', 'retype', array(
            'required' => true,
            'label' => '',
            'placeholder' => 'Подтверждение пароля',
            'class' => 'small',
            'validators' => array(
                array('Identical', false, array('strict' => true, 'token' => 'password'))
            )
        ));
    }
}
