/**
 * Control panel JavaScript.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @package         Admin_scripts
 */

(function($) {
    
    /**
     * Initialises the status messages.
     *
     * @return  void
     */
    function iniStatusMessages() {
        setTimeout(function() {
            $('.status_messages').fadeTo('slow', 0.1, function() {
                $(this).slideUp('slow');
            });
        }, 3500);
    }
    
    // Run on document ready.
    $('document').ready(function() {
        iniStatusMessages();
    });
    
})(window.jQuery);

/* End of file      : cp.js */
/* File location    : /themes/cp_themes/default/admin_scripts/js/cp.js */
