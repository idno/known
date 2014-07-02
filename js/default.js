/**
 Helper Javascript functions for Known

 If you need to add your own JavaScript, the best thing to do is to create your own js files
 and reference them from a custom plugin or template.

 @package idno
 @subpackage core
 */

/**
 *** Content creation
 */

function bindControls() {
    $('.acl-option').click(function () {
        $('#access-control-id').val($(this).attr('data-acl'));
        $('#acl-text').html($(this).html());
    });
}

function contentCreateForm(plugin) {
    $.ajax(wwwroot() + plugin + '/edit/', {
        dataType: 'html',
        success: function (data) {
            $('#contentCreate').html(data).slideDown(400);
            $('#contentTypeButtonBar').slideUp(400);
            window.contentCreateType = plugin;
            window.contentPage = true;

            bindControls();
        },
        error: function (error) {
            $('#contentTypeButtonBar').slideDown(400);
        }

    });
}

function hideContentCreateForm() {
    if (window.contentPage == true) {
        $('#contentTypeButtonBar').slideDown(200);
        $('#contentCreate').slideUp(200);
    } else {
        window.close(); // Will only fire for child windows
        if (window.history.length > 1) {
            window.history.back();
        }
    }
}

function autoSave(context, elements) {
    var previousVal = [];
    setInterval(function () {
        var changed = {};
        for (element in elements) {
            if ($("#" + elements[element]).val() != previousVal[elements[element]]) {
                val = $("#" + elements[element]).val();
                console.log(elements[element] + " has changed to " + val);
                changed[elements[element]] = val;
                previousVal[elements[element]] = val;
            }
        }
        if (Object.keys(changed).length > 0) {
            $.post(wwwroot() + 'autosave/',
                {
                    "context": context,
                    "elements": changed,
                    "names": elements
                },
                function() {
                }
            );
        }
    }, 10000);
}