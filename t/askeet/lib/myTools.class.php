<?php

class myTools
{
  public static function stripText($text)
  {
    $text = strtolower($text);

    // strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);

    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', '-', $text);

    // trim dashes
    $text = preg_replace('/\-$/', '', $text);
    $text = preg_replace('/^\-/', '', $text);

    return $text;
  }

  public static function stemPhrase($phrase)
  {
    // split into words
    $words = str_word_count(strtolower($phrase), 1);

    // ignore stop words
    $words = myTools::removeStopWordsFromArray($words);

    // stem words
    $stemmed_words = array();
    foreach ($words as $word)
    {
      // ignore 1 and 2 letter words
      if (strlen($word) <= 2)
      {
        continue;
      }

      // stem word (stemming is specific for each language)
      $stemmed_words[] = PorterStemmer::stem($word, true);
    }

    return $stemmed_words;
  }

  public static function removeStopWordsFromArray($words)
  {
    $stop_words = array(
      'i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours', 
      'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 
      'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves', 
      'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are', 
      'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 
      'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 
      'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 
      'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down', 
      'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 
      'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 
      'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 
      'than', 'too', 'very',
    );

    return array_diff($words, $stop_words);
  }
}

?>