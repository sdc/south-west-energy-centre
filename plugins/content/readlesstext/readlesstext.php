<?php
/**
 * @package readlesstext
 * @copyright 2008-2011 Parvus
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author Parvus
 *
 * readless is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * readless is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with readless. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @version $Id: readlesstext.php 165 2012-09-10 20:20:53Z parvus $
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.utilities.date' );

if ( !/*NOT*/ class_exists( 'ContentHelperRoute' ) )
{
  /* There have been some reports of people that somehow got errors by having
   * this class not being included by default (not only with this plugin; also
   * with other completely unrelated plugins.
   * Although this block should never be entered, it serves as a workaround
   * for those people having this issue.
   */
  require_once( JPATH_SITE . '/components/com_content/helpers/route.php' );
}

class ReadLessTextHelper
{
  /**
   * Checks whether it is allowed to run.
   * This function does not make any modification, except when in discover
   * mode: @c $article->text will then be set to the discover information.
   * @param JTableContent $article the item fetched from the database
   * @param params $params the parameters to use
   * @param dict $options OUT the key 'discover' with a boolean value will be
   *   filled in.
   * @param bool $activeByDefaultOnAllContentItems True if all content items
   *   must be shortened by 'read less', unless explicitly disallowed.
   * @param string $extraDiscoverInfo When Discover mode is active, this string
   *   will be added to the discover information.
   * @return Boolean value
   */
  public static function Filter( &$callCount, $article, $params, &$options,
      $activeByDefaultOnAllContentItems = false, $extraDiscoverInfo = '' )
  {
    $current = array();
    $current[ 'component' ] = JRequest::getWord( 'option' );
    $current[ 'view' ] = JRequest::getWord( 'view' );
    $current[ 'viewId' ] = JRequest::getInt( 'id' );
    $current[ 'articleId' ] = ReadLessTextHelper::GetArticleId( $article );
    $current[ 'articleSlug' ] = ReadLessTextHelper::GetArticleId( $article );
    $current[ 'articleCategoryId' ] = ReadLessTextHelper::GetCategoryId( $article );

    $allowed = true;

    $articleNumberSkipCount = max( 0, $params->get( 'articleNumberSkipCount' ) );
    $articleNumberShortenCount = max( 0, $params->get( 'articleNumberShortenCount' ) );
    $discoverTextAboutCallCount = '';
    if ( $callCount < $articleNumberSkipCount )
    {
      $discoverTextAboutCallCount .= "<br>This plugin may only become active on this page after skipping "
          . $articleNumberSkipCount . " article(s) or item(s) on this page (still "
          . ( $articleNumberSkipCount - $callCount ) . " to skip).";
      $allowed = false;
    }
    else if ( ( $articleNumberShortenCount == 0 )
        or ( $callCount < $articleNumberSkipCount + $articleNumberShortenCount ) )
    {
      /* ok */
    }
    else
    {
      $discoverTextAboutCallCount .= "<br>This plugin may only become active on this page for "
          . $articleNumberShortenCount . " articles or items on this page.";
      $allowed = false;
    }
    $callCount++;

    if ( $allowed )
    {
      if ( $params->get( 'when', '0' ) == '0' )
      {
        /* common usage */

        if ( $activeByDefaultOnAllContentItems )
        {
          $allowedFilters = 'com_content';
          $disallowedFilters = '';
        }
        else
        {
          $allowedFilters = 'com_content:blog, com_content:categories, com_content:category, com_content:featured, com_content:frontpage, com_content:section';
          $disallowedFilters = '';
        }
      }
      else
      {
        /* specific usage */
        $allowedFilters = $params->get( 'allowed', '' );
        $disallowedFilters = $params->get( 'disallowed', '' );
      }

      if ( $activeByDefaultOnAllContentItems and ( $current[ 'component' ] == 'com_content' ) )
      {
        $allowedFilters = 'com_content, ' . $allowedFilters;
      }

      $contexts = array();
      $contextDescriptions = array(); /* Only used to facilitate Discover mode. */
      $possiblyInterchangeable = array(); /* Only used to facilitate Discover mode. */
      /* Category/section/other descriptions have to be explicitly enabled.
       * Do not include the more general compact indications of the current
       * page/article in that case.
       */
      if ( $current[ 'articleId' ] != 0 )
      {
        $contexts[] = $current[ 'component' ];
        $contextDescriptions[] = 'all pages of this component';
        $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ];
        $contextDescriptions[] = 'all similar pages';
        if ( $current[ 'viewId' ] )
        {
          $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ];
          $contextDescriptions[] = 'all items on this page only';
          $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
        }
      }
      $contexts[] = $current[ 'component' ] . ':' . $current[ 'articleId' ];
      $contextDescriptions[] = 'this item only on all pages';
      $contexts[] = $current[ 'component' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
      $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on all pages';
      $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . ':' . $current[ 'articleId' ];
      $contextDescriptions[] = 'this item only on all similar pages';
      $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
      $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on all similar pages';
      $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
      if ( $current[ 'viewId' ] )
      {
        $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ] . ':' . $current[ 'articleId' ];
        $contextDescriptions[] = 'this item only on this page only';
        $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
        $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on this page only';
        $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
      }

