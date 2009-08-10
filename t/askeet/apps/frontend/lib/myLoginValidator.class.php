<?php

/**
 * login validator.
 *
 * @package    askeet
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id$
 */
 class myLoginValidator extends sfValidator
 {    
   public function initialize ($context, $parameters = null)
   {
     // initialize parent
     parent::initialize($context);

     // set defaults
     $this->getParameterHolder()->set('login_error', 'Invalid input');

     $this->getParameterHolder()->add($parameters);

     return true;
   }

   /**
    * Execute this validator.
    *
    * @param mixed A file or parameter value/array.
    * @param error An error message reference.
    *
    * @return bool true, if this validator executes successfully, otherwise
    *              false.
    */
   public function execute (&$value, &$error)
   {
     $password_param = $this->getParameterHolder()->get('password');
     $password = $this->getContext()->getRequest()->getParameter($password_param);

     $login = $value;

     // anonymous is not a real user
     if ($login == 'anonymous')
     {
       $error = $this->getParameterHolder()->get('login_error');
       return false;
     }

     if ($user = UserPeer::getAuthenticatedUser($login, $password))
     {
       $this->getContext()->getUser()->signIn($user);

       return true;
     }

     $error = $this->getParameterHolder()->get('login_error');
     return false;
   }
}

?>
