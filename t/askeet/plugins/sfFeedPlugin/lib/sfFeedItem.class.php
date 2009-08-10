<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFeedItem.class.php 1469 2006-06-16 15:32:04Z fabien $
 */
class sfFeedItem
{
  private
   $title,
   $link,
   $description,
   $authorEmail,
   $authorName,
   $authorLink,
   $pubdate,
   $comments,
   $uniqueId,
   $enclosure,
   $categories = array();

  public function setTitle ($title)
  {
    $this->title = $title;
  }

  public function getTitle ()
  {
    return $this->title;
  }

  public function setLink ($link)
  {
    $this->link = $link;
  }

  public function getLink ()
  {
    return $this->link;
  }

  public function setDescription ($description)
  {
    $this->description = $description;
  }

  public function getDescription ()
  {
    return $this->description;
  }

  public function setAuthorEmail ($authorEmail)
  {
    $this->authorEmail = $authorEmail;
  }

  public function getAuthorEmail ()
  {
    return $this->authorEmail;
  }

  public function setAuthorName ($authorName)
  {
    $this->authorName = $authorName;
  }

  public function getAuthorName ()
  {
    return $this->authorName;
  }

  public function setAuthorLink ($authorLink)
  {
    $this->authorLink = $authorLink;
  }

  public function getAuthorLink ()
  {
    return $this->authorLink;
  }

  public function setPubdate ($pubdate)
  {
    $this->pubdate = $pubdate;
  }

  public function getPubdate ()
  {
    return $this->pubdate;
  }

  public function setComments ($comments)
  {
    $this->comments = $comments;
  }

  public function getComments ()
  {
    return $this->comments;
  }

  public function setUniqueId ($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }

  public function getUniqueId ()
  {
    return $this->uniqueId;
  }

  public function setEnclosure ($enclosure)
  {
    $this->enclosure = $enclosure;
  }

  public function getEnclosure ()
  {
    return $this->enclosure;
  }

  public function setCategories ($categories)
  {
    $this->categories = $categories;
  }

  public function getCategories ()
  {
    return $this->categories;
  }
}

?>