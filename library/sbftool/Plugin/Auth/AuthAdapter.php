<?php
class Sbftool_Plugin_Auth_AuthAdapter extends Zend_Auth_Adapter_DbTable
{
  public function __construct()
  {
    $registry = Zend_Registry::getInstance();
    parent::__construct($registry->dbAdapter);
    
    $config = Zend_Registry::get('config')->db;
    $prefix = $config->table_prefix;

    $this->setTableName($prefix . '_' . 'users');
    $this->setIdentityColumn('userhash');
    $this->setCredentialColumn('password');
    $this->setCredentialTreatment("SHA1(?)");
    
  }
}
?>