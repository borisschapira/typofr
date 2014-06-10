<?php
/*
Plugin Name: TypoFR
Plugin URI: https://github.com/borisschapira/typofr
Description: a plugin for french typography management, powered by JoliTypo
Version: 0.2
Author: Boris Schapira
Author URI: http://borisschapira.com
License: MIT
*/

include 'vendor/autoload.php';

// Correction typographique francaise par JoliTypo

use JoliTypo\Fixer;

function typofr($text)
{
    static $fixer;
    if (!isset($fixer)) {
        $fixer = new Fixer(array(
            'Ellipsis',
            'Dimension',
            'Dash',
            'FrenchQuotes',
            'FrenchNoBreakSpace',
            'CurlyQuote',
            'Hyphen',
            'Trademark'));
        $fixer->setLocale('fr_FR'); // Needed by the Hyphen Fixer
    }

    $decoded = utf8_decode($text);
    $fixed = $fixer->fix($decoded);
    
    return $fixed;
}

add_filter('the_content', 'typofr');
add_filter('the_title', 'typofr');

