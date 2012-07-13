<?php 
class ibftool_Controller_Plugin_Auth_Acl extends Zend_Acl
{
  public function __construct()
  {  // RESSOURCES
    $this->add(new Zend_Acl_Resource('index'));
    $this->add(new Zend_Acl_Resource('login'));
    $this->add(new Zend_Acl_Resource('registration'));
    $this->add(new Zend_Acl_Resource('error'));
    $this->add(new Zend_Acl_Resource('questionnaire'));
    $this->add(new Zend_Acl_Resource('module'));
    $this->add(new Zend_Acl_Resource('investmentgame'));
    $this->add(new Zend_Acl_Resource('result'));
    $this->add(new Zend_Acl_Resource('admin'));
    $this->add(new Zend_Acl_Resource('mlaexperiment'));
    $this->add(new Zend_Acl_Resource('mlaexperiment2'));
    $this->add(new Zend_Acl_Resource('admin_questionnaire'));
    $this->add(new Zend_Acl_Resource('treatment'));
    $this->add(new Zend_Acl_Resource('menu'));
    $this->add(new Zend_Acl_Resource('static'));
    $this->add(new Zend_Acl_Resource('loginregister'));    
    $this->add(new Zend_Acl_Resource('investmentgameassets'));    
    $this->add(new Zend_Acl_Resource('rs_plot'));
    $this->add(new Zend_Acl_Resource('rs_calculation'));
    $this->add(new Zend_Acl_Resource('admin_install'));
    


    // ROLES
    $this->addRole(new Zend_Acl_Role('guest'));
    $this->addRole(new Zend_Acl_Role('member'), 'guest');
    $this->addRole(new Zend_Acl_Role('moderator'), 'member');
    $this->addRole(new Zend_Acl_Role('admin'), 'moderator');

    // RULES
    $this->deny(null, null);
    $this->allow(null, 'error');
    $this->allow(null, 'menu');
    $this->allow(null, 'login');
    $this->allow(null, 'loginregister');    
    $this->allow(null, 'registration');
    $this->allow("member", 'treatment');
    $this->allow("member", 'index');
    $this->allow("member", 'mlaexperiment');
    $this->allow("member", 'mlaexperiment2');
    $this->allow('member', 'questionnaire');
    $this->allow('member', 'investmentgame');
    $this->allow('member', 'investmentgameassets');    
    $this->allow("member", 'static');
    $this->allow('member', 'module');
    $this->allow('member', 'rs_plot');
    $this->allow('member', 'rs_calculation');
    $this->deny('member', 'registration');  
    
    $this->allow('admin', null);
    
  }
}
?>