<?php

/**
 * Mock Admin Scripts model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 */

class Mock_admin_scripts_model {

    public function add_status_message(EI_status_message $message) {}
    public function get_admin_scripts() {}
    public function get_installed_module_version() {}
    public function get_package_name() {}
    public function get_package_version() {}
    public function get_site_id() {}
    public function get_status_messages() {}
    public function get_theme_url() {}
    public function install_module() {}
    public function install_module_register() {}
    public function run_script($bundle, $script) {}
    public function uninstall_module() {}
    public function update_module($installed_version = '', $package_version = '') {}

}


/* End of file      : mock.action_scripts_model.php */
/* File location    : /system/tests/admin_scripts/mocks/mock.action_scripts.php */
