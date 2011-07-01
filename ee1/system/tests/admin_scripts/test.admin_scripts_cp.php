<?php if ( ! defined('EXT')) exit('Direct file access not allowed');

/**
 * Admin Scripts CP tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 */

require_once PATH .'tests/admin_scripts/mocks/mock.admin_scripts_model'. EXT;
require_once PATH_MOD .'admin_scripts/mcp.admin_scripts'. EXT;

class Test_admin_scripts_CP extends Testee_unit_test_case {

    private $_installed_version;
    private $_model;
    private $_package_name;
    private $_package_version;
    private $_site_id;
    private $_subject;
    private $_theme_url;
    
    
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

        $this->_installed_version = '0.9.0';
        $this->_package_name    = 'example_package';
        $this->_package_version = '1.0.0';
        $this->_site_id         = 10;
        $this->_theme_url       = 'http://example.com/themes/';
        
        Mock::generate('Mock_admin_scripts_model', get_class($this) .'_mock_admin_scripts_model');
        $this->_model   = $this->_get_mock('admin_scripts_model');

        $this->_model->setReturnValue('get_installed_module_version', $this->_installed_version);
        $this->_model->setReturnValue('get_package_name', $this->_package_name);
        $this->_model->setReturnValue('get_package_version', $this->_package_version);
        $this->_model->setReturnValue('get_site_id', $this->_site_id);
        $this->_model->setReturnValue('get_theme_url', $this->_theme_url);
    }


    public function test__constructor__success()
    {
        $this->_model->expectOnce('get_installed_module_version');
        $this->_model->expectOnce('get_package_name');
        $this->_model->expectOnce('get_package_version');
        $this->_model->expectOnce('update_module', array($this->_installed_version, $this->_package_version));

        new Admin_scripts_CP(FALSE, $this->_model);
    }


    public function test__display_admin_scripts__success()
    {
        global $DSP, $IN;

        $DSP->expectOnce('view', array('admin_scripts', '*', TRUE));
        $IN->setReturnValue('GBL', 'admin_scripts', array('P'));

        $this->_model->expectOnce('get_admin_scripts');
        $this->_model->expectOnce('get_status_messages');

        $subject = new Admin_scripts_CP(TRUE, $this->_model);
    }


    public function test__run_script__success()
    {
        global $IN, $LANG;

        $action = 'run_script';
        $bundle = 'example_bundle';
        $script = 'example_script';
    
        $IN->setReturnValue('GBL', $action, array('A'));
        $IN->setReturnValue('GBL', $bundle, array('bundle'));
        $IN->setReturnValue('GBL', $script, array('script'));

        $this->_model->expectOnce('run_script', array($bundle, $script));
        $this->_model->setReturnValue('run_script', TRUE);

        $message        = 'Everything A-OK';
        $status_message = new EI_status_message(array('message' => $message, 'type' => EI_status_message::INFO));

        $LANG->setReturnValue('line', $message, array('status_message__run_script__success'));
        $this->_model->expectOnce('add_status_message', array($status_message));

        $subject = new Admin_scripts_CP(TRUE, $this->_model);
    }


    public function test__run_script__failure()
    {
        global $IN, $LANG;

        $action = 'run_script';
        $bundle = 'example_bundle';
        $script = 'example_script';
    
        $IN->setReturnValue('GBL', $action, array('A'));
        $IN->setReturnValue('GBL', $bundle, array('bundle'));
        $IN->setReturnValue('GBL', $script, array('script'));

        $this->_model->expectOnce('run_script', array($bundle, $script));
        $this->_model->setReturnValue('run_script', FALSE);

        $message        = 'Disaster!';
        $status_message = new EI_status_message(array('message' => $message, 'type' => EI_status_message::ERROR));

        $LANG->setReturnValue('line', $message, array('status_message__run_script__failure'));
        $this->_model->expectOnce('add_status_message', array($status_message));

        $subject = new Admin_scripts_CP(TRUE, $this->_model);
    }


    public function test__run_script__missing_bundle()
    {
        global $IN, $LANG;

        $action = 'run_script';
        $bundle = FALSE;
        $script = 'example_script';

        $IN->setReturnValue('GBL', $action, array('A'));
        $IN->setReturnValue('GBL', $bundle, array('bundle'));
        $IN->setReturnValue('GBL', $script, array('script'));

        $message        = 'Missing bundle';
        $status_message = new EI_status_message(array('message' => $message, 'type' => EI_status_message::ERROR));

        $LANG->setReturnValue('line', $message, array('status_message__run_script__missing_bundle'));
        $this->_model->expectOnce('add_status_message', array($status_message));

        $this->_model->expectNever('run_script');
        $subject = new Admin_scripts_CP(TRUE, $this->_model);
    }


    public function test__run_script__missing_script()
    {
        global $IN, $LANG;

        $action = 'run_script';
        $bundle = 'example_bundle';
        $script = '';

        $IN->setReturnValue('GBL', $action, array('A'));
        $IN->setReturnValue('GBL', $bundle, array('bundle'));
        $IN->setReturnValue('GBL', $script, array('script'));

        $message        = 'Missing script';
        $status_message = new EI_status_message(array('message' => $message, 'type' => EI_status_message::ERROR));

        $LANG->setReturnValue('line', $message, array('status_message__run_script__missing_script'));
        $this->_model->expectOnce('add_status_message', array($status_message));

        $this->_model->expectNever('run_script');
        $subject = new Admin_scripts_CP(TRUE, $this->_model);
    }


}


/* End of file      : test.admin_scripts_cp.php */
/* File location    : /system/tests/admin_scripts/test.admin_scripts_cp.php */
