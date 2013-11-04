
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

    function bindControls() {
        $('.acl-option').click(function() {
            $('#access-control-id').val($(this).attr('data-acl'));
            $('#acl-text').html($(this).html());
        });
    }

    function contentCreateForm(plugin) {
        /*if (window.contentCreateType == plugin) {
            $('#contentCreate').slideDown(400);
            $('#contentTypeButtonBar').slideUp(400);
        } else {*/
            $.ajax('/' + plugin + '/edit/', {
                dataType: 'html',
                success: function(data) {
                    $('#contentCreate').html(data).slideDown(400);
                    $('#contentTypeButtonBar').slideUp(400);
                    window.contentCreateType = plugin;
                    window.contentPage = true;
                    if (jQuery){
                        //$('form').sisyphus();
                    }
                    
                    bindControls();
                },
                error: function(error) {
                    $('#contentTypeButtonBar').slideDown(400);
                }

            });
        //}
    }

    function hideContentCreateForm() {
        if (window.contentPage == true) {
            $('#contentTypeButtonBar').slideDown(200);
            $('#contentCreate').slideUp(200);
        } else {
            if (window.history.length > 1) {
                window.history.go(-1);
            } else {
                window.close();
            }
        }
    }