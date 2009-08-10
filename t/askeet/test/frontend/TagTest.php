<?php

require_once('Tag.class.php');

class TagTest extends UnitTestCase
{
  public function test_normalize()
  {
    $tests = array(
      'FOO'       => 'foo',
      '   foo'    => 'foo',
      'foo  '     => 'foo',
      ' foo '     => 'foo',
      'foo-bar'   => 'foobar',

      ' FOo-bar ' => 'foobar',
    );

    foreach ($tests as $tag => $normalized_tag)
    {
      $this->assertEqual($normalized_tag, Tag::normalize($tag));
    }
  }

  public function test_splitPhrase()
  {
    $tests = array(
      'foo' => array('foo'),
      'foo bar' => array('foo', 'bar'),
      '  foo    bar  ' => array('foo', 'bar'),
      '"foo bar" askeet' => array('foo bar', 'askeet'),
    );

    foreach ($tests as $tag => $tags)
    {
      $this->assertEqual($tags, Tag::splitPhrase($tag));
    }
  }
}

?>
