<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="initial-scale=1.0">    <!-- So that mobile webkit will display zoomed in -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->

    <title><?php if (!empty($vars['title'])) echo $vars['title']; ?></title>
    <style type="text/css">

        /* Resets: see reset.css for details */
        .ReadMsgBody { width: 100%; background-color: #ebebeb;}
        .ExternalClass {width: 100%; background-color: #ebebeb;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table {border-spacing:0;}
        table td {border-collapse:collapse;}
        .yshortcuts a {border-bottom: none !important;}
        span.preheader { display: none !important; }

        /* Constrain email width for small screens */
        @media screen and (max-width: 600px) {
            table[class="container"] {
                width: 95% !important;
            }
        }

        /* Give content more room on mobile */
        @media screen and (max-width: 480px) {
            td[class="container-padding"] {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
        }

    </style>
</head>
<body style="margin:0; padding:10px 0;" bgcolor="#ebebeb" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><?php
if (!empty($vars['preheader'])) {
    ?><span class="preheader"><?php echo $vars['preheader']; ?></span><?php
}
?>
<br>

<!-- 100% wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#ebebeb">
    <tr>
        <td align="center" valign="top" bgcolor="#ebebeb" style="background-color: #ebebeb; padding-top: 3em; padding-bottom: 3em;">

            <!-- 600px container (white background) -->
            <table border="0" width="600" cellpadding="0" cellspacing="0" class="container" bgcolor="#ffffff">
                <tr>
                    <td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 16px; line-height: 20px; font-family: Helvetica, sans-serif; color: #666;">
                        <br>

                        <!-- ### BEGIN CONTENT ### -->

                        <?php echo $vars['body']?>

                        <!-- ### END CONTENT ### -->

                        <hr style="border-top: 1px solid #cccccc;">

                        <?php
                            $path = parse_url(\Idno\Core\Idno::site()->request()->getUri(), PHP_URL_PATH);
                            $path = str_replace('/', '_', $path);
                        ?>

                        <p style="text-align: center; margin-top: 15px;">
                        <em style="font-style:italic; font-size: 12px; color: #aaa; text-decoration: center;"><?php echo \Idno\Core\Idno::site()->language()->_('Powered by') ?> <a href="https://withknown.com/?utm_source=transactional&utm_medium=email&utm_campaign=e<?php echo $path?>" style="color: #4c93cb; text-decoration: none;">Known</a>.</em>
                        </p>
                        <br><br>

                    </td>
                </tr>
            </table>
            <!--/600px container -->

        </td>
    </tr>
    <tr>
        <td align="center" valign="top" bgcolor="#ebebeb" style="background-color: #ebebeb; padding-bottom: 3em;">
            <p style="color: #999999; font-size: 12px;">Known, Inc. 421 Bryant St, San Francisco, CA, 94107</p>
        </td>
    </tr>    
</table>
<!--/100% wrapper-->
<br>
<br>
</body>
</html>

