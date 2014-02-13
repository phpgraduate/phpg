<?php
namespace Auth\Form;

use Zend\Form\Form,
    Zend\Form\Element,
    Zend\InputFilter\Factory as InputFactory,
        Zend\InputFilter\InputFilter;

class Signup extends Form
{
    public function __construct($name = null)
    {
		parent::__construct('signup');

         $this->add(array(
             'name' => 'csrf',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'username',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Username',
             ),
         ));
         $this->add(array(
             'name' => 'password',
             'type' => 'password',
             'options' => array(
                 'label' => 'Password',
             ),
         ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
		 
      /*  parent::__construct('signup');
        $this->setAttribute('method', 'post');
        
               $username = new Element\Text('username');
               $username->setLabel('Username')
                        ->setAttribute('size', '32');
               $this->add($username);
                        
        $password = new Element\Password('password');
        $password->setLabel('Password')
                         ->setAttribute('size', '10');
               $this->add($password);
                
               $csrf = new Element\Csrf('csrf');
               $this->add($csrf);
               
               $submit = new Element\Submit('submit');
               $submit->setValue('Log In');
               $this->add($submit);

                // set InputFilter
        $inputFilter = new InputFilter();
        $factory = new InputFactory();
               $inputFilter->add($factory->createInput(array(
                        'name'        => 'username',
                        'required' => true,
                        'filters'  => array(
                                        array('name' => 'StringTrim'),
                                ),
                        'validators' => array(
                                        array(
                                              'name' => 'StringLength',
                                                  'options' => array(
                                                                'min' => 8
                                                        )
                                        )
                                )
                )));
         $inputFilter->add($factory->createInput(array(
                        'name'        => 'password',
                        'required' => true,
                        'validators' => array(
                                        array(
                                                'name' => 'StringLength',
                                                'options' => array(
                                                                'min' => 5
                                                        )
                                        )
                                )
                )));
        $inputFilter->add($factory->createInput(array(
                        'name'        => 'csrf',
                        'required' => true,
                        'validators' => array(
                                        array(
                                                'name' => 'Csrf',
                                                'options' => array(
                                                                'timeout' => 600
                                                        )
                                        )
                                )
                )));
        $this->setInputFilter($inputFilter);*/
    }
}