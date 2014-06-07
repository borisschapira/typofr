<?php
/*
Plugin Name: TypoFR
Plugin URI: https://github.com/borisschapira/typofr
Description: a plugin for french typography management, inspired by SPIP
Version: 0.1
Author: Boris Schapira
Author URI: http://borisschapira.com
License: GPL2
*/

// Correction typographique francaise
// Function fortement inspirée du travail de Arnaud Martin, Antoine Pitrou,
// Philippe Riviere, Emmanuel Saint-James sur SPIP
function typofr($text)
{
    /** @var array $pairs Pairs of strings to replace * */
    static $pairs;

    /** @var string $joker A string that should not exist */
    $joker = "-\x2-";

    if (!isset($pairs)) {
        $pairs = array(
            "&nbsp;" => '~', // on utilise le ~ comme joker d'espace insécable
            "&raquo;" => '&#187;', // »
            "&laquo;" => '&#171;', // «
            "&rdquo;" => '&#8221;', // ”
            "&ldquo;" => '&#8220;', // “
            "&deg;" => '&#176;', // degree
            "'" => '&#8217;' // apostrophe
        );
    }

    $text = str_replace(array_keys($pairs), array_values($pairs), $text);

    // S'il y a un ; dans le texte, on doit normalement le traiter,
    // mais on risque de toucher les entitées &xxx; donc on réalise
    // d'abord ce remplacement
    if (strpos($text, ';') !== false) {
        $text = str_replace(';', '~;', $text); // tout ; doit être précédé d'un espace insecable
        $text = preg_replace(',(&#?[0-9a-z]+)~;,iS', '$1;', $text);; // sauf s'il fait partie d'une entité &xxx;
    }

    $text = preg_replace('/[!?][!?\.]*/S', "$joker~$0", $text, -1, $c);
    if ($c) {
        $text = preg_replace("/([\[<\(!\?\.])$joker~/S", '$1', $text);
        $text = str_replace("$joker", '', $text);
    }

    $text = preg_replace('/&#171;|M(?:M?\.|mes?|r\.?|&#176;) |[nN]&#176; /S', '$0~', $text);

    if (strpos($text, '~') !== false)
        $text = preg_replace("/ *~+ */S", "~", $text);

    $text = preg_replace("/--([^-]|$)/S", "$joker&mdash;$1", $text, -1, $c);
    if ($c) {
        $text = preg_replace("/([-\n])$joker&mdash;/S", "$1--", $text);
        $text = str_replace($joker, '', $text);
    }

    $text = preg_replace(',(' ._PROTOCOLES_STD . ')~((://[^"\'\s\[\]\}\)<>]+)~([?]))?,S', '$1$3$4', $text);
    $text = str_replace('~', '&nbsp;', $text);

    return $text;
}

add_filter('the_content', 'typofr');
add_filter('the_title', 'typofr');