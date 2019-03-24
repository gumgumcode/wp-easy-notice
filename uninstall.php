<?php
// die when the file is called directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// remove all registered options when plugin is deleted.
delete_option('sn_opt_msg');
delete_option('sn_opt_checkbox_posts');
delete_option('sn_opt_checkbox_pages');
delete_option('sn_opt_checkbox_home');
delete_option('sn_opt_ryg');
?>