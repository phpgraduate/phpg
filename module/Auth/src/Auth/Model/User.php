<?php
namespace Auth\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Crypt\Password\Bcrypt;

class User implements InputFilterAwareInterface {

        public $id;

        public $username;

        public $password;

        public $twitter;

		public $isAdmin;

		protected $inputFilter;

        public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password = (isset($data['password'])) ? md5( $data['password'] ) : null;
        $this->twitter  = (isset($data['twitter'])) ? $data['twitter'] : null;
		$this->isAdmin  = (isset($data['is_admin'])) ? $data['is_admin'] : null;
    }

    public function getArrayCopy()
    {
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
    	if (!$this->inputFilter) {
    		$inputFilter = new InputFilter();

    		$inputFilter->add(array(
    				'name'     => 'username',
    				'required' => true,
    				'filters'  => array(
    						array('name' => 'StripTags'),
    						array('name' => 'StringTrim'),
    				),
    				'validators' => array(
    						array(
    								'name'    => 'StringLength',
    								'options' => array(
    										'encoding' => 'UTF-8',
    										'min'      => 1,
    										'max'      => 100,
    								),
    						),
    				),
    		));

    		$inputFilter->add(array(
    				'name'     => 'password',
    				'required' => true,
    				'filters'  => array(
    						array('name' => 'StripTags'),
    						array('name' => 'StringTrim'),
    				),
    				'validators' => array(
    						array(
    								'name'    => 'StringLength',
    								'options' => array(
    										'encoding' => 'UTF-8',
    										'min'      => 5,
    										'max'      => 16,
    								),
    						),
    				),
    		));

    		$this->inputFilter = $inputFilter;
    	}

    	return $this->inputFilter;
    }

}