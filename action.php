<?php
/**
 * DokuWiki Action Plugin minical
 * 
 * @author Alexandre Bastien <alexandre.bastien@fsaa.ulaval.ca>
 * Forked from WikiCalendar (Michael Klier <chi@chimeric.de>)
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
if(!defined('DOKU_LF')) define('DOKU_LF', "\n");

require_once(DOKU_PLUGIN.'action.php');

 // All DokuWiki plugins to extend the admin function
 // need to inherit from this class
class action_plugin_minical extends DokuWiki_Action_Plugin {

    function getInfo() {
        return array(
                'author' => 'Alexandre Bastien',
                'email'  => 'alexandre.bastien@fsaa.ulaval.ca',
                'date'   => @file_get_contents(DOKU_PLUGIN.'minical/VERSION'),
                'name'   => 'MiniCal Plugin (action component)',
                'desc'   => 'Implements a simple Calendar with links to wikipages. Forked from minical.',
                'url'    => 'http://dokuwiki.org/plugin:minical',
            );
    }

    // register hook
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_SHOW_REDIRECT', 'BEFORE', $this, 'handle_redirect');
        $controller->register_hook('HTML_EDITFORM_OUTPUT', 'BEFORE', $this, 'handle_form');
        $controller->register_hook('FORM_EDIT_OUTPUT', 'BEFORE', $this, 'handle_form');
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'handle_started');
    }

     // Checks for calendar values for proper redirects
    function handle_started(Doku_Event $event, $param) {
        if(is_array($_SESSION[DOKU_COOKIE])) {
            if(array_key_exists('plugin_minical_month', $_SESSION[DOKU_COOKIE])) {
                $_REQUEST['plugin_minical_month'] = $_SESSION[DOKU_COOKIE]['plugin_minical_month'];
                $_REQUEST['plugin_minical_year']  = $_SESSION[DOKU_COOKIE]['plugin_minical_year'];
                unset($_SESSION[DOKU_COOKIE]['plugin_minical_month']);
                unset($_SESSION[DOKU_COOKIE]['plugin_minical_year']);
            }
        }
    }

     // Inserts the hidden redirect id field into edit form
    function handle_form(Doku_Event $event, $param) {
        if(array_key_exists('plugin_minical_redirect_id', $_REQUEST)) {
            $form = $event->data;
            if(is_a($form, \dokuwiki\Form\Form::class)) {
                $form->setHiddenField('plugin_minical_redirect_id', cleanID($_REQUEST['plugin_minical_redirect_id']));
                $form->setHiddenField('plugin_minical_month', cleanID($_REQUEST['plugin_minical_month']));
                $form->setHiddenField('plugin_minical_year', cleanID($_REQUEST['plugin_minical_year']));
            } else {
                $form->addHidden('plugin_minical_redirect_id', cleanID($_REQUEST['plugin_minical_redirect_id']));
                $form->addHidden('plugin_minical_month', cleanID($_REQUEST['plugin_minical_month']));
                $form->addHidden('plugin_minical_year', cleanID($_REQUEST['plugin_minical_year']));
            }
        }
    }

     // Redirects to the calendar page
    function handle_redirect(Doku_Event $event, $param) {
        if(array_key_exists('plugin_minical_redirect_id', $_REQUEST)) {
            @session_start();
            $_SESSION[DOKU_COOKIE]['plugin_minical_month'] = $_REQUEST['plugin_minical_month'];
            $_SESSION[DOKU_COOKIE]['plugin_minical_year']  = $_REQUEST['plugin_minical_year'];
            @session_write_close();
            $event->data['id'] = cleanID($_REQUEST['plugin_minical_redirect_id']);
            $event->data['title'] = '';
        }
    }
}

