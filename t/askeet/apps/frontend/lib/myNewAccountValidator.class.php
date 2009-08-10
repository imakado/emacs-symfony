<?php

/**
 * new account validator.
 *
 * @package    askeet
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id$
 */
class myNewAccountValidator extends sfValidator
{
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
    $this->getContext()->getRequest()->setAttribute('newaccount', true);

    $login = $value;

    $c = new Criteria();
    $c->add(UserPeer::NICKNAME, $login);
    $user = UserPeer::doSelectOne($c);

    // nickname exists?
    if ($user)
    {
      $error = $this->getParameterHolder()->get('newaccount_error');
      return false;
    }

    return true;
  }

  public function initialize ($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->getParameterHolder()->set('newaccount_error', 'Invalid input');

    $this->getParameterHolder()->add($parameters);

    return true;
  }
}

?>