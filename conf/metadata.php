<?php
/**
 * Configuration metadata for DokuWiki Plugin MiniCal
 * @author Alexandre Bastien <alexandre.bastien@fsaa.ulaval.ca>
 */
$meta['weekstart'] = array('multichoice', '_choices' => array('Monday', 'Sunday'));
$meta['timezone']  = array('multichoice', '_choices' => timezone_identifiers_list());
