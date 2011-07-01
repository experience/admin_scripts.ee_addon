<?php

/**
 * Control panel status message.
 *
 * @package		EI
 * @author		Stephen Lewis
 */

if ( ! class_exists('EI_status_message'))
{
    class EI_status_message {
        
        /* --------------------------------------------------------------
         * CONSTANTS
         * ------------------------------------------------------------ */
        
        /**
         * Status message types.
         *
         * @access	public
         * @var		string
         */
        const ERROR		= 'error';
        const INFO		= 'information';
        const WARNING	= 'warning';
        
        
        
        /* --------------------------------------------------------------
         * PRIVATE PROPERTIES
         * ------------------------------------------------------------ */
        
        /**
         * Message.
         *
         * @access	private
         * @var		string
         */
        private $_message;
        
        /**
         * Type.
         *
         * @access	private
         * @var		string
         */
        private $_type;
        
        
        
        /* --------------------------------------------------------------
         * PUBLIC METHODS
         * ------------------------------------------------------------ */
        
        /**
         * Constructor.
         *
         * @access	public
         * @param	array	$properties		Associative array of properties.
         * @return	void
         */
        public function __construct(Array $properties = array())
        {
            foreach ($properties AS $key => $value)
            {
                $method_name = 'set_' .$key;
                if (method_exists($this, $method_name))
                {
                    $this->$method_name($value);
                }
            }
        }
        
        
        /**
         * Returns the message.
         *
         * @access	public
         * @return	string
         */
        public function get_message()
        {
            return $this->_message;
        }
        
        
        /**
         * Returns the type.
         *
         * @access	public
         * @return	string
         */
        public function get_type()
        {
            return $this->_type;
        }
        
        
        /**
         * Sets the message.
         *
         * @access	public
         * @param 	string		$message		The message.
         * @return	string
         */
        public function set_message($message)
        {
            if (is_string($message))
            {
                $this->_message = $message;
            }
            
            return $this->get_message();
        }
        
        
        /**
         * Sets the type.
         *
         * @access	public
         * @param 	string		$type		The type.
         * @return	string
         */
        public function set_type($type)
        {
            if ($this->_is_valid_message_type($type))
            {
                $this->_type = $type;
            }
            
            return $this->get_type();
        }
        
        
        /**
         * Converts the instance to an array.
         *
         * @access	public
         * @return	array
         */
        public function to_array()
        {
            return array(
                'message'	=> $this->get_message(),
                'type'		=> $this->get_type()
            );
        }
        
        
        /* --------------------------------------------------------------
         * PRIVATE METHODS
         * ------------------------------------------------------------ */
        
        /**
         * Checks whether the supplied argument is a valid message type.
         *
         * @access	private
         * @param	string		$type		The message type to check.
         * @return	bool
         */
        private function _is_valid_message_type($type)
        {
            return in_array($type, array(
                self::ERROR,
                self::INFO,
                self::WARNING
            ));
        }
        
    }
}

/* End of file		: EI_status_message.php */
/* File location	: system/modules/cartthrobber/classes/EI_status_message.php */
