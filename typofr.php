<?php
/*
Plugin Name: TypoFR
Plugin URI: https://github.com/borisschapira/typofr
Description: a plugin for french typography management, powered by JoliTypo
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
    $reEncoded = utf8_encode($decoded);

    $fixed = $fixer->fix($decoded);
    $fixedWrong = $fixer->fix($reEncoded);

    $logs = "<script> console && console.log('-------\\nOriginal : ".$text." (".mb_detect_encoding($text).")\\nDecoded : ".$decoded." (".mb_detect_encoding($decoded).")\\nRe-encoded : ".$reEncoded." (".mb_detect_encoding($reincoded).")\\nFix(Decoded) : ".$fixed." (".mb_detect_encoding($fixed).")\\nFix(Re-encoded) : ".$fixedWrong." (".mb_detect_encoding($fixedWrong).")')</script>";

    return $fixed.$logs;
}

//add_filter('the_content', 'typofr');
add_filter('the_title', 'typofr');

