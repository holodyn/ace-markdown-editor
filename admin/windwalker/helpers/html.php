<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */


// No direct access
defined('_JEXEC') or die;

/**
 * HTML Helper to handle some text.
 *
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 */
class AKHelperHtml {
    
    /**
     * Repair HTML. If Tidy not exists, use repair function.
     * 
     * @param   string  $html       The HTML string to repair.
     * @param   boolean $use_tidy   Force tidy or not.
     *
     * @return  string  Repaired HTML.
     */
    public static function repair($html , $use_tidy = true ) {
        
        if(function_exists('tidy_repair_string') && $use_tidy ):
        
            $TidyConfig = array('indent'        => true,
                                'output-xhtml'  => true,
                                'show-body-only'=> true,
                                'wrap'          => false
                                );
            return tidy_repair_string($html,$TidyConfig,'utf8');
        
        else:
        
            $arr_single_tags = array('meta','img','br','link','area');
            
            //put all opened tags into an array
            preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
            $openedtags = $result[1];
         
            //put all closed tags into an array
            preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
            $closedtags = $result[1];
            $len_opened = count ( $openedtags );
            
            // all tags are closed
            if( count ( $closedtags ) == $len_opened )
            {
                return $html;
            }
            
            $openedtags = array_reverse ( $openedtags );
            
            // close tags
            for( $i = 0; $i < $len_opened; $i++ )      
            {
                if ( !in_array ( $openedtags[$i], $closedtags ) )
                {
                    if(!in_array ( $openedtags[$i], $arr_single_tags )) $html .= "</" . $openedtags[$i] . ">";
                }
                else
                {
                    unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
                }
            }
            
            return $html;
        
        endif;
    }
    
    /**
     * Parse BBCode and convert to HTML.
     *
     * Use jBBCode library: http://jbbcode.com/
     * 
     * @param   string  $text   Text to parse BBCode.
     *
     * @return  string  Parsed text.
     */
    public static function bbcode($text)
    {
        require_once( "phar://".AKPATH_HTML."/jbbcode/jbbcode.phar/Parser.php" );

        $parser = new JBBCode\Parser();
        $parser->loadDefaultCodes();
         
        $parser->parse($text);
         
        print $parser->getAsHtml();    
    }
    
    /**
     * Parse Markdown and convert to HTML.
     *
     * Use PHP Markdown & Markdown Extra: http://michelf.ca/projects/php-markdown/
     * 
     * @param    string     $text    Text to parse Markdown.
     * @param    string     $extra   Use MarkdownExtra: http://michelf.ca/projects/php-markdown/extra/ .
     *
     * @return   string     Parsed Text.
     */
    public static function markdown($text, $extra = true, $option = array())
    {
        require_once( "phar://".AKPATH_HTML."/php-markdown/php-markdown.phar/Markdown.php" );
        
        $text = str_replace( "\t", '    ', $text );
        
        if($extra){
            require_once( "phar://".AKPATH_HTML."/php-markdown/php-markdown.phar/MarkdownExtra.php" );
            $result =  Michelf\MarkdownExtra::defaultTransform($text);
        }else{
            $result =  Michelf\Markdown::defaultTransform($text);
        }
        
        self::highlight( JArrayHelper::getValue($option, 'highlight', 'default') );
        
        return $result ;
    }
    
    /**
     * Highlight Markdown <pre><code class="lang">.
     *
     * Use highlight.js: http://softwaremaniacs.org/soft/highlight/en/
     * 
     * @param   string  $theme  Code style name.
     */
    public static function highlight($theme = 'default')
    {
        $css = '/assets/js/highlight/styles/'.$theme.'.css' ;
        if( !JFile::exists(AKPATH_ROOT.$css) ) {
            $css = '/assets/js/highlight/styles/default.css' ;
        }
        
        static $loaded = false;
        
        $doc = JFactory::getDocument();
        $doc->addStylesheet( AKHelper::_('path.getWWUrl').$css );
        $doc->addScript(AKHelper::_('path.getWWUrl').'/assets/js/highlight/highlight.pack.js');
        
        if(!$loaded){
            $doc->addScriptDeclaration("\n    hljs.initHighlightingOnLoad();");
            $loaded = true;
        }
    }
}


