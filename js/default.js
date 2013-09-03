
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


    
    function bindContentCreateForm() { 
        
        // Bind POSSE Capture submit
        $('#contentCreate input.btn-primary').click(function(){
            
            $('span.posseButton a.label-info').each(function() { 
                $('#contentCreate form').append("<input type=\"hidden\" name=\"posseMethod[]\" value=\"" + $(this).attr('data-posse') + "\" />").submit();
            });
            
            return false;
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
                    $('#contentCreate').html(data);
                    $('#contentCreateWrapper').slideDown(400);
                    $('#contentTypeButtonBar').slideUp(400);
                    window.contentCreateType = plugin;
                    window.contentPage = true;
                    
                    // Commenting the following out since it seems to break EVERYTHING
                    //if (jQuery){
                    //    $('form').sisyphus();
                    //}
                    
                    bindContentCreateForm();
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
            $('#contentCreateWrapper').slideUp(200);
        } else {
            if (window.history.length > 1) {
                window.history.go(-1);
            } else {
                window.close();
            }
        }
    }
    