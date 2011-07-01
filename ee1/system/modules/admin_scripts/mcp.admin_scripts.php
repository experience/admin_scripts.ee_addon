<?php if ( ! defined('EXT')) exit('Direct file access not allowed');

/**
 * Admin Scripts module Control Panel file.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 */

require_once PATH_MOD .'admin_scripts/models/admin_scripts_model' .EXT;

class Admin_scripts_CP {

    private $_model;
    public $version;


    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   bool        $switch         My whole life I don't know what this song means.
     * @param   mixed       $model          Dummy model. Used for testing.
     * @return  void
     */
    public function __construct($switch = TRUE, $model = NULL)
    {
        $this->_model       = $model ? $model : new Admin_scripts_model();
        $this->_base_qs     = 'C=modules' .AMP .'M=' .$this->_model->get_package_name();
        $this->_base_url    = BASE .AMP .$this->_base_qs;
        
        // Is the module even installed?
        if ($installed_version = $this->_model->get_installed_module_version())
        {
            $this->version  = $this->_model->get_package_version();
            $this->_model->update_module($installed_version, $this->version);
            
            if ($switch)
            {
                $this->handle_action();
                $this->handle_view();
            }
        }
    }


    /**
     * Uninstalls the module.
     *
     * @access  public
     * @return  bool
     */
    public function admin_scripts_module_deinstall()
    {
        return $this->_model->uninstall_module();
    }


    /**
     * Installs the module.
     *
     * @access  public
     * @return  bool
     */
    public function admin_scripts_module_install()
    {
        return $this->_model->install_module();
    }


    /**
     * Handles the querystring action.
     *
     * @access  public
     * @return  void
     */
    public function handle_action()
    {
        global $IN;

        switch ($IN->GBL('A'))
        {
            case 'run_script':
                $this->_run_script();
                break;
        }
    }


    /**
     * Handles the querystring view.
     *
     * @access  public
     * @return  void
     */
    public function handle_view()
    {
        global $IN;

        switch ($IN->GBL('P'))
        {
            case 'admin_scripts':
            default:
                $this->_display_admin_scripts();
                break;
        }
    }


    /* --------------------------------------------------------------
     * PRIVATE METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Displays the 'admin_scripts' view.
     *
     * @access  private
     * @return  void
     */
    private function _display_admin_scripts()
    {
        global $DSP, $LANG;

        $view_vars = array(
            'admin_scripts'     => $this->_model->get_admin_scripts(),
            'run_script_url'    => $this->_base_url .AMP .'P=admin_scripts' .AMP .'A=run_script' .AMP
        );

        $this->_load_view('admin_scripts', $view_vars);
    }


    /**
     * Handles the 'run_script' action.
     *
     * @access  private
     * @return  void
     */
    private function _run_script()
    {
        global $IN, $LANG;

        $bundle = $IN->GBL('bundle');
        $script = $IN->GBL('script');

        if ( ! $bundle)
        {
            $status_message = new EI_status_message(array(
                'message'   => $LANG->line('status_message__run_script__missing_bundle'),
                'type'      => EI_status_message::ERROR
            ));

            $this->_model->add_status_message($status_message);
            return;
        }

        if ( ! $script)
        {
            $status_message = new EI_status_message(array(
                'message'   => $LANG->line('status_message__run_script__missing_script'),
                'type'      => EI_status_message::ERROR
            ));

            $this->_model->add_status_message($status_message);
            return;
        }

        if ( ! $this->_model->run_script($bundle, $script))
        {
            $status_message = new EI_status_message(array(
                'message'   => $LANG->line('status_message__run_script__failure'),
                'type'      => EI_status_message::ERROR
            ));

            $this->_model->add_status_message($status_message);
            return;
        }

        $status_message = new EI_status_message(array(
            'message'   => $LANG->line('status_message__run_script__success'),
            'type'      => EI_status_message::INFO
        ));

        $this->_model->add_status_message($status_message);
    }


    /**
     * Loads the specified view.
     *
     * @access  private
     * @param   string      $view       The view to display.
     * @param   array       $vars       The view variables.
     * @param   array       $crumbs     The breadcrumbs.
     * @return  void
     */
    private function _load_view($view, $vars = array(), $crumbs = array())
    {
        global $DSP, $LANG;
        
        $theme_url          = $this->_model->get_theme_url();
        $package_name       = $this->_model->get_package_name();
        $lang_module_name   = $LANG->line($package_name .'_module_name');
        
        // CSS and JS.
        $headers = '<link rel="stylesheet" href="' .$theme_url .'css/cp.css" />';
        $footers = '<script src="' .$theme_url .'js/cp.js"></script>';
        
        // Add some extra goodies to the view variables array.
        $common_vars = array(
            'base_module_url'   => $this->_base_url .AMP .'P=',
            'browser_title'     => '',          // Just so we don't have to check for its existence, below.
            'include_path'      => PATH_MOD .$package_name .'/views/',
            'module_name'       => $lang_module_name,
            'module_version'    => $this->_model->get_package_version(),
            'status_messages'   => $this->_model->get_status_messages()
        );
        
        $vars = array_merge($common_vars, $vars);
        
        // Append the standard string to the browser title.
        $vars['browser_title'] = $vars['browser_title'] ? $vars['browser_title'] .' | ' .$lang_module_name : $lang_module_name;
        
        // Output everything.
        $DSP->extra_header  .= $headers;
        $DSP->title         = $vars['browser_title'];
        $DSP->crumbline     = TRUE;
        $DSP->crumb         = $DSP->anchor($this->_base_url, $lang_module_name);
        $DSP->body          .= $DSP->view($view, $vars, TRUE) .$footers;
    }
    

}


/* End of file      : mod.admin_scripts.php */
/* File location    : /system/modules/admin_scripts/mod.admin_scripts.php */
