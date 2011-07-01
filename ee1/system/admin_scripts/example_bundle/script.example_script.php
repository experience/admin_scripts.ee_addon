<?php

/**
 * Example script. Note that the class name *must* be namespaced to the bundle.
 *
 * @author              Stephen Lewis (http://github.com/experience/)
 * @package             Admin_scripts
 */

class Example_bundle_example_script {

    public $name;
    public $description;


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
        $this->name         = 'Example Script';
        $this->description  = 'This is an example script.';
    }


    /**
     * Runs the admin script.
     *
     * @access  public
     * @return  bool
     */
    public function run_script()
    {
        return TRUE;
    }


}


/* End of file          : script.example_script.php */
/* File location        : /system/admin_scripts/example_bundle/script.example_script.php */
