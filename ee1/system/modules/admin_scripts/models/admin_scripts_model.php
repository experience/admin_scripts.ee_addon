<?php if ( ! defined('EXT')) exit('Direct file access not allowed');

/**
 * Admin Scripts model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 * @version         0.1.0
 */

require_once PATH_MOD .'admin_scripts/classes/EI_status_message' .EXT;

class Admin_scripts_model {

    private $_package_name;
    private $_package_version;
    private $_site_id;
    private $_status_messages = array();
    private $_theme_folder_url;
    
    
    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $this->_package_name    = 'admin_scripts';
        $this->_package_version = '0.1.0';
    }


    /**
     * Adds a status message to the status messages array.
     *
     * @access  public
     * @param   EI_status_message   $message    The status message.
     * @return  array
     */
    public function add_status_message(EI_status_message $message)
    {
        $this->_status_messages[] = $message;
        return $this->_status_messages;
    }


    /**
     * Returns an array of all the available admin scripts. Admin Scripts assumes that
     * each add-on will define its own scripts, in a /system/admin_scripts/addon_name/
     * directory.
     *
     * @access  public
     * @return  array
     */
    public function get_admin_scripts()
    {
        $admin_scripts_path = PATH .'admin_scripts' .DIRECTORY_SEPARATOR;
        $admin_scripts = array();
        
        // Retrieve the contents of the admin_scripts directory.
        if ( ! $bundles = $this->_get_directory_contents($admin_scripts_path, 'DIRECTORY'))
        {
            return $admin_scripts;
        }
        
        foreach ($bundles AS $bundle)
        {
            $bundle_path = $admin_scripts_path .$bundle .DIRECTORY_SEPARATOR;
            
            if ( ! $bundle_contents = $this->_get_directory_contents($bundle_path, 'FILE'))
            {
                continue;
            }

            /**
             * Each bundle should contain:
             * 1. A bundle_config.php file.
             * 2. One or more script.script_name.php files.
             */

            $has_bundle_info    = FALSE;
            $bundle_scripts     = array();
            
            foreach ($bundle_contents AS $bundle_file)
            {
                // Config.
                if (preg_match('/^bundle_info' .EXT .'$/', $bundle_file))
                {
                    include_once $bundle_path .$bundle_file;     // Loads the bundle_name and bundle_description.

                    if (isset($bundle_info)
                        && is_array($bundle_info)
                        && array_key_exists('name', $bundle_info)
                        && array_key_exists('description', $bundle_info))
                    {
                        $has_bundle_info = TRUE;
                    }

                    continue;
                }

                // Script.
                if ( ! preg_match('/^script[_|\.](.*)' .EXT .'$/i', $bundle_file, $matches))
                {
                    continue;
                }

                include_once $bundle_path .$bundle_file;
                $class_name = ucfirst($bundle .'_' .$matches[1]);

                if ( ! class_exists($class_name))
                {
                    continue;
                }

                $bundle_script      = new $class_name();
                $bundle_scripts[]   = array(
                    'description'   => $bundle_script->description,
                    'name'          => $bundle_script->name,
                    'script'        => $matches[1]
                );
            }
            
            if ($bundle_scripts && $has_bundle_info)
            {
                $admin_scripts[] = array(
                    'bundle'        => $bundle,
                    'description'   => $bundle_info['description'],
                    'name'          => $bundle_info['name'],
                    'scripts'       => $bundle_scripts
                );
            }
        }
        
        return $admin_scripts;
    }
    
    
    /**
     * Returns the version number of the installed module. If the module is
     * not installed, returns an empty string.
     *
     * @access  public
     * @return  string
     */
    public function get_installed_module_version()
    {
        global $DB;

        $package_name   = ucfirst($this->get_package_name());
        $db_module      = $DB->query("SELECT module_version
            FROM exp_modules
            WHERE module_name = '{$package_name}'
            LIMIT 1"
        );

        return $db_module->num_rows
            ? $db_module->row['module_version']
            : '';
    }


    /**
     * Returns the package name.
     *
     * @access  public
     * @return  string
     */
    public function get_package_name()
    {
        return $this->_package_name;
    }
    
    
    /**
     * Returns the package version.
     *
     * @access  public
     * @return  string
     */
    public function get_package_version()
    {
        return $this->_package_version;
    }


    /**
     * Returns the site ID.
     *
     * @access  public
     * @return  int
     */
    public function get_site_id()
    {
        global $PREFS;
        
        if ( ! $this->_site_id)
        {
            $this->_site_id = (int) $PREFS->ini('site_id');
        }
        
        return $this->_site_id;
    }


    /**
     * Returns an array of status messages.
     *
     * @access  public
     * @return  array
     */
    public function get_status_messages()
    {
        return $this->_status_messages;
    }
    
    
    /**
     * Returns the `theme` folder URL.
     *
     * @access  public
     * @return  string
     */
    public function get_theme_url()
    {
        global $PREFS;
        
        if ( ! $this->_theme_folder_url)
        {
            $this->_theme_folder_url = $PREFS->ini('theme_folder_url');
            $this->_theme_folder_url .= substr($this->_theme_folder_url, -1) == '/'
                ? 'cp_themes/default/'
                : '/cp_themes/default/';
                
            $this->_theme_folder_url .= $this->get_package_name() .'/';
        }
        
        return $this->_theme_folder_url;
    }


    /**
     * Installs the module.
     *
     * @access  public
     * @return  bool
     */
    public function install_module()
    {
        $this->install_module_register();
        return TRUE;
    }
    
    
    /**
     * Registers the module, as part of the installation process.
     *
     * @access  public
     * @return  void
     */
    public function install_module_register()
    {
        global $DB;
        
        $package_name = ucfirst($this->get_package_name());
        $DB->query($DB->insert_string(
            'exp_modules',
            array(
                'has_cp_backend'    => 'y',
                'module_id'         => '',
                'module_name'       => $package_name,
                'module_version'    => $this->get_package_version()
            )
        ));
    }


    /**
     * Runs the specified bundle script.
     *
     * @access  public
     * @param   string        $bundle        The bundle name.
     * @param   string        $script        The script name.
     * @return  bool
     */
    public function run_script($bundle, $script)
    {
        $script_file = PATH
            .'admin_scripts' .DIRECTORY_SEPARATOR
            .$bundle .DIRECTORY_SEPARATOR
            .'script.' .$script .EXT;

        if ( ! is_file($script_file))
        {
            return FALSE;
        }

        include_once $script_file;
        $script_class = ucfirst($bundle .'_' .$script);

        if ( ! class_exists($script_class))
        {
            return FALSE;
        }

        $subject = new $script_class();

        if ( ! method_exists($subject, 'run_script'))
        {
            return FALSE;
        }

        return $subject->run_script();
    }


    /**
     * Uninstalls the module.
     *
     * @access  public
     * @return  bool
     */
    public function uninstall_module()
    {
        global $DB;
        
        $package_name = ucfirst($this->get_package_name());
        
        $db_module = $DB->query("SELECT module_id
            FROM exp_modules
            WHERE module_name = '{$package_name}'
            LIMIT 1");
        
        if ( ! $db_module->num_rows)
        {
            return FALSE;
        }
        
        $DB->query("DELETE FROM exp_module_member_groups WHERE module_id = '{$db_module->row['module_id']}'");
        $DB->query("DELETE FROM exp_modules WHERE module_name = '{$package_name}'");
        
        return TRUE;
    }


    /**
     * Updates the module.
     *
     * @access  public
     * @param   string      $installed_version      The installed version.
     * @param   string      $package_version        The package version.
     * @return  bool
     */
    public function update_module($installed_version = '', $package_version = '')
    {
        global $DB;

        $package_name = ucfirst($this->get_package_name());
        
        if ( ! $installed_version OR ! $package_version
            OR version_compare($installed_version, $package_version, '>='))
        {
            return FALSE;
        }
        
        // Update the module version number.
        $DB->query($DB->update_string(
            'exp_modules',
            array('module_version' => $package_version),
            "module_name = '{$package_name}'"
        ));

        return TRUE;
    }



    /* --------------------------------------------------------------
     * PRIVATE METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Returns the contents of a directory.
     *
     * @access  private
     * @param   string      $dir_path       The directory to examine.
     * @param   string      $item_type      The item type to return ('DIRECTORY', or 'FILE').
     * @return  void
     */
    private function _get_directory_contents($dir_path = '', $item_type = 'DIRECTORY')
    {
        $return = array();
        $item_type = strtoupper($item_type);
        
        if ($dir_handle = @opendir($dir_path))
        {
            $dir_path = rtrim(realpath($dir_path), DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR;
            
            while (($dir_item = readdir($dir_handle)) !== FALSE)
            {
                // Ignore any hidden files or directories.
                if (substr($dir_item, 0, 1) == '.')
                {
                    continue;
                }
                
                switch ($item_type)
                {
                    case 'DIRECTORY':
                        if (is_dir($dir_path .$dir_item))
                        {
                            $return[] = $dir_item;
                        }
                        break;
                        
                    case 'FILE':
                        if (is_file($dir_path .$dir_item))
                        {
                            $return[] = $dir_item;
                        }
                        break;
                        
                    default:
                        continue;
                        break;
                }
            }
        }
        
        return $return;
    }
    

}


/* End of file      : admin_scripts_model.php */
/* File location    : /system/modules/admin_scripts/models/admin_scripts_model.php */
