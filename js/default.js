
/**
    Helper Javascript functions for idno

    If you need to add your own JavaScript, the best thing to do is to create your own js files
    and reference them from a custom plugin or template.

    @package idno
    @subpackage core
*/

/**
*** Content creation
 */

    function contentCreateForm(plugin) {
        $('#contentTypeButtonBar').fadeOut(200);
        if (window.contentCreateType == plugin) {
            $('#contentCreate').show(200);
        } else {
            $.ajax('/' + plugin + '/edit/', {
                dataType: 'html',
                success: function(data) {
                    $('#contentCreate').html(data);
                    $('#contentCreate').show(200);
                    window.contentCreateType = plugin;
                },
                error: function(error) {
                    $('#contentTypeButtonBar').fadeIn(200);
                }

            });
        }
    }

    function hideContentCreateForm() {
        $('#contentCreate').hide(200);
        $('#contentTypeButtonBar').fadeIn(200);
    }