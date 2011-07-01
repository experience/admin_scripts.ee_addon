<?php if ( ! defined('EXT')) exit('Direct file access not allowed');

/**
 * Admin Scripts model tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 */

require_once PATH_MOD .'admin_scripts/models/admin_scripts_model'. EXT;

class Test_admin_scripts_model extends Testee_unit_test_case {

    private $_site_id;
    private $_subject;
    
    
    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Runs before each test.
     *
     * @access  public
     * @return  void
     */
    public function setUp()
    {
        global $PREFS;
        
        parent::setUp();
        
        // Model::get_site_id gets called so often, we just mock the $PREFS site_id here.
        $this->_site_id = 10;
        $PREFS->setReturnValue('ini', $this->_site_id, array('site_id'));
        
        // Create the test subject.
        $this->_subject = new Admin_scripts_model();
    }


    public function test__get_installed_module_version__success()
    {
        global $DB;

        $package_name = ucfirst($this->_subject->get_package_name());
        
        $sql = "SELECT module_version
            FROM exp_modules
            WHERE module_name = '{$package_name}'
            LIMIT 1";
        
        $version    = '1.0.0';
        $db_result  = $this->_get_mock('db_cache');
        $db_row     = array('module_version' => $version);
        
        $DB->expectOnce('query', array(new EqualWithoutWhitespaceExpectation($sql)));
        
        $DB->setReturnReference('query', $db_result);
        $db_result->setReturnValue('__get', 1, array('num_rows'));
        $db_result->setReturnValue('__get', $db_row, array('row'));
        
        $this->assertIdentical($version, $this->_subject->get_installed_module_version());
    }
    
    
    public function test__get_installed_module_version__not_installed()
    {
        global $DB;
        
        $db_result = $this->_get_mock('db_cache');
        
        $DB->setReturnReference('query', $db_result);
        $db_result->setReturnValue('__get', 0, array('num_rows'));
        
        $this->assertIdentical('', $this->_subject->get_installed_module_version());
    }


    public function test__get_site_id__success()
    {
        global $PREFS;
        
        $site_id = 10;
        
        $PREFS->expectOnce('ini', array('site_id'));
        $PREFS->setReturnValue('ini', $site_id, array('site_id'));
        
        $this->assertIdentical($site_id, $this->_subject->get_site_id());
    }
    
    
    public function test__get_theme_url__with_end_slash()
    {
        global $PREFS;
        
        $theme_url  = '/path/to/themes/';
        $full_url   = $theme_url
            .'cp_themes/default/'
            .$this->_subject->get_package_name()
            .'/';
        
        $PREFS->expectOnce('ini', array('theme_folder_url'));
        $PREFS->setReturnValue('ini', $theme_url, array('theme_folder_url'));
        
        $this->assertIdentical($full_url, $this->_subject->get_theme_url());
    }
    
    
    public function test__get_theme_url__without_end_slash()
    {
        global $PREFS;
        
        $theme_url  = '/path/to/themes';
        $full_url   = $theme_url
            .'/cp_themes/default/'
            .$this->_subject->get_package_name()
            .'/';
        
        $PREFS->setReturnValue('ini', $theme_url, array('theme_folder_url'));

        $this->assertIdentical($full_url, $this->_subject->get_theme_url());
    }


    public function test__install_module_register__success()
    {
        global $DB;
        
        $package_name   = ucfirst($this->_subject->get_package_name());
        $insert_sql     = 'insert_sql';
        $module_data    = array(
            'has_cp_backend'    => 'y',
            'module_id'         => '',
            'module_name'       => $package_name,
            'module_version'    => $this->_subject->get_package_version()
        );
        
        $DB->expectOnce('insert_string', array('exp_modules', $module_data));
        $DB->expectOnce('query', array($insert_sql));
        $DB->setReturnValue('insert_string', $insert_sql);
        
        $this->_subject->install_module_register();
    }


    public function test__uninstall_module__success()
    {
        global $DB;
        
        $module_id          = 10;
        $package_name       = ucfirst($this->_subject->get_package_name());
        
        $delete_groups_sql  = "DELETE FROM exp_module_member_groups WHERE module_id = '{$module_id}'";
        $delete_modules_sql = "DELETE FROM exp_modules WHERE module_name = '{$package_name}'";
        $select_sql         = "SELECT module_id FROM exp_modules WHERE module_name = '{$package_name}' LIMIT 1";
        
        $db_query           = $this->_get_mock('db_cache');
        $db_row             = array('module_id' => $module_id);
        
        $DB->expectAt(0, 'query', array(new EqualWithoutWhitespaceExpectation($select_sql)));
        $DB->expectAt(1, 'query', array(new EqualWithoutWhitespaceExpectation($delete_groups_sql)));
        $DB->expectAt(2, 'query', array(new EqualWithoutWhitespaceExpectation($delete_modules_sql)));
        
        $DB->setReturnReferenceAt(0, 'query', $db_query);
        $db_query->setReturnValue('__get', 1, array('num_rows'));
        $db_query->setReturnValue('__get', $db_row, array('row'));
        
        $this->assertIdentical(TRUE, $this->_subject->uninstall_module());
    }
    
    
    public function test__uninstall_module__module_not_found()
    {
        global $DB;
        
        $db_query = $this->_get_mock('db_cache');
        
        $DB->setReturnReferenceAt(0, 'query', $db_query);
        $db_query->setReturnValue('__get', 0, array('num_rows'));
        
        $this->assertIdentical(FALSE, $this->_subject->uninstall_module());
    }


    public function test__update_module__update()
    {
        global $DB;
        
        $installed_version  = '1.0.0';
        $package_name       = ucfirst($this->_subject->get_package_name());
        $package_version    = '1.1.0';
        $update_criteria    = "module_name = '{$package_name}'";
        $update_data        = array('module_version' => $package_version);
        $update_sql         = 'UPDATE_SQL';
        
        $DB->expectOnce('query', array($update_sql));
        $DB->expectOnce('update_string', array('exp_modules', $update_data, $update_criteria));
        $DB->setReturnValue('update_string', $update_sql);
        
        $this->assertIdentical(TRUE, $this->_subject->update_module($installed_version, $package_version));
    }
    
    
    public function test__update_module__no_update()
    {
        global $DB;
        
        $DB->expectNever('query');
        $DB->expectNever('update_string');
        
        $installed_version  = '1.1.0';
        $package_version    = '1.1.0';
        $this->assertIdentical(FALSE, $this->_subject->update_module($installed_version, $package_version));
        
        $installed_version  = '1.1.0';
        $package_version    = '1.0.0';
        $this->assertIdentical(FALSE, $this->_subject->update_module($installed_version, $package_version));
        
        $installed_version  = '';
        $package_version    = '1.0.0';
        $this->assertIdentical(FALSE, $this->_subject->update_module($installed_version, $package_version));
        
        $installed_version  = '1.0.0';
        $package_version    = '';
        $this->assertIdentical(FALSE, $this->_subject->update_module($installed_version, $package_version));
    }


}


/* End of file      : test.admin_scripts_model.php */
/* File location    : /system/tests/admin_scripts/test.admin_scripts_model.php */