      /* Loop over key (may be active if filter does not match) value (parameter name) pairs */
      $loop = array( false => $allowedFilters, true => $disallowedFilters);
      foreach ( $loop as $defaultAllowed => $filters )
      {
        if ( $filters )
        {
          /* A bit more manipulation is required here: $filters can be given in
           * different formats
           * @li {component}:{view}:id targets a specific article displayed in all the views of the given type.
           * @li {component}:{view}=nr:id targets a specific article displayed in the given view only.
           * @li {component}:id targets a specific article displayed in any view.
           * @li {component}:{view} targets all articles displayed in all the views of the given type.
           * @li {component}:{view}=nr targets all articles displayed in the given view .
           * @li {component} targets all articles of that component.
           * @note If {component}: is not given, com_content is assumed.
           * @note If {view} is not given, it is not checked for.
           * Plus contexts may be given on different lines.
           * The string manipulations below ensure that all filters start with a component name
           * followed by a view (with or without nr) and/or and id.
           */
          $filters = ',' . JString::strtolower( $filters );
          $filters = preg_replace( '/[\r\n]+/', ',', $filters );
          $search = array( ' ', ',', '+com_', '+' );
          $replace = array( '', ',+', 'com_', 'com_content:' );
          $filters = JString::str_ireplace( $search, $replace, $filters );
          $filterList = explode( ',', $filters );

          $filterAllows = $defaultAllowed;
          foreach ( $contexts as $c )
          {
            if ( in_array( $c, $filterList ) !== FALSE )
            {
              $filterAllows = !/*NOT*/$defaultAllowed;
              $lastMatchingContext = $c;
              break;
            }
          }
          $allowed &= $filterAllows;
        }
        else
        {
          /* There is no restriction set. Retain the default or already determined value for $allowed. */
        }
      }
    }

    $discover = $params->get( 'discover', false );
    if ( $discover )
    {
      $version = new JVersion();
      $discover = JFactory::getUser()->authorise( 'core.login.admin' );
    }
    if ( $options !== NULL )
    {
       $options[ 'discover' ] = $discover; /* Store this value so that others don't need to perform the same logic. */
    }
    if ( $discover )
    {
      $enableordisable = array( true => "disable", false => "enable" );

      $pluginName = "<em>read less text</em>";
      $article->text = "";
      $activeornot = array( true => "<strong>active</strong>", false => "<strong>not active</strong>" );
      $article->text .= "<p>" . $pluginName . " is " . $activeornot[$allowed] . " on this item ";
      if ( $allowed )
      {
        $article->text .= "(provided the contents' length is large enough - this is not checked yet at this point).";
      }
      $article->text .= $discoverTextAboutCallCount;

      $article->text .= "<ul>Information you can use to create your own contexts:";
      $article->text .= "<li>current component: <code>" . $current[ 'component' ] . "</code></li>";
      $article->text .= "<li>current view: <code>" . $current[ 'view' ] . "</code></li>";
      $article->text .= "<li>current view id: <code>" . $current[ 'viewId' ] . "</code></li>";
      $article->text .= "<li>current article id: <code>" . $current[ 'articleId' ] . "</code></li>";
      $article->text .= "<li>category id of article: <code>" . $current[ 'articleCategoryId' ] . "</code></li>";
      $article->text .= "</ul></p>";

      if ( isset( $lastMatchingContext ) )
      {
        /* Maybe active, maybe not, but at least one context matched. */
        $article->text .= "The last context you configured that matched the current item is <strong><code>" . $lastMatchingContext . "</code></strong>";
        if ( !/*NOT*/$allowed )
        {
          $article->text .= "<br>If you want to enable the plugin on this item on this page, you minimally need to remove or change this context.";
        }
      }
      else if ( $allowed )
      {
        /* Active, but no context ever matched. */
        $article->text .= "<br/>There are no contexts listed where " . $pluginName . " is allowed to be active, so it is <strong>active by default</strong>.";
      }
      else
      {
        /* Not active, but no context ever matched. */
        $article->text .= "<br/>No context matches the current item, so it is <strong>not active by default</strong>.";
      }
      $article->text .= "</p><ul>If you want to " . $enableordisable[$allowed] . " " . $pluginName . " on ";
      for ( $i = 0; $i < count( $contexts ); $i++)
      {
        $article->text .= "<li>" . $contextDescriptions[$i] . ", you can use the context <code>" . $contexts[$i] . "</code>";
      }
      $article->text .= "</ul>";

      $article->text .= "<p>";
      if ( $current[ 'component' ] == 'com_content' )
      {
        $article->text .= "<strong>Note</strong>: when configuring, you may omit <code>com_content:</code> from the context string.";
        $article->text .= "<br/>";
      }
      if ( $current[ 'articleId' ] == $current[ 'viewId' ] )
      {
        $article->text .= "<strong>Note</strong>: if the view name <code>"
            . $current[ 'view' ]
            . "</code> serves to display a single item/article, the contexts <code>"
            . implode( '</code>, <code>', $possiblyInterchangeable )
            . "</code> yield the same result and are interchangeable.<br/>";
      }
      $article->text .= "<strong>Note</strong>: discover information is only displayed to users with back-end permissions and can be disabled in the back-end.";
      if ( $extraDiscoverInfo )
      {
        $article->text .= "<br/>";
        $article->text .= "<strong>" . $extraDiscoverInfo . "</strong>";
      }
      $article->text .= "</p>";
      $article->introtext = $article->text;
      $article->fulltext = "";
    }

    return $allowed;
  }

  /**
   * Determines if @c $string ends with $lastPart.
   * @param string $string The string to examine
   * @param string $lastPart The substring to find at the end of @c $string
   * @return true if @c $string ends with @c $lastPart, false otherwise
   */
  private static function _EndsWith( $string, $lastPart )
  {
    if ( strlen( $string ) < strlen( $lastPart ) )
    {
      $endsWith = false;
    }
    else
    {
      if ( substr_compare( $string, $lastPart, -1 * strlen( $lastPart ) ) )
      {
        $endsWith = false;
      }
      else
      {
        $endsWith = true;
      }
    }
    return $endsWith;
  }

  /**
   * Determines the length of the given article text.
   * @param string $htmltext The html text to consider.
   * @param string $lengthUnit Unit of the length to determine. One of 'char',
   *   'word', 'sentence', 'paragraph'.
   * @param bool $end OUT If true, the last part of @c htmlText ends the
   *   ongoing unit.
   * @return the length expressed in the given unit.
   * @pre it is assumed subsequent whitespace has already been removed.
   */
  public static function DetermineLength( $htmltext, $lengthUnit, &$end )
  {
    switch ( $lengthUnit )
    {
      case 'sentence':
        /* Calculation is done using a list of sentences.
         * Exclude empty sentences, exclude sentences with only markup,
         * exclude consecutive punctuation characters.
         * The text is also trimmed to determine afterwards whether the last
         * sentence has ended.
         * Paragraph, row and list item demarcations also mark the end of a sentence.
         * Ensure that all sentence endings can be treated alike.
         */
        $search = array( '</p>', '</li>', '</dt>', '</dl>', '</tr>', '#', '.', '?', '!', '¿' );
        $replace = array( '.', '.', '.', '.', '.', '_', '#', '#', '#', '#' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );
        $text = rtrim( strip_tags( $htmltext ) );
        $length = 0;
        foreach ( explode( '#', $text ) as $sentence )
        {
          if ( preg_split( '/\s+/', $sentence, 1, PREG_SPLIT_NO_EMPTY ) )
          {
            $length++;
          }
        }
        $end = self::_EndsWith( $text, '#' );
        break;

      case 'paragraph':
        /* Calculation is done using a list of non-empty paragraphs.
         * Exclude empty paragraphs, exclude paragraps with only markup.
         * The text is trimmed first to determine whether the last paragraph
         * has ended afterwards.
         * Table and list demarcations also mark the end of a sentence.
         * Ensure that all paragraph endings can be treated alike.
         */
        $search = array( '</ul>', '</ol>', '</dl>' );
        $replace = array( '</p>', '</p>', '</p>' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );
        $htmltext = rtrim( $htmltext );
        $length = 0;
        foreach ( explode( '</p>', $htmltext ) as $paragraph )
        {
          $paragraph = strip_tags( $paragraph );
          if ( preg_split( '/\s+/', $paragraph, 1, PREG_SPLIT_NO_EMPTY ) )
          {
            $length++;
          }
        }
        $end = self::_EndsWith( $htmltext, '</p>' );
        break;

      case 'word':
        /* Paragraph, row and list item demarcations also mark the end of a sentence.
         * Ensure that all word endings can be treated alike.
         */
        $search = array( '</p>', '</li>', '</dt>', '</dl>', '</tr>', '.', '?', '!', '¿' );
        $replace = array( ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );
        $text = strip_tags( $htmltext );
        $length = count( preg_split( '/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY ) );
        $end = self::_EndsWith( $text, ' ' );
        break;

      case 'char':
      default:
        /* Calculation only needs the plaintext. */
        $text = strip_tags( $htmltext );
        $length = JString::strlen( $text );
        $end = true;
        break;
    }

    return $length;
  }

  /**
   * Returns the id of the article this plugin is being called upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article IN The item/article being prepared for display.
   * @return A number.
   */
  public static function GetArticleId( $article )
  {
    $id = 0;
    foreach ( array( 'id', 'did', 'cid' ) as $field )
    {
      if ( isset( $article->$field ) )
      {
        $id = (int)$article->$field;
        break;
      }
    }

    return $id;
  }

  /**
   * Returns the slug of the article this plugin is being called upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article IN The item/article being prepared for display.
   * @return A string.
   */
  public static function GetArticleSlug( $article )
  {
    $id = self::GetArticleId( $article );

    if ( isset( $article->slug ) and $article->slug )
    {
      $slug = $article->slug;
    }
    else if ( isset( $article->alias ) and $article->alias )
    {
      $slug = $id . ':' . $article->alias;
    }
    else
    {
      if ( isset( $article->title ) and $article->title )
      {
        $slug = $id . ':' . JApplication::stringURLSafe( $this->title );
      }
      else if ( isset( $article->name ) and $article->name )
      {
        $slug = $id . ':' . JApplication::stringURLSafe( $article->name );
      }
      else
      {
        $slug = $id;
      }
    }

    return $slug;
  }

  /**
   * Returns the id of the category of the article this plugin is being called
   * upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article The item/article being prepared for display.
   * @return A number.
   */
  public static function GetCategoryId( &$article )
  {
    $id = 0;
    foreach ( array( 'catid', 'catsid' ) as $field )
    {
      if ( isset( $article->$field ) )
      {
        $id = (int)$article->$field;
        break;
      }
    }
    return $id;
  }

  /**
   * Returns the slug of the category of the article this plugin is being called
   * upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article The item/article being prepared for display.
   * @return A string.
   */
  public static function GetCategorySlug( &$article )
  {
    $id = self::GetCategoryId( $article );

    if ( isset( $article->catslug ) and $article->catslug )
    {
      $slug = $article->catslug;
    }
    else if ( isset( $article->category_alias ) and $article->category_alias )
    {
      $slug = $id . ':' . $article->category_alias;
    }
    else
    {
      $slug = $id;
    }

    return $slug;
  }

  /**
   *
   * @param string $plainText
   * @param integer $length
   * @param string $lengthUnit Unit of the length. One of 'char',
   *   'word', 'sentence', 'paragraph'.
   * @param bool $retainWholeWords Only used when $lengthUnit equals 'char'
   */
  public static function Substr( $plainText, $length, $lengthUnit, $retainWholeWords = false )
  {
    $substr = $plainText;
    switch ( $lengthUnit )
    {
      case 'sentence':
        $substrLength = 0;
        $matches = preg_split( '/([.?!¿]+)/mis', $plainText, $length + 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE );
        $i = 0;
        while ( ( $i < count( $matches ) ) and ( $length > 0 ) /* If there are still sentences to be included. */ )
        {
          $offset = 0; /* Use the data from the sentence by default. */
          if ( $i + 1 < count( $matches ) )
          {
            $offset = 1; /* Use the data from the delimiter after the sentence. */
          }
          $substrLength = $matches[ $i + $offset ][1]; /* Start position */
          $substrLength += JString::strlen( $matches[ $i + $offset ][0] ); /* Length of matched string */

          $i += 2;
          $length--;
        }
        $substr = JString::substr( $plainText, 0, $substrLength);
        break;

      case 'paragraph':
        /* Default value is correct. Nothing to do. */
        break;

      case 'word':
        $words = preg_split( '/\s+/', $plainText, $length + 1, PREG_SPLIT_NO_EMPTY );
        $words = array_slice( $words, 0, $length );
        $substr = implode( ' ', $words );
        break;

      case 'char':
      default:
        /* Subsequent whitespace must be counted as one. */
        $substrLength = 0;
        foreach ( preg_split( '/\s+/', $plainText, $length + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE ) as $match )
        {
          if ( $length > 0 ) /* If there are still characters to be included. */
          {
            $substrLength = $match[1];
            $wordLength = JString::strlen( $match[0] );
            if ( $retainWholeWords or ( $length >= $wordLength ) )
            {
              $substrLength += $wordLength;
            }
            else
           {
              $substrLength += $length;
            }
            $length -= $wordLength;
            $length--; /* Whitespace is counted as one. */
          }
        }
        $substr = JString::substr( $plainText, 0, $substrLength);
        break;
    }

    return $substr;
  }

  /**
   * Checks if the given url is still a valid thumbnail url.
   * Determines the correct url to the corresponding thumbnail.
   * If the thumbnail does not exist, it is created
   * @param string $url The url to the thumbnail image
   * @param dict $minimum Associative array as given to @c GetThumbnail().
   *   used to recreate the expected thumbnail url.
   * @param dict $crop Associative array as given to @c GetThumbnail().
   *   used to recreate the expected thumbnail url.
   * @param int $thumbWidth A number. May be zero or negative. When positive, it
   *   indicates the maximum width of the resized thumbnail. Else, there is no
   *   restriction on image width, and will be chosen in function of the
   *   height.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail width, in pixels.
   * @param int $thumbHeight A number. May be zero or negative. When positive, it
   *   indicates the maximum height of the resized thumbnail. Else, there is
   *   no restriction on image height, and will be chosen in function of the
   *   width.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail height, in pixels.
   * @return @c true if the given thumbnail url is still correct. @c false
   *   when some checks failed or could not be performed.
   */
  public static function ValidateThumbnail( $imageUrl, $thumbnailUrl, $minimum, $crop, & $thumbWidth, & $thumbHeight )
  {
    $success = false;

    $ext = strrchr( $thumbnailUrl, '.'); /* e.g.: .png */
    $path = JPATH_CACHE . '/plg_readlesstext/';
    $string = $imageUrl . $minimum[ 'width' ] . $minimum[ 'height' ] . $minimum[ 'ratio' ]
      . $crop[ 'horizontal_position' ] . $crop[ 'vertical_position' ]
      . $thumbWidth . $thumbHeight;
    $expectedThumbnailPath = $path . md5( $string ) . $ext;

    /* Transform the local path into a url */
    $parts = explode( JPATH_BASE . '/', $expectedThumbnailPath, 2 );
    $expectedThumbnailUrl = JURI::base() . $parts[1];

    if ( $thumbnailUrl == $expectedThumbnailUrl )
    {
      if ( @file_exists( $expectedThumbnailPath ) )
      {
        /* The given thumbnail exists, is located in the cache, and
         * the minimum configuration settings haven't been changed since.
         * we can re-use it.
         */
        $sizeArray = @getimagesize( $expectedThumbnailPath );
        $thumbWidth = $sizeArray[ 0 ];
        $thumbHeight = $sizeArray[ 1 ];

        $success = true;
      }
    }

    return $success;
  }

  /**
   * Determines the correct url to the corresponding thumbnail.
   * If the thumbnail does not exist, it is created
   * @param string $url The path to the image
   * @param dict $minimum Associative array, with keys 'width', 'height', 'ratio',
   *   and values 0 or positive numbers, expressed in pixels.
   *   Looked at both to find a previously created thumbnail; and when the
   *   thumbnail does not exist yet and has to be created.
   * @param dict $crop Associative array, with keys 'horizontal_position',
   *   'vertical_position' and values 'left', 'right', 'center' or 'no'.
   * @param int $thumbWidth A number. May be zero or negative. When positive, it
   *   indicates the maximum width of the resized thumbnail. Else, there is no
   *   restriction on image width, and will be chosen in function of the
   *   height.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail width, in pixels.
   * @param int $thumbHeight A number. May be zero or negative. When positive, it
   *   indicates the maximum height of the resized thumbnail. Else, there is
   *   no restriction on image height, and will be chosen in function of the
   *   width.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail height, in pixels.
   * @param int $lifetime The lifetime of the thumbnail in seconds to set when it
   *   is created by calling this function. Not used to check if the existing
   *   thumbnail is still valid. Default: 4 weeks (2419200 seconds).
   * @return false if an error occurred or if the given $url is incorrect. The
   *   path to the thumbnail otherwise.
   */
  public static function GetThumbnail( $url, $minimum, $crop, & $thumbWidth, & $thumbHeight, $lifetime = 2419200 )
  {
    $path = JPATH_CACHE . '/plg_readlesstext/';
    if ( !/*NOT*/@file_exists( $path ) )
    {
      @mkdir( $path );
    }
    $string = $url . $minimum[ 'width' ] . $minimum[ 'height' ] . $minimum[ 'ratio' ]
      . $crop[ 'horizontal_position' ] . $crop[ 'vertical_position' ]
      . $thumbWidth . $thumbHeight;
    $thumbnailPath = $path . md5( $string );
    /* .ext appended below */

    /* Transform the local path into a url */
    $parts = explode( JPATH_BASE . '/', $thumbnailPath, 2 );
    $thumbnailUrl = JURI::base() . $parts[1];

    $type = 0;
    if ( function_exists( 'exif_imagetype' ) )
    {
      $type = @exif_imagetype( $url );
    }
    if ( !/*NOT*/$type )
    {
      /* Fallback: try to determine the correct image type by checking for an extension in the url. */
      $ext = '';
      $parts = explode( '.', $url );
      if ( count( $parts ) > 1 )
      {
        $parts = explode( '?', $parts[ count( $parts ) - 1 ], 2 );
        $ext = JString::strtolower( $parts[ 0 ] );
      }
      if ( array_key_exists( $ext, ReadLessTextHelper::$_extToType ) )
      {
        $type = ReadLessTextHelper::$_extToType[ $ext ];
      }
    }
    if ( $type and array_key_exists( $type, ReadLessTextHelper::$_image ) )
    {
      $thumbnailPath .= ReadLessTextHelper::$_image[ $type ][ 'ext' ];
      $thumbnailUrl .= ReadLessTextHelper::$_image[ $type ][ 'ext' ];
      if ( @file_exists( $thumbnailPath ) )
      {
        /* Thumbnail already exists.
         * The image resource $url has been examined during a previous
         * execution. According to the settings, it is fit to serve
         * as a thumbnail.
         * Done!
         */
        $sizeArray = @getimagesize( $thumbnailPath );
        $thumbWidth = $sizeArray[ 0 ];
        $thumbHeight = $sizeArray[ 1 ];
      }
      else if ( !/*NOT*/@is_dir( $path ) or !/*NOT*/@is_writable( $path ) )
      {
        /* Insufficient write permissions. Use fall-back. */
        $thumbnailUrl = $url;
      }
      else
      {
        if ( !/*NOT*/array_key_exists( 'host', parse_url( $url ) ) )
        {
          $url = JURI::base() . $url;
        }
        $image = @call_user_func( ReadLessTextHelper::$_image[ $type ][ 'load' ], $url );
          if ( !/*NOT*/array_key_exists( 'host', parse_url( $url ) ) and !/*NOT*/@file_exists( $thumbnailPath ) )
          {
            /* A local file is referenced, but somehow it is not found using normal file access.
             * This can indicate a relative path was given, and our current working directory
             * has been changed.
             */
            $url = JURI::base() . $url;
          }
          $image = @call_user_func( ReadLessTextHelper::$_image[ $type ][ 'load' ], $url );

        $width = max( 1, @imagesx( $image ) ); /* Ensure a division is possible. */
        $height = max( 1, @imagesy( $image ) ); /* Ensure a division is possible. */
        $ratio = min( $width / $height, $height / $width );

        if ( !/*NOT*/$image
                or ( $width < $minimum[ 'width' ] )
                or ( $height < $minimum[ 'height' ] )
                or ( $ratio < $minimum[ 'ratio' ] ) )
        {
          /* Thumbnail may not be created.
           * According to the settings, it is not fit to serve as a thumbnail.
           */
          $thumbnailUrl = false;
        }
        else
        {
          /* Find the resized dimensions, keeping the proportions.
           *
           * There are four different ways to resize:
           * A: resize full width to thumbnail width,
           *     resize height with same ratio,
           *         crop height to thumbnail height (top, bottom, evenly both)
           * B: resize full height to thumbnail height,
           *     resize width with same ratio,
           *         crop width to thumbnail width (left, right, evenly both)
           * C: resize full width to thumbnail width,
           *     resize height with same ratio,
           *          resized height <= thumbnail height
           * D: resize full height to thumbnail height,
           *     resize width with same ratio,
           *         resized width <= thumbnail width
           *
           *  Based on
           * - the actual image dimensions: ix, iy
           * - the desired thumbnail dimensions: tx, ty
           *  - the crop options:
           *     crop horizontal: yes (left/right/evenly) or no (do not crop horizontally)
           *     crop vertical: yes (left/right/evenly) or no (do not crop vertically)
           * we need to determine the resize factor.
           *
           * Determine the horizontal and vertical ratio's: rx, ry
           * - If rx < ry: If resized using ry, the horizontal width will be
           *     greater than the thumbnail width. So either the width must
           *     be cropped (if allowed), either the image must be resized
           *     using rx (and thus the resized height will be less than the
           *     desired thumbnail height ty).
           * - If rx == ry: highly unlikely. Crop options are not needed
           *     here. Just resize using the single resize factor that was
           *     calculated.
           * - If rx > ry: Similar to the first case.
           *     Replace width <> height, rx <> ry and ty <> tx
           * Thus:
           * rx, ry = tx/ix, ty/iy
           * rx < ry
           *   ? crop horizontal ? r = ry : r = rx
           *   : crop vertical ? r = rx : r = ry
           *
           * The image width to use is equal to r * tx
           * The image height to use is equal to r * ty
           *
           * Determine the start positions sx, sy: everything lower and
           * everything higher than that plus the image width/height will be
           * thrown away (cropped).
           * Default value is 0, 0, to be used when cutting on the
           * right/bottom, or when cropping is disabled.
           * If there is something to be thrown away, i.e.
           * if r * ix > tx
           *   cut left, retain right ? sx = ix - tx / r
           *   cut right, retain left ? sx = 0
           *   cut evenly ? sx = (ix - tx / r) / 2
           * Likewise for sy
           *
           * Done!
           */

          /* -------------------------------------------------------------------------------- */
          /* Determine ratio to use */
          if ( $thumbWidth > 0 )
          {
            $resizeFactorWidth = min( 1, $thumbWidth / $width );
          }
          else
          {
            $thumbWidth = $width;
            $resizeFactorWidth = 1;
          }
          if ( $thumbHeight > 0 )
          {
            $resizeFactorHeight = min( 1, $thumbHeight / $height );
          }
          else
          {
            $thumbHeight = $height;
            $resizeFactorHeight = 1;
          }
          $resizeFactor = 1; /* Default value */
          if ( $resizeFactorWidth < $resizeFactorHeight )
          {
            /* Width is (relatively) greater than the height. */
            if ( in_array( $crop[ 'horizontal_position' ], array( 'left', 'right', 'center' ) ) )
            {
              /* Horizontal cropping is allowed. We may resize less,
               * and crop the extraneous part.
               */
              $resizeFactor = $resizeFactorHeight;
            }
            else
            {
              /* Horizontal cropping is not allowed. The full width must be
               * resized: the resized height will be less than the intended
               * thumbnail height.
               */
              $resizeFactor = $resizeFactorWidth;
            }
          }
          else
          {
            /* Vertical cropping is allowed. We may resize less,
             * and crop the extraneous part.
             */
            if ( in_array( $crop[ 'vertical_position' ], array( 'top', 'bottom', 'center' ) ) )
            {
              $resizeFactor = $resizeFactorWidth;
            }
            else
            {
              /* Vertical cropping is not allowed. The full height must be
               * resized: the resized width will be less than the intended
               * thumbnail width.
               */
              $resizeFactor = $resizeFactorHeight;
            }
          }

          /* -------------------------------------------------------------------------------- */
          /* Determine start positions */

          $horizontalStart = 0; /* Default value */
          $usedWidth = min( $width, $thumbWidth / $resizeFactor );
          $thumbWidth = intval( ( $usedWidth * $resizeFactor ) + 0.01 );
          if ( $usedWidth + 1 < $width )
          {
            switch ( $crop[ 'horizontal_position' ] )
            {
              case 'center':
                $horizontalStart = max( 0, ( $width - $usedWidth ) / 2 );
                break;

              case 'right':
                $horizontalStart = max( 0, $width - $usedWidth );
                break;

              case 'left':
                /* $horizontalStart remains 0 */
                break;

              default:
                /* Do not crop the width after all. We should never get here! */
                break;
            }
          }
          $verticalStart = 0; /* Default value */
          $usedHeight = min( $height, $thumbHeight / $resizeFactor );
          $thumbHeight = intval( ( $usedHeight * $resizeFactor ) + 0.01 );
          if ( $usedHeight + 1 < $height )
          {
            switch ( $crop[ 'vertical_position' ] )
            {
              case 'center':
                $verticalStart = max( 0, ( $height - $usedHeight ) / 2 );
                break;

              case 'bottom':
                $verticalStart = max( 0, $height - $usedHeight );
                break;

              case 'top':
                /* $verticalStart remains 0 */
                break;

              default:
                /* Do not crop the height after all. We should never get here! */
                break;
            }
          }

          /* -------------------------------------------------------------------------------- */
          /* Create thumbnail */

          $thumbnail = call_user_func( ReadLessTextHelper::$_image[ $type ][ 'create' ], $thumbWidth, $thumbHeight );
          if ( $type == 1 /* IMAGETYPE_GIF */ )
          {
            /* Make the thumbnail initially transparent if the original was transparent too.
             * Otherwise, fill it initially up with all white.
             */
            $transparentColorIdentifier = @imagecolortransparent( $image );
            if ( $transparentColorIdentifier >= 0 )
            {
              $colors = @imagecolorsforindex( $image, $transparentColorIdentifier );
              $transcolorindex = @imagecolorallocate( $thumbnail, $colors[ 'red' ], $colors[ 'green' ], $colors[ 'blue' ] );
              @imagefill( $thumbnail, 0, 0, $transcolorindex );
              @imagecolortransparent( $thumbnail, $transcolorindex ); /* Needed? */
            }
            else
            {
              $whiteColorIdentifier = @imagecolorallocate( $thumbnail, 255, 255, 255 );
              @imagefill( $thumbnail, 0, 0, $whitecolorindex);
            }
          }

          if ( ReadLessTextHelper::$_image[ $type ][ 'create_alpha' ] )
          {
            call_user_func( ReadLessTextHelper::$_image[ $type ][ 'create_alpha' ], $thumbnail, false );
          }
          call_user_func( ReadLessTextHelper::$_image[ $type ][ 'copy' ], $thumbnail, $image,
                  0, 0, $horizontalStart, $verticalStart,
                  $thumbWidth, $thumbHeight, $usedWidth, $usedHeight );
          if ( ReadLessTextHelper::$_image[ $type ][ 'save_alpha' ] )
          {
            call_user_func( ReadLessTextHelper::$_image[ $type ][ 'save_alpha' ], $thumbnail, true );
          }
          call_user_func( ReadLessTextHelper::$_image[ $type ][ 'save' ], $thumbnail, $thumbnailPath );

          /* The expiration information is not used directly, but it is still
           * added to allow Joomla's core garbage collection functionality to work.
           */
          $expirePath = $thumbnailPath . '_expire';
          @file_put_contents( $expirePath, ( time() + $lifetime) );
        }
      }
    }
    else
    {
      /* To me, the remaining image types are esoteric. Some of them I never
       * even heard of.
       * OR
       * Determining the image type failed.
       */
      $thumbnailUrl = false;
    }

    return $thumbnailUrl;
  }

  private static $_extToType = array(
      'gif' => 1 /* IMAGETYPE_GIF */,
      'jpg' => 2 /* IMAGETYPE_JPEG */,
      'jpeg' => 2 /* IMAGETYPE_JPEG */,
      'png' => 3 /* IMAGETYPE_PNG */
  );

  private static $_image = array(
      1 /* IMAGETYPE_GIF */ => array(
          'ext' => '.gif',
          'load' => 'imagecreatefromgif',
          'create' => 'imagecreate',
          'create_alpha' => '',
          'copy' => 'imagecopyresampled',
          'save_alpha' => '',
          'save' => 'imagegif'
      ),
      2 /* IMAGETYPE_JPEG */ => array(
          'ext' => '.jpg',
          'load' => 'imagecreatefromjpeg',
          'create' => 'imagecreatetruecolor',
          'create_alpha' => '',
          'copy' => 'imagecopyresampled',
          'save_alpha' => '',
          'save' => 'imagejpeg'
      ),
      3 /* IMAGETYPE_PNG */ => array(
          'ext' => '.png',
          'load' => 'imagecreatefrompng',
          'create' => 'imagecreatetruecolor',
          'create_alpha' => 'imagealphablending',
          'copy' => 'imagecopyresampled',
          'save_alpha' => 'imagesavealpha',
          'save' => 'imagepng'
      ) );
}

class ReadLessTextCache
{
  /**
   * Constructor
   * @param string $rtable The table name where the item is stored.
   * @param uint $rid The item id in $rtable.
   * @param string $hash a fingerprint of the article. When the fingerprint  doesn't match with the
   *   value stored in the database, the other fields are reset.
   */
  function __construct( $rtable, $rid, $hash )
  {
    /* Must be set before calling _GetDataFromDb */
    $this->_data[ 'rtable' ] = $rtable;
    $this->_data[ 'rid' ] = $rid;

    $this->_GetDataFromDb( $rtable, $rid );
    if ( array_key_exists( 'hash', $this->_data )
        and $this->_data[ 'hash' ]
        and ( $this->_data[ 'hash' ] == $hash ) )
    {
      /* Ok. Use the cached data. */
      $this->_dirty = false;
    }
    else
    {
      foreach ( array_keys( $this->_data ) as $key )
      {
        if ( $key != 'id' )
        {
          $this->_data[ $key ] = 0;
        }
      }
      $this->_data[ 'rtable' ] = $rtable;
      $this->_data[ 'rid' ] = $rid;
      $this->_data[ 'hash' ] = $hash;
      $this->_dirty = false; /* There is no need to store reset values. */
    }
  }

  /**
   * Retrieves the stored value for the given field.
   * @param string $field A field name, as stored in the database.
   * @note Possible fields are: 'char', 'word', 'sentence', 'paragraph', 'begin', 'end', 'url'.
   * @return @li 0 when the requested field or its field value could not be found. A valid value otherwise.
   */
  public function Get( $field )
  {
    $value = 0;
    if ( array_key_exists( $field, $this->_data ) )
    {
      $value = $this->_data[ $field ];
    }
    return $value;
  }

  /**
   * Sets or updates (a) value(s) for the given field(s).
   * @param mixed $field Either a field name, as stored in the database.
   *   Or an array of field names.
   * @param mixed value Either a fields value. It is assumed the values given can be converted to the stored database
   *   type.
   *   Or an array of field values.
   * @return void
   * @note The values are not yet stored in the database. Use @c Store() to make the changes permanent.
   * @see Store
   * @return void
   */
  public function Set( $field, $value )
  {
    if ( is_string( $field ) )
    {
      $set = array( $field => $value );
    }
    else
    {
      /* Assume is_array() */
      $set = array_combine( $field, $value );
    }
    foreach ( $set as $k => $v )
    {
      if ( isset( $this->_data[ $k ] ) and ( $this->_data[ $k ] == $v ) )
      {
        /* Nothing changed. No need to mark the data as changed. */
      }
      else
      {
        $this->_data[ $k ] = $v;
        $this->_dirty = true;
      }
    }
  }

  /**
   * Makes all changes made permanent.
   */
  public function Store()
  {
    if ( $this->_dirty )
    {
      $this->_SetDataToDb();
      $this->_dirty = false;
    }
    else
    {
      /* Nothing changed. No need to write to the database. */
    }
  }

  /**
   * Fetches all known readlesstext data from the database of given article/item
   * @return void.
   * @post @c $this->_data will have been set to an associative array, with the
   *   field names as keys, and the field values as values, or to an empty array
   *   (when no stored data was found).
   */
  private function _GetDataFromDb()
  {
    $db = JFactory::getDBO();
    $query = 'SELECT *
        FROM ' . self::_table . '
        WHERE rtable = ' . $db->Quote( $this->_data[ 'rtable' ] ) . '
        AND rid = ' . $db->Quote( $this->_data[ 'rid' ] );
    $db->setQuery( $query );
    $this->_data = $db->loadAssoc();
    if ( $this->_data )
    {
      unset( $this->_data[ 'last_update' ] );
    }
    else
    {
      $this->_data = array();
      /* rtable, rid, and hash will be filled in by the caller */
    }
  }

  /**
   * Writes all locally stored readlesstext data to the database of given article/item.
   * @note Both @c rtable and @c rid, given during construction, will be stored too.
   * @note If a record already exists, it will be updated; if not, a new one will be added.
   * @return void.
   */
  private function _SetDataToDb()
  {
    $db = JFactory::getDBO();

    $inserts = array();
    foreach ( $this->_data as $key => $value )
    {
      $inserts[ $db->quoteName( $key ) ] = $db->Quote( $value );
    }

    $updates = array();
    foreach ( $this->_data as $key => $value )
    {
      $updates[] = $db->quoteName( $key ) . '=' . $db->Quote( $value );
    }

    $query = 'INSERT INTO ' . self::_table . ' (' . implode( ',', array_keys( $inserts ) ) . ')
        VALUES (' . implode( ',', $inserts ) . ')
        ON DUPLICATE KEY UPDATE ' . implode( ',', $updates );

    $db->setQuery( $query );
    $db->query();
  }

  private $_data = array(); /**< Local storage for all the readlesstext data. Keys correspond to the table fieldnames. */
  private $_dirty = false; /**< When @c False, no database write will be done. This eliminates needless writes. */

  const _table = '#__readlesstext'; /**< The table name where readlesstext stores extra data about an article/item */
}

class ReadLessTextExpand
{
  function __construct()
  {
    $this->_expandables[ '{author_id}' ] = 0;
    $this->_expandables[ '{author}' ] = '';
    $this->_expandables[ '{created}' ] = '';
    $this->_expandables[ '{modified}' ] = '';
    $this->_expandables[ '{publish_up}' ] = '';
    $this->_expandables[ '{hits}' ] = '';
    $this->_expandables[ '{title}' ] = '';
    $this->_expandables[ '{words}' ] = '';
  }

  /**
   * Prepares a future call to Expand(). Based on the given arguments, the
   * expandables are set. Existing exandables are overwritten.
   * @param JTableContent $article RO. The item/article being prepared for display. May be Null.
   * @param string $plaintext RO. The plain text version of the full,
   *   unshortened article. May be Null.
   * @param string $dateFormat Determines the format for the date fields to expand
   *   in the pre- and suffixes. May be Null.
   * @param $overrides Overrides information deduced from $article with given
   *   values. Keys are one of {author}, {author_id}, {words}, {created},
   *   {modified}, {publish_up}, {hits}, {category}, {category_id}, {id},
   *   {component}, {title}, {url}. May be Null.
   * @note {words} can only be expanded correctly if it is set via @c $overrides
   */
  public function SetExpandables( $article, $plaintext, $dateFormat, $overrides )
  {
    if ( $article )
    {
      /* {component} */
      $this->_expandables[ '{component}' ] = JRequest::getWord( 'option' );

      /* {id} */
      $this->_expandables[ '{id}' ] = ReadLessTextHelper::GetArticleId( $article );

      /* {category_id} */
      $this->_expandables[ '{category_id}' ] = ReadLessTextHelper::GetCategoryId( $article );

      /* {author_id} */
      if ( isset( $article->created_by ) )
      {
        $this->_expandables[ '{author_id}' ] = $article->created_by;
      }

      /* {author} */
      if ( isset( $article->created_by_alias ) and $article->created_by_alias )
      {
        $this->_expandables[ '{author}' ] = $article->created_by_alias;
      }
      else if ( isset( $article->author ) and $article->author )
      {
        $this->_expandables[ '{author}' ] = $article->author;
      }

      /* {created} */
      if ( isset( $article->created ) )
      {
        $this->_created = new JDate( $article->created );
      }

      /* {modified} */
      if ( isset( $article->modified ) and ( $article->modified != '0000-00-00 00:00:00' ) )
      {
        $this->_modified = new JDate( $article->modified );
      }
      else
     {
        $this->_modified = $this->_created;
      }

      /* {publish_up} */
      if ( isset( $article->publish_up ) )
      {
        $this->_publishUp = new JDate( $article->publish_up );
      }

      /* {hits} */
      if ( isset( $article->hits ) )
      {
        $this->_expandables[ '{hits}' ] = $article->hits;
      }

      /* {title} */
      if ( isset( $article->title ) )
      {
        $this->_expandables[ '{title}' ] = $article->title;
      }
    }

    if ( $dateFormat )
    {
      if ( $this->_created )
      {
        $this->_expandables[ '{created}' ] = $this->_created->toFormat( $dateFormat );
      }
      if ( $this->_modified )
      {
        $this->_expandables[ '{modified}' ] = $this->_modified->toFormat( $dateFormat );
      }
      if ( $this->_publishUp )
      {
        $this->_expandables[ '{publish_up}' ] = $this->_publishUp->toFormat( $dateFormat );
      }
    }

    /* {words} must be set via $overrides */

    if ( $overrides )
    {
      $this->_expandables = array_merge( $this->_expandables, $overrides );
    }
  }

  /**
   * Expands the string according to the expandables, set in a previous call to SetExpandables
   * @param string $string
   */
  public function Expand( $string )
  {
    return JString::str_ireplace( array_keys( $this->_expandables ), array_values( $this->_expandables ), $string );
  }

  private $_expandables = array();
}

class plgContentReadLessText extends JPlugin
{
  function __construct( &$subject, $config )
  {
    parent::__construct( $subject, $config );
    $this->loadLanguage();
  }

//  This function can not be used reliably.
//  It can be that another plugin altered the text as stored in the database before this plugin get execution time.
//  read less must then work on the altered text, not on the text as stored in the database.
//
//   /**
//    * Entry function. Will be called each time some article text has been
//    * saved. The article's lengths will be calculated and stored or updated
//    * in the database.
//    * @param string	$context ignored
//    * @param JTableContent $article The item/article being prepared for display.
//    * @param bool $isNew If the content has just been created
//    * @note Article is passed by reference, but after the save, so no changes
//    *   will be saved.
//    * @return true
//    */
//   public function onContentAfterSave( $context, &$article, $isNew )
//   {
//     return true;
//   }

  /**
   * Entry function. Will be called each time some article text is to be
   * prepared for display.
   * @param string	$context ignored
   * @param JTableContent $article The item/article being prepared for display.
   * @param $params ignored
   * @param integer $limitstart ignored
   * @return void
   */
  function onContentBeforeDisplay( $context, &$article, &$params, $limitstart = 0 )
  {
    $this->ReadLessText( $article, self::$_callCount );
  }
//   onContentPrepare is not used: the call is too limited.
//   $article only contains a text field, not the id and other needed fields.
//   function onContentPrepare( $context, &$article, &$params, $limitstart = 0 )
//   {
//     $this->ReadLessText( $article, self::$_callCount );
//   }

    /**
     *
     * @param JTableContent $article The item/article being prepared for display.
     * @param number $callCount The number of times this function has been
     *   called by the same plugin. This value is used to determine whether
     *   the given article may be shortened.
     *   Will have been incremented by one when this function returns.
     */
    public function ReadLessText( &$article, &$callCount )
    {
//      jimport( 'joomla.error.profiler' );
//      $this->profiler = new JProfiler();
//      $this->profiles = array();
//      $this->profiles[] = $this->profiler->mark( ' IN ' . $article->id );

      /* The two blocks below are a workaround for issues in the Joomla core.
       * - In a 'featured' view, the fulltext is not set or empty, even when it
       *   is present in the database
       * - In a single article view, the readmore property is not set.
       * Seen in 1.6.6, 1.7.2 & 2.5.4
       *
       * It also ensures that when this plugin is invoked on other components (e.g. com_media),
       * There are no php notices.
       */
      foreach ( array( 'fulltext', 'readmore' ) as $var )
      {
        if ( !/*NOT*/ isset( $article->$var ) )
        {
          $article->$var = '';
        }
      }
      if ( $article->readmore and ( !/*NOT*/ $article->fulltext ) )
      {
        $db = JFactory::getDBO();
        $query = "SELECT c.fulltext
            FROM #__content c
            WHERE c.id = " . $article->id;
        $db->setQuery( $query );
        $article->fulltext = $db->loadResult();
      }

      $this->_prefixIsAdded = false;
      $this->_suffixIsAdded = false;
      $this->_isShortened = false;
      $this->_prefix = '';
      $this->_suffix = '';

      $alwaysActiveForGuests = $this->params->get( 'alwaysActiveForGuests', '0' );
      if ( $alwaysActiveForGuests )
      {
        $extraDiscoverInfo = 'Guests can only see the shortened article, unless a context in the <code>Disallowed</code> field matches this item.';
        $activeByDefaultOnAllContentItems = ( JFactory::getUser()->guest == 1 );
      }
      else
      {
        $extraDiscoverInfo = '';
        $activeByDefaultOnAllContentItems = false;
      }

//      $this->profiles[] = $this->profiler->mark( ' before filter ' );
      $options = array();
      if ( ReadLessTextHelper::Filter( $callCount, $article, $this->params, $options,
          $activeByDefaultOnAllContentItems, $extraDiscoverInfo ) )
      {
        /* Entering this block means: read less text must be active. */
//        $this->profiles[] = $this->profiler->mark( ' after filter ' );

        $imageHtml = '';
        $prefix = '';
        $suffix = '';

        if ( $options[ 'discover' ] )
        {
          /* Discover mode is active: the article's text has been replaced with
           * discover information and may not be altered.
           * No need to update the private variables - they will not be used.
           */
        }
        else
       {
//          $this->profiles[] = $this->profiler->mark( ' before prepare ' );
          $length = $this->_PrepareArticleText( $article, $this->params );
//          $this->profiles[] = $this->profiler->mark( ' after prepare ' );

          $expand = new ReadLessTextExpand();
          $expand->SetExpandables( $article, $this->_plaintext, Null, Null );
          $this->_GetParams( $article, $length, $expand );
          if ( ( $this->_applyFormatting == 'when_active' )
              or ( ( $this->_applyFormatting == 'when_long_enough' ) and $length ) )
          {
//            $this->profiles[] = $this->profiler->mark( ' before cutoff ' );
            $article->text = $this->_CutOff( $article->text, $length );
//            $this->profiles[] = $this->profiler->mark( ' after cutoff ' );
          }

          if ( ( $this->_createThumbnail == 'when_active' )
              or ( ( $this->_createThumbnail == 'when_shortened' ) and $this->_isShortened ) )
          {
//            $this->profiles[] = $this->profiler->mark( ' before thumbnail ' );
            $imageHtml = $this->_GetThumbnailHtml( $article );
//            $this->profiles[] = $this->profiler->mark( ' after thumbnail ' );
          }

          if ( ( $this->_addPrefix == 'when_active' )
              or ( ( $this->_addPrefix == 'when_shortened' ) and $this->_isShortened ) )
          {
            if ( !/*NOT*/ $this->_prefixIsAdded )
            {
              $prefix = $this->_prefix;
              $this->_prefixIsAdded = true;
            }
          }
          if ( ( $this->_addSuffix == 'when_active' )
              or ( ( $this->_addSuffix == 'when_shortened' ) and $this->_isShortened ) )
          {
            if ( !/*NOT*/ $this->_suffixIsAdded )
            {
              $suffix = $this->_suffix;
              $this->_suffixIsAdded = true;
            }
          }

          if ( $this->_cache )
          {
            $this->_cache->Store();
          }
        }

        $article->text = $imageHtml . $prefix . $article->text . $suffix;
        $article->introtext = $article->text;
        $article->fulltext = '';

//         $this->profiles[] = $this->profiler->mark( ' OUT ' . $article->id );
//         $article->text .= '<div>';
//         foreach ( $this->profiles as $profile )
//         {
//           $article->text .= "<br/>" . $profile;
//         }
//         $article->text .= '<br/></div>';
//         $article->introtext = $article->text;
//         $article->fulltext = '';
      }
    }

    /**
     * Determines the html version of the intro and the full field,
     * irrespective the component.
     * @param JTableContent $article The item/article being prepared for display.
     * @return An array, with the first element containing the intro text,
     *   if any, and the second element the remainder of the text.
     */
    private static function _DetermineArticleText( $article )
    {
      /* Be extra careful, so that other components do not give PHP notices
       * and warnings.
       * - Some components do not have the introtext and/or the fulltext field
       *   but only the text field,
       *   e.g. ??
       * - Others have none of the three, but use a special name (why o why),
       *   e.g. in com_eventlist:
       *     datdescription for an event.
       *     catdescription for a category.
       *     locdescription for a venue.
       *     description for a group.
       * - whereas others have all three of them, and full info is to be
       *   fetched from introtext and fulltext.
       */
      $introtext = '';
      $fulltext = '';
      if ( isset( $article->introtext ) )
      {
        $introtext = $article->introtext;
      }
      if ( isset( $article->fulltext ) )
      {
        $fulltext = $article->fulltext;
      }
      if ( !/*NOT*/$introtext and !/*NOT*/$fulltext)
      {
        if ( isset( $article->text ) and $article->text )
        {
          $fulltext = $article->text;
        }
      }
      if ( !/*NOT*/$introtext and !/*NOT*/$fulltext)
      {
        /* No text has been found yet. Check if it is stored in a variable named
         * xxxdescription, like eventlist does.
        */
        $values = array();
        foreach ( get_object_vars( $article ) as $key => $value )
        {
          if ( strstr( $key, 'description') !== false )
          {
            $values[] = $value;
          }
        }
        /* Only accept the text from a xxxdescription when it is the only one
         * found: e.g. eventlist uses one object to store the text of both
        * the event and the venue, and it is impossible to choose the correct
        * one.
        */
        if ( count( $values ) == 1 )
        {
          $fulltext = $values[0];
        }
      }

      return array( $introtext, $fulltext );
    }

    private function _DetermineLength( $lengthUnit )
    {
      $length = $this->_cache->Get( $lengthUnit );
      if ( !/*NOT*/$length )
      {
        $end = true; /* Just to define it; it is used as output parameter in the next call, but ignored by us. */
        $length = ReadLessTextHelper::DetermineLength( $this->_htmltext, $lengthUnit, $end );
        if ( $length )
        {
          $this->_cache->Set( $lengthUnit, $length );
        }
      }
      return $length;
    }

    /**
     * Determines the correct text to operate on. Sets the private variables
     * plaintext and htmltext.
     * @param JTableContent $article IN OUT. The item/article being prepared
     *   for display. The properties introtext, text, fulltext and readmore
     *   will have been set or adapted.
     * @param params $params the parameters to use
     * @return mixed boolean false to indicate article may not be shortened,
     *   or a number indicating the nr of units to retain.
     *   It is still possible this number is greater than the entire article
     *   text length.
     * @post @c $article->text contains the text to be shortened or to display.
     * @note When false is returned, @c $article->text will not have been
     *   changed (nor set, when it was not defined).
     */
    private function _PrepareArticleText( &$article, $params )
    {
      $cutOffLength = false; /* Default returnvalue */
      $this->_lengthUnit = $params->get( 'lengthUnit', 'char' );

      $a = self::_DetermineArticleText( $article );
      $introtext = $a[0];
      $fulltext = $a[1];
      $plainintrotext = strip_tags( $introtext );
      $plainfulltext = strip_tags( $fulltext );
      $this->_plaintext = $plainintrotext . ' ' . $plainfulltext;
      $this->_htmltext = $introtext . ' ' . $fulltext;

      /* Subsequent whitespace must be counted as one when $lengthUnit equals
       * 'char'. But other units need this as well: e.g. a non-breaking space
       * or a tab at the end of the $htmlText indicates the last word is
       * finished (trim does not remove non-breaking spaces).
       */
      $this->_htmltext = preg_replace( '/\xc2\xa0/mis', ' ', $this->_htmltext );
      $this->_htmltext = preg_replace( '/\s+/mis', ' ', $this->_htmltext );

      $respectExistingReadmoreLink = $params->get( 'respectExistingReadmoreLink', true );
      if ( $fulltext and isset( $article->readmore ) and $article->readmore )
      {
        /* A 'read more' link has been explicitly inserted, and fulltext is not empty.
         * - the full text is made by combining introtext, (closing the paragraph), a
         *   hr tag (id 'system-readmore') (opening the paragraph) and fulltext.
         */
        if ( $respectExistingReadmoreLink )
        {
          /* Ensure that both the logic to shorten an article is carried out,
           * and that the article is shortened to the manually inserted
          * 'read more' link.
          */
          $article->text = $introtext;
          $cutOffLength = PHP_INT_MAX;
          $this->_isShortened = true;
        }
      }

      $this->_cache = new ReadLessTextCache( JRequest::getWord( 'option' ), $article->id, md5( $this->_htmltext ) );

      if ( !/*NOT*/$cutOffLength )
      {
        $minimumLength = $params->get( 'minimumTextLength', 1 );

        $length = $this->_DetermineLength( $this->_lengthUnit );
        if ( $this->_lengthUnit == 'word' )
        {
          $this->_wordCount = $length;
        }
        else
       {
          $this->_wordCount = $this->_DetermineLength( 'word' );
        }

        if ( $minimumLength < $length )
        {
          $cutOffLength = $params->get( 'cutOffTextLength', 1 );
          if ( $cutOffLength > $length )
          {
            $cutOffLength = false;
          }
          else
         {
            $article->text = $this->_htmltext;
          }
        }
      }
      $article->readmore = 0;

      if ( !/*NOT*/$cutOffLength )
      {
        /* The article may not be shortened. Ensure the full article is present,
         * even when a manual to-be-ignored read more is present.
         */
        $article->text = $this->_htmltext;
      }
      $article->introtext = $article->text;

      return $cutOffLength;
    }

    /**
     * Determines various options, which are stored in the private variables.
     * @note Not all private variables will be set. Those are covered in the
     *   @c _PrepareArticleText function
     * @param JTableContent $article RO. The item/article being prepared
     *   for display.
     * @params mixed $length Boolean false or a positive number. Indicates
     *   whether it is already determined if the article is to be shortened and
     *   how long the shortened article must become.
     * @param class $expand Instance of ReadLessTextExpand.
     * @pre _PrepareArticleText() must have been called beforehand: e.g.
     *   @c _plaintext is used (while expanding).
     */
    private function _GetParams( &$article, $length, $expand )
    {
      $this->_applyFormatting = $this->params->get( 'applyFormatting', 'when_active' );
      $this->_addPrefix = $this->params->get( 'addPrefix', 'when_active' );
      $this->_addSuffix = $this->params->get( 'addSuffix', 'when_active' );
      if ( $this->_addSuffix == 'when_active_use_article_manager_option' )
      {
        if ( JComponentHelper::getParams( 'com_content' )->get( 'show_readmore' ) )
        {
          $this->_addSuffix = 'when_active';
        }
        else
       {
          $this->_addSuffix = 'no';
        }
      }
      $this->_createThumbnail = $this->params->get( 'createThumbnail', 'when_active' );

      /* An array of tags or tokens which are used later on. */
      if ( ( $this->_applyFormatting == 'when_active' )
          or ( ( $this->_applyFormatting == 'when_long_enough' ) and $length ) )
      {
        $list = array(
            'extraSelfClosingTags',
            'tagsToRemove',
            'tagsToRemoveWithContents',
            'squareTokensToRemoveWithContents',
            'curlyTokensToRemoveWithContents' );
      }
      else
     {
        $list = array(
            'extraSelfClosingTags');
      }
      foreach ( $list as $parameter )
      {
        $string = JString::strtolower( $this->params->get( $parameter, '' ) );
        $string = JString::str_ireplace( ' ', '', $string );
        if ( $string )
        {
          $parameter = '_' . $parameter;
          $this->$parameter = explode( ',', $string );
        }
      }

      $this->_articleUrl = JRoute::_( ContentHelperRoute::getArticleRoute(
          ReadLessTextHelper::GetArticleSlug( $article ), ReadLessTextHelper::GetCategorySlug( $article ) ) );
      $expand->SetExpandables( Null, Null, Null, array( '{url}' => $this->_articleUrl, '{words}' => $this->_wordCount ) );

      $translateAdditions = $this->params->get( 'translateAdditions', false );
      $prefixLinksToFullArticle = false;
      if ( $this->_addPrefix != 'no' )
      {
        if ( JFactory::getUser()->guest == 0 )
        {
          $this->_prefix = $this->params->get( 'userPrefix', '' );
          $prefixLinksToFullArticle = $this->params->get( 'userPrefixLinksToFullArticle', true );
        }
        else
        {
          $this->_prefix = $this->params->get( 'guestPrefix', '' );
          $prefixLinksToFullArticle = $this->params->get( 'guestPrefixLinksToFullArticle', true);
        }
      }
      if ( $translateAdditions )
      {
        $this->_prefix = JText::_( $this->_prefix );
      }
      $expand->SetExpandables( Null, Null, $this->params->get( 'prefixDateFormat', '%m/%d' ), null );
      $this->_prefix = $expand->expand( $this->_prefix );
      if ( $prefixLinksToFullArticle and $this->_prefix )
      {
        $this->_prefix = '<a href="' . $this->_articleUrl . '">' . $this->_prefix . '</a>';
      }

      $suffixLinksToFullArticle = false;
      if ( $this->_addSuffix != 'no' )
      {
        if ( JFactory::getUser()->guest == 0 )
        {
          $this->_suffix = $this->params->get( 'userSuffix', '' );
          $suffixLinksToFullArticle = $this->params->get( 'userSuffixLinksToFullArticle', true );
        }
        else
        {
          $this->_suffix = $this->params->get( 'guestSuffix', '' );
          $suffixLinksToFullArticle = $this->params->get( 'guestSuffixLinksToFullArticle', true);
        }
      }
      if ( $translateAdditions )
      {
        $this->_suffix = JText::_( $this->_suffix );
      }
      $expand->SetExpandables( Null, Null, $this->params->get( 'suffixDateFormat', '%m/%d' ), null );
      $this->_suffix = $expand->expand( $this->_suffix );
      if ( $suffixLinksToFullArticle and $this->_suffix )
      {
        $this->_suffix = '<a href="' . $this->_articleUrl . '">' . $this->_suffix . '</a>';
      }

      $this->_defaultThumbnail = $this->params->get( 'defaultThumbnailTemplate', '' );
      $this->_defaultThumbnail = $expand->expand( $this->_defaultThumbnail );

      $this->_retainWholeWords = $this->params->get( 'retainWholeWords', false );

      $this->_crop[ 'horizontal_position' ] = $this->params->get( 'cropHorizontalPosition', 'no' );
      $this->_crop[ 'vertical_position' ] = $this->params->get( 'cropVerticalPosition', 'no' );

      $this->_cacheTime = $this->params->get( 'thumbCacheTime', 2419200 /* 4 weeks */ );
      $this->_thumbWidth = $this->params->get( 'thumbWidth', 0 );
      $this->_thumbHeight = $this->params->get( 'thumbHeight', 0 );
      $this->_minimum[ 'width' ] = $this->params->get( 'minimumImageWidth', 0 );
      $this->_minimum[ 'height' ] = $this->params->get( 'minimumImageHeight', 0 );
      $this->_minimum[ 'ratio' ] = max( 0.05, min( 0.95, $this->params->get( 'minimumImageRatio', 0 ) ) );
    }

    /**
     * Searches for an image that passes all constraints set in the
     * configuration settings; removes that image from the article's text - if
     * present - and returns HTML code containing a thumbnail to that image,
     * readily suitable for display.
     * @param JTableContent $article IN OUT. The item/article being prepared
     *   for display.
     * @param string $articleText IN, OUT. $article->text may have been
     *   adapted when this function returns.
     * @return String. The HTML code for displaying the styled, resized and
     *   linked first image according to the configuration settings, the empty
     *   string otherwise.
     */
    private function _GetThumbnailHtml( $article )
    {
      /* - First try to get all data from the cached information. It this
       *   succeeds, we can avoid using regular exporessions.
       * - Else, pop an image from the shortened text.
       * - If none appropriate could be found, try to find one in the full
       *   unshortened article.
       * ALERT: There is a risk in this approach: if the
       *   suffix contains an image, has already been added and
       *   passes the image constraints, it will be elected.
       *   / This can be avoided by _Cutoff return the shortened text in two
       *     parts. @todo?
       *   / Or by popping twice - once on the shortened and once on the
       *     unshortened text - and comparing the results. @todo?
       *   / Or by popping on the unshortened text and removing the image in
       *     the shortened text. @todo?
       * - If then still no thumbnail is found, i.e.
       *   / images may not be moved
       *   / no images found
       *   / no acceptable image found,
       *   and a thumbnail is still desired, try to find a default thumbnail.
       */
      $thumbnailUrl = $this->_GetCachedThumbnail( $article->text );
      if ( !/*NOT*/ $thumbnailUrl )
      {
        $thumbnailUrl = $this->_PopImage( $article->text );
        if ( !/*NOT*/ $thumbnailUrl )
        {
          $thumbnailUrl = $this->_PopImage( $this->_htmltext );
          if ( !/*NOT*/ $thumbnailUrl )
          {
            $thumbnailUrl = $this->_GetDefaultThumbnail();
          }
        }
      }

      if ( $thumbnailUrl )
      {
        $attributes = $this->_GetImageAttributes( $article->title, $this->_thumbWidth, $this->_thumbHeight );
        $thumbnailHtml = '<a href="' . $this->_articleUrl . '"><img src="' . $thumbnailUrl. '"' . $attributes . '/></a>';
      }
      else
      {
        $thumbnailHtml = false;
      }

      return $thumbnailHtml;
    }

    /**
     * Fetches all cached image information. If the cached information is valid, it is used to construct the HTML code
     * containing a thumbnail, readily suitable for display. The image used to create the thumbnail, if present in the
     * article, will be stripped from the article's text.
     * @param string $articleText OUT. the string to adapt.
     * @return String. The HTML code for displaying the styled, resized and
     *   linked image according to the configuration settings, the empty
     *   string otherwise.
     */
    private function _GetCachedThumbnail( &$articleText )
    {
      $isValid = false;
      if ( $this->_cache )
      {
        $imageTagStartPos = $this->_cache->Get( 'image_tag_start_pos' );
        $imageTagLength = $this->_cache->Get( 'image_tag_length' );
        $imageUrl = $this->_cache->Get( 'image_url' );
        $thumbnailUrl = $this->_cache->Get( 'thumbnail_url' );

        if ( $thumbnailUrl and ( $imageTagLength > 0 ) )
        {
          $isValid = ReadLessTextHelper::ValidateThumbnail( $imageUrl, $thumbnailUrl, $this->_minimum, $this->_crop,
              $this->_thumbWidth, $this->_thumbHeight );
        }
      }

      if ( $isValid )
      {
        /* Remove the code for the image on the old location.
         * Don't do this blindly: it can be (it is highly likely) that all
         * images already have been removed from the given text.
         */
        if ( ( JString::substr( $articleText, $imageTagStartPos, 1 ) == '<' )
            and ( JString::substr( $articleText, $imageTagStartPos + $imageTagLength, 1 ) == '>' ) )
        {
          $imageUrlStartPos = strpos( $articleText, $imageUrl );
          if ( ( $imageTagStartPos < $imageUrlStartPos )
              and ( $imageUrlStartPos < $imageTagStartPos + $imageTagLength ) )
          {
            /* Ok, the portion we want to strip down does contain the url, and
             * does start and end with an opening resp. closing tag.
             * That's all we do to ascertain we will remove the correct portion.
             */
            $articleText = JString::substr_replace( $articleText, '', $imageTagStartPos, $imageTagLength );
          }
        }
      }
      else
      {
        $thumbnailUrl = false;
      }

      return $thumbnailUrl;
    }

    /**
     * Searches for an image according to the default thumbnail template, and
     * returns HTML code containing a thumbnail to that image, readily
     * suitable for display.
     * @return String. The HTML code for displaying the styled, resized and
     *   linked image according to the configuration settings, the empty
     *   string otherwise.
     */
    private function _GetDefaultThumbnail()
    {
      /* Be more lenient with respect to the default thumbnail: ensure it does
       * not get rejected due to size and dimension constraints.
       */
      $minimum[ 'width' ] = 0;
      $minimum[ 'height' ] = 0;
      $minimum[ 'ratio' ] = 0;
      $thumbnailUrl = ReadLessTextHelper::GetThumbnail( $this->_defaultThumbnail, $minimum, $this->_crop,
          $this->_thumbWidth, $this->_thumbHeight, $this->_cacheTime );

      return $thumbnailUrl;
    }

    /**
     * Searches for an image in the article's text which is big enough according
     * to the configuration settings; removes that image from the article's
     * text, and returns HTML code containing a thumbnail to that image, readily
     * suitable for display.
     * @param string $articleText IN, OUT. the string to search in and to adapt.
     * @return String. The HTML code for displaying the styled, resized and
     *   linked first image according to the configuration settings, the empty
     *   string otherwise.
     */
    private function _PopImage( &$articleText )
    {
      $thumbnailUrl = false;
      $matchCount = preg_match( self::_imgPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, 0 );
      while ( $matchCount )
      {
        $imageCode = $matches[0][0];
        $imageUrl = $matches[1][0];

        /* No acceptable image has been found yet, try this one. */
        $thumbnailUrl = ReadLessTextHelper::GetThumbnail( $imageUrl, $this->_minimum, $this->_crop, $this->_thumbWidth,
            $this->_thumbHeight, $this->_cacheTime );
        if ( $thumbnailUrl )
        {
          $imageTagStartPos = JString::strpos( $articleText, $imageCode );
          $imageTagLength = JString::strlen( $imageCode );

          /* Remove the code for the image on the old location. */
          $articleText = JString::substr_replace( $articleText, '', $imageTagStartPos, $imageTagLength );

          $this->_cache->Set( 'image_tag_start_pos', $imageTagStartPos );
          $this->_cache->Set( 'image_tag_length', $imageTagLength );
          $this->_cache->Set( 'image_url', $imageUrl );
          $this->_cache->Set( 'thumbnail_url', $thumbnailUrl );

          $matchCount = false; /* end the loop */
        }
        else
        {
          /* Prepare the next iteration
           * - The value of $offset determines where the next search starts.
           */
          $offset = $matches[ count( $matches ) - 1 ][1] + 1;
          $matchCount = preg_match( self::_imgPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, $offset );
        }
      }

      return $thumbnailUrl;
    }

    /**
     * Composes the attributes for the moved image, inclusive the inline CSS
     * attribute @c style.
     * This function does not make any modification.
     * @param alt: A string. The alternative text to be used for the image.
     * @param $width: A Number. The size of the image in pixels. If 0 or negative, this value is not set.
     * @param $height: A Number. The size of the image in pixels. If 0 or negative, this value is not set.
     * @return string: Either the empty string when no attributes are needed;
     *   either a string ready for insertion as an attribute in a HTML tag.
     * @note Double quotes are used to quote the value.
     */
    private function _GetImageAttributes( $alt, $width = 0, $height = 0 )
    {
      $attributes = '';
      $styleValue = '';

      $attributes .= ' alt="' . $alt . '"';
      if ( $width > 0 )
      {
        $attributes .= ' width="' . $width . '"';
      }
      if ( $height > 0 )
      {
        $attributes .= ' height="' . $height . '"';
      }

      $class = $this->params->get( 'thumbClass', '' );
      if ( $class )
      {
        $attributes .= ' class="' . $class . '"';
      }

      $imagePosition = $this->params->get( 'thumbPosition', 'left' );
      if ( $imagePosition )
      {
        $styleValue .= ' float:' . $imagePosition . ';';
      }
      $padding = $this->params->get( 'thumbPadding', -1 );
      if ( $padding >= 0 )
      {
        $styleValue .= ' padding:' . $padding . 'px;';
      }
      $margin = $this->params->get( 'thumbMargin', '' );
      if ( $margin or ( $margin[0] !== '-' /* a negative number starts with - */ ) )
      {
        $margin = JString::strtolower( $margin );
        $search = array( ' ', ';' );
        $replace = array( ',', ',' );
        $margin = JString::str_ireplace( $search, $replace, $margin );
        $marginList = explode( ',', $margin );
        $n = 0;
        while ( $n < count( $marginList ) )
        {
          if ( $marginList[ $n ] or ( $marginList[ $n ] === '0' ) )
          {
            if ( JString::strpos( '0123456789', $marginList[ $n ][ JString::strlen( $marginList[ $n ] ) - 1 ] ) === FALSE )
            {
              /* Ok, a unit is already appended */
            }
            else
            {
              $marginList[ $n ] .= "px";
            }
            $marginList[ $n ] = ' ' . $marginList[ $n ];
            $n++;
          }
          else
          {
            unset( $marginList[ $n ] );
            $marginList = array_values( $marginList ); /* re-index */
          }
        }
        $margin = implode( '', $marginList );
        $styleValue .= ' margin:' . $margin . ';';
      }
      $borderWidth = $this->params->get( 'thumbBorderWidth', -1 );
      if ( $borderWidth >= 0 )
      {
        $borderColor = $this->params->get( 'thumbBorderColor', '#cccccc' );
        $borderStyle = $this->params->get( 'thumbBorderStyle', '' );
        $styleValue .= ' border: ' . $borderWidth . 'px ' . $borderStyle . ' ' . $borderColor . ';';
      }
      if ( $styleValue )
      {
        $attributes .= ' style="' . $styleValue . '"';
      }

      return $attributes;
    }

    /**
     * Searches the given text for [token]...[/token] constructs that must be
     * removed.
     * @param string $text The text too operate on.
     * @note Possibly writes isShortened.
     * @return The stripped text.
     */
    private function _StripTokens( $text )
    {
      foreach ( array(
          '_squareTokensToRemoveWithContents' => array( '\[', '\]' ),
          '_curlyTokensToRemoveWithContents' => array( '{', '}' ) ) as $tokenList => $chars )
      {
        foreach ( $this->$tokenList as $token )
        {
          $search = array( self::_tokenOpeningReplacer, self::_tokenClosingReplacer, self::_tokenReplacer );
          $replace = array( $chars[0], $chars[1], $token );
          $tokenPattern = JString::str_ireplace( $search, $replace, self::_tokenPattern );
          $count = 0;
          $text = preg_replace( $tokenPattern, '', $text, -1, $count );
          if ( $count )
          {
            /* We have stripped away a - likely fancy - part of the article. The user must
             * get the possibility to access the full article - and thus that fancy part.
             */
            $this->_isShortened = true;
          }
        }
      }
      return $text;
    }

    /**
     * Cuts off the article text, retaining the full formatting. It uses regular
     * expressions to find tags, and a simple push/pop system to retain the
     * opened but still-unclosed tags. When the text has been cut off after a
     * admin-configurable unit count, all the opened and still-unclosed tags are
     * then closed.
     * @note The prefix will not be added in this function.
     * @note The suffix may be added too, before closing the still-unclosed tags.
     *   @c _suffixIsAdded will be set to  @c true in that case.
     * @param string $text RO. The text to shorten.
     * @param integer $cutOffLength The size in number of characters of the plain
     *   text to retain, or FALSE to indicate all text must be retained (safe the
     *   tags that must be removed according to the configuraion settings).
     * @return The shortened text.
     */
    private function _CutOff( $text, $cutOffLength )
    {
      $openTags = array();
      $result = "";

      /* The total length of the plain text strings may not be bigger than the
       * maximum article length setting.
       */
      if ( $cutOffLength )
      {
        $nrOfUnitsYetToRetain = $cutOffLength;
      }
      else
      {
        $nrOfUnitsYetToRetain = PHP_INT_MAX;
      }

      /* Use an integer here, not a boolean.
       * If a tag is found which has to be removed with its content - i.e. it is
       * listed in @c _tagsToRemoveWithContents - we search for that specific
       * tagin the next loop. If can be that that same tag is nested, and that
       * an opening tag is found again. By using a counter be can be sure when
       * the outer closing tag has been found.
       */
      $removeAllUntilEndOfTag = 0;

      $offset = 0;
      $currentUnitIsOngoing = false;
      $continue = true;
      $tagPattern = JString::str_ireplace( self::_tagReplacer, self::_anyTag, self::_tagPattern );
      while ( $continue )
      {
        /* Find HTML tags. Each match is an array of (mostly) interesting data
         * about the tag - see @c _tagPattern. Using that, we can find the plain
         * text in front of it.
         */
        /* By using PREG_OFFSET_CAPTURE, each entry in the array returned will
         * be an array itself, containing the matches (sub)string and the starting
         * offset.
         */
        $matchCount = preg_match( $tagPattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset );
        if ( !/*NOT*/$matchCount or ( count( $matches ) < 4 ) )
        {
          /* No (more) HTML tags were found. We can not just assume that the last part
           * of the string is always a HTML tag, especially not when no wysiwig editor
           * has been used to create this article.
           * Fetch the remainder of text, and set the needed variables to some dummy value,
           * so that the loop can finish correctly.
           */
          $matchCount = preg_match ( "/.*/is", $text, $matches, PREG_OFFSET_CAPTURE, $offset );
          $plainText = $matches[0][0];
          $fullTag = "";
          $tag = "";
          $isSelfClosingTag = false;
          $isClosingTag = false;
          //$offset = /* Don't care */
          $continue = false;
        }
        else
       {
          $plainText = $matches[1][0];
          $fullTag = $matches[2][0];
          $tag = JString::strtolower( $matches[4][0] );

          /* Determine whether the tag we found is an opening tag (e.g. <abc>),
           * a closing tag (e.g. </abc>) or a self-closing tag (e.g. <abc />).
           * If a closing slash is present, it is captured by the regular
           * expression, either in index 3, either in the last-but-one index
           * - and the last-but-one index is always greater than 3.
           */
          $isSelfClosingTag = $matches[ count( $matches) - 2 ][0] == '/';
          $isClosingTag = $matches[3][0] == '/';

          /* Those pesky html4 tags are still lingering around. Be sure to
           * correctly determine a tag as self-closing, even when no closing
           * slash was present. e.g. <br> vs. <br/>
           */
          if ( !/*NOT*/ $isClosingTag )
          {
            if ( in_array( $tag, $this->_extraSelfClosingTags ) )
            {
              $isSelfClosingTag = true;
            }
          }

          /* Prepare the next loop: the value of $offset determines where the next search starts. */
          $offset = $matches[ count( $matches) - 1 ][1] + 1;
          //$continue = /* Remains true for now. May become false below. */
        }

        if ( $removeAllUntilEndOfTag )
        {
          /* We're inside a block of code of which nothing may be retained in the cut-off text.
           * Do not add the plaintext and tag strings.
           */
          if ( $isClosingTag )
          {
            $removeAllUntilEndOfTag--;
            if ( $removeAllUntilEndOfTag <= 0 )
            {
              $tagPattern = JString::str_ireplace( self::_tagReplacer, self::_anyTag, self::_tagPattern );
            }
          }
          else
         {
            $removeAllUntilEndOfTag++;
          }
        }
        else
       {
          /* Add plaintext */
          $end = true;
          $length = ReadLessTextHelper::DetermineLength( $plainText . $fullTag, $this->_lengthUnit, $end );
          if ( $currentUnitIsOngoing and $end )
          {
            if ( !/*NOT*/ $length )
            {
              $length = 1;
            }
          }
          if ( $length and !/*NOT*/$end )
          {
            $length--;
          }
          $currentUnitIsOngoing = !/*NOT*/ $end;
          if ( $length >= $nrOfUnitsYetToRetain )
          {
            $plainText = ReadLessTextHelper::Substr( $plainText, $nrOfUnitsYetToRetain, $this->_lengthUnit, $this->_retainWholeWords );
            $result .= $plainText;
            if ( !/*NOT*/ $this->_suffixIsAdded )
            {
              $result .= $this->_suffix;
              $this->_suffixIsAdded = true;
            }
            //$nrOfUnitsYetToRetain = /* Don't care */
            $this->_isShortened = true;
            $continue = false;
          }
          else
         {
            $result .= $plainText;
            $nrOfUnitsYetToRetain -= $length;
          }

          /* - Possibly add the tag,
           * - update the list of opened-and-not-yet-closed tags and
           * - prepare the next loop.
           */
          if ( in_array( $tag, $this->_tagsToRemoveWithContents ) )
          {
            /* We don't know what will be stripped away in the shortened text:
             * it could be markup, a title or a table.
             * For sure is that the author this tag with its contents in the
             * full article, and that it is not taken along in the shortened
             * version. Even when all the remainder of the article fits in the
             * shortened text, it is best to ensure a pre- and/or suffix is
             * appended when done shortening.
             */
            $this->_isShortened = true;

            /* Do not add the tag. */
            if ( $isClosingTag or $isSelfClosingTag )
            {
              /* Nothing more to do. */
            }
            else
           {
              $removeAllUntilEndOfTag++;
              $tagPattern = JString::str_ireplace( self::_tagReplacer, $tag, self::_tagPattern );
            }
          }
          else if ( ( in_array( $tag, $this->_tagsToRemove ) )
              or ( in_array( 'all', $this->_tagsToRemove ) ) )
          {
            /* Do not add the tag. */
          }
          else if ( $isSelfClosingTag )
          {
            $result .= $fullTag;
          }
          else if ( $isClosingTag )
          {
            $result .= $fullTag;
            /* For simplicity, just assume at this point the text only contains
             * valid HTML, i.e. that all opening tags are properly closed in the
             * correct order. That means we do not need to check whether some
             * pushed opening tag matches with this closing tag: it just has to
             * be.
             */
            unset( $openTags[ count( $openTags ) - 1 ] );
            $openTags = array_values( $openTags ); /* re-index */
          }
          else if ( $tag )
          {
            /* The tag found is a valid opening tag that must remain in the cut-off text. */
            $result .= $fullTag;
            $openTags[] = $tag;
          }
        }
      } /* while ( $continue ) */

      if ( !/*NOT*/ $this->_suffixIsAdded and $this->_isShortened )
      {
        $result .= $this->_suffix;
        $this->_suffixIsAdded = true;
      }

      /* All tags that were opened and not yet closed are now closed here
       * in the correct order: i.e. in reverse order.
       */
      for( $i = count( $openTags ) - 1; $i >= 0; $i-- )
      {
        $result .= '</' . $openTags[ $i ] . '>';
      }

      return $result;
    }

    /* *********************************************************************** */

    /**
     * Various options needed while operating on the text.
     * May only be set in _PrepareArticleText or _GetParams.
     * Once set, it is to be considered RO.
     * @{
     */
    private $_cache = null; /* Set correctly in _PrepareArticleText(). */

    private $_addPrefix = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
    private $_addSuffix = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
    private $_applyFormatting = 'no'; /**< Must be checked after checking the article's length, (if applicable) in combination with _isShortened. */
    private $_createThumbnail = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
    private $_prefix = ''; /* Set correctly in_GetParams() */
    private $_suffix = ''; /* Set correctly in_GetParams() */
    private $_articleUrl = '.';
    private $_retainWholeWords = false;
    private $_crop = array( 'horizontal_position' => 'no', 'vertical_position' => 'no' );

    private $_cacheTime = 2419200 /* 4 weeks */;
    private $_thumbWidth = 0;
    private $_thumbHeight = 0;
    private $_minimum = array( 'width' => 0, 'height' => 0, 'ratio' => 0.05 );

    private $_extraSelfClosingTags = array();
    private $_tagsToRemove = array();
    private $_tagsToRemoveWithContents = array();
    private $_squareTokensToRemoveWithContents = array();
    private $_curlyTokensToRemoveWithContents = array();

    private $_htmltext = ''; /* Set correctly in _PrepareArticleText() */
    private $_plaintext = ''; /* Set correctly in _PrepareArticleText() */
    private $_lengthUnit = 'char'; /* Set correctly in _PrepareArticleText() */
    private $_wordCount = 0; /* Set correctly in _PrepareArticleText() */
    private $_hash = 0; /* Set correctly in _PrepareArticleText(). */
    /** @} */

    /**
     * Can be set to true, when an existing manually inserted read more token
     * is found; or later on, when the formatting options cause the removal of
     * parts of the article; or when the article is automatically shortened and
     * some remainder of the article is excluded.
     * Once set to true, the variable may not be set to false again.
     */
    private $_isShortened = false; /* Reset in_GetParams() */

    /**
     * Used to ensures at most one pre- and suffix is added, since that can occur
     * at several places.
     * @{
     */
    private $_prefixIsAdded = false; /* Reset in_GetParams() */
    private $_suffixIsAdded = false; /* Reset in_GetParams() */
    /** @} /*

    /* *********************************************************************** */

    /**
     * Used to find an image tag in a text.
     *
     * A match found using this regular expression will return at each index:
     * [0] The complete @c img tag, inclusive the brackets and the attributes
     * [1] The value of @src attribute, i.e. the URL where the image can be fetched.
     * [last] The closing bracket. Used to know the precise end byte
     *     offset of the matched tag. Especially needed for multi byte strings
     *     (some of the attributes inside the tag might very well be that),
     *     since JString::strlen returns the number characters, not the number
     *     of bytes. The offset given to preg_match needs to be a byte offset.
     *
     * Note: It is not possible (or: I could not get it to work) to include correct
     * positions together with the found matches when using non-ASCII UTF8 strings
     * when using this pattern with preg_match.
     * The matches returned seem correct though, and strpos can be used to fetch
     * the starting UTF8 character index.
     *
     * @note This is a simplified version of _tagPattern. Potentially this can
     * match a great portion of the text, crossing several html tags, _if_ an img
     * tag is given _without_ a src attribute. If this happens, give them what they
     * are asking for. Or, in other words: don't worry about that.
     */
    const _imgPattern = '/<img.+?src\s*=\s*["\']([^"\']+)["\'][^>]*(>)/mis';
    /*                                           1111111            -1    */

    /**
     * Used to parse an html text by finding all the HTML tags.
     * Thanks to http://kev.coolcavemen.com/2007/03/ultimate-regular-expression-for-html-tag-parsing-with-php/
     * It is a bit modified, to catch the tag without attributes and brackets,
     * the closing tag character '/', and the last char in the match as well.
     * @note Before usage, the string @c _tagReplacer in @c _tagPattern must be replaced with the tag to search for,
     *   or with @c _anyTag
     *
     * A match found using this regular expression will return at each index:
     * [0] The full match; i.e. the concatenation of everything below
     * [1] The plaintext preceding the tag
     * [2] The complete tag, inclusive the brackets and the attributes
     * [3] Either empty, either '/' when the tag is a closing tag.
     * [4] The tag name
     * [5] The full attributes - possibly not present
     * [6] ...
     * [last but one] If the tag is self closing: '/'. Else, and if attributes
     *     are present: the last attribute. Else: the empty string.
     * [last] The closing bracket. Used to know the precise end byte
     *     offset of the matched tag. Especially needed for multi byte strings
     *     (some of the attributes inside the tag might very well be that),
     *     since JString::strlen returns the number characters, not the number
     *     of bytes. The offset given to preg_match needs to be a byte offset.
     *
     * @note the xxx part is to be replaced
     * - replace with _anyTag to catch any tag
     * - replace with 'abc' to catch tag abc
     *
     * @{
     */
    const _tagPattern = "/(.*?)(<(\/?)(xxx)((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)(\/?)(>))/mis";
    /*                      111    333  444   --- attribute ------------------------------------------           -2   -1     */
    const _tagReplacer = 'xxx';
    const _anyTag = '\w+';
    /** @} */

    /**
     * Used to find third party token indications like [pgn]...[/pgn]
     * What can not be detected:
     * - nested token codes
     * - self closing token codes
     * - escaped starts of token codes
     *
     * A match found using this regular expression will return at each index:
     * [0] The complete token code, inclusive the opening character and the closing token.
     *
     * @note the xxx, yy and zzz parts are to be replaced
     * - replace xxx with [ or { or ... to catch the start of a token
     * - replace yyy with ] or } or ... to catch the end of a token
     *
     * @{
     */
    const _tokenPattern = "/xxx\s*zzz.*?yyy.*?xxx\s*\/\s*zzzs?\s*yyy/mis";
    const _tokenOpeningReplacer = 'xxx';
    const _tokenClosingReplacer = 'yyy';
    const _tokenReplacer = 'zzz';
    /** @} */

    private static $_callCount = 0;

//     private $profiler = null;
//     private $profiles = null;
}

?>
