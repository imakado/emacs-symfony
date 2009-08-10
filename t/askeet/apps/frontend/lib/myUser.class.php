<?php

class myUser extends sfBasicSecurityUser
{
  public function signIn($user)
  {
    $this->setAttribute('subscriber_id', $user->getId(), 'subscriber');
    $this->setAuthenticated(true);

    $this->addCredential('subscriber');

    if ($user->getIsModerator())
    {
      $this->addCredential('moderator');
    }

    if ($user->getIsAdministrator())
    {
      $this->addCredential('administrator');
    }

    $this->setAttribute('nickname', $user->getNickname(), 'subscriber');
  }

  public function signOut()
  {
    $this->getAttributeHolder()->removeNamespace('subscriber');

    $this->setAuthenticated(false);
    $this->clearCredentials();
  }

  public function getSubscriberId()
  {
    if ($this->isAuthenticated())
    {
      return $this->getAttribute('subscriber_id', '', 'subscriber');
    }
    else
    {
      return 0;
    }
  }

  public function getSubscriber()
  {
    if ($this->isAuthenticated())
    {
      return UserPeer::retrieveByPk($this->getSubscriberId());
    }
    else
    {
      return null;
    }
  }

  public function getNickname()
  {
    if ($this->isAuthenticated())
    {
      return $this->getAttribute('nickname', '', 'subscriber');
    }
    else
    {
      return '';
    }
  }
}

?>