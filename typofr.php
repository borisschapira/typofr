<?php
/*
Plugin Name: TypoFR
Plugin URI: https://github.com/borisschapira/typofr
Description: a plugin for french typography management, inspired by SPIP
Version: 0.1
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
//            'Ellipsis',
//            'Dimension',
//            'Dash',
//            'FrenchQuotes',
//            'FrenchNoBreakSpace',
//            'CurlyQuote',
//            'Hyphen',
            'Trademark'));
        $fixer->setLocale('fr_FR'); // Needed by the Hyphen Fixer
    }

    $fixed = $fixer->fix($text);
    return $text."<script>console && console.log('-------');console && console.log('".$text." (".mb_detect_encoding($text).")'); console && console.log('".$fixed." (".mb_detect_encoding($fixed).")')</script>";
}

//add_filter('the_content', 'typofr');
add_filter('the_title', 'typofr');

