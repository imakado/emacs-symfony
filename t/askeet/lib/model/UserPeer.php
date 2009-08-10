<?php

  // include base peer class
  require_once 'lib/model/om/BaseUserPeer.php';
  
  // include object class
  include_once 'lib/model/User.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ask_user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class UserPeer extends BaseUserPeer
{
  public static function getUserFromNickname($nickname)
  {
    $c = new Criteria();
    $c->add(self::NICKNAME, $nickname);

    return self::doSelectOne($c);
  }

  public static function getAuthenticatedUser($nickname, $password)
  {
    $user = self::getUserFromNickname($nickname);

    // nickname exists?
    if ($user)
    {
      // password is OK?
      if (sha1($user->getSalt().$password) == $user->getSha1Password())
      {
        return $user;
      }
    }

    return null;
  }

  public static function getModeratorCandidatesCount()
  {
    $c = new Criteria();
    $c->add(self::WANT_TO_BE_MODERATOR, true);

    return self::doCount($c);
  }

  public static function getModeratorCandidates()
  {
    $c = new Criteria();
    $c->add(self::WANT_TO_BE_MODERATOR, true);
    $c->addAscendingOrderByColumn(self::CREATED_AT);

    return self::doSelect($c);
  }

  public static function getModerators()
  {
    $c = new Criteria();
    $c->add(self::IS_MODERATOR, true);
    $c->addAscendingOrderByColumn(self::CREATED_AT);

    return self::doSelect($c);
  }

  public static function getAdministrators()
  {
    $c = new Criteria();
    $c->add(self::IS_ADMINISTRATOR, true);
    $c->addAscendingOrderByColumn(self::CREATED_AT);

    return self::doSelect($c);
  }

  public static function getProblematicUsersCount()
  {
    $c = new Criteria();
    $c->add(self::DELETIONS, 0, Criteria::GREATER_THAN);

    return self::doCount($c);
  }

  public static function getProblematicUsers()
  {
    $c = new Criteria();
    $c->add(self::DELETIONS, 0, Criteria::GREATER_THAN);
    $c->addDescendingOrderByColumn(self::DELETIONS);

    return self::doSelect($c);
  }
}

?>