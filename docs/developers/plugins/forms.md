# Form input

Form submissions and API calls in Known are both handled by pages. In effect, every page is also an API handler.

* Prerequisites: [you should read about page handling in Known.](pages.md)

## Browser-based form submission

All forms must uniquely sign their data before they will be accepted. This is to prevent a number of attacks that could
compromise a user's security.

Each form signing method uses a method of `\Idno\Core\Idno::site()->actions()`. The following methods should be used within
your templates that take user input.

### From within an HTML form

All HTML form submissions must take place over POST requests. Additionally, the HTML form must sign their requests with
hidden action token fields.

To do this, simply include form signing code with a unique, relative URL for your action somewhere within the form body.

    <?= \Idno\Core\Idno::site()->actions()->signForm('your/form/url') ?>

The `postContent()` method in [the page handler class](pages.md) will seamlessly validate this token.

An example of a complete form might be:

    <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>myplugin/custompage/" method="post">
        <p>
            <label>
                A URL:
                <input type="url" name="my_url" placeholder="Type your URL here" value="">
            </label>
        </p>
        <p>
            <?= \Idno\Core\Idno::site()->actions()->signForm('myplugin/custompage') ?>
            <input type="submit" value="Post the form">
        </p>
    </form>

### From within a link

You can create a link to submit a properly-signed POST request using
`\Idno\Core\Idno::site()->actions()->createLink($url, $label, $data_to_include, $extra_configuration)`.

For example, the code for the logout button is as follows:

    <?= \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/logout',
                                                 'Sign out')); ?>

`$data_to_include` is an optional array of key => value pairs that will be sent with the POST request.
`$extra_configuration` is an optional array of options that help Known decide how to style the link. These include:

* `class`: a CSS class to apply
* `confirm`: if true, will prompt the user to confirm their action before submitting the POST request
* `confirm-text`: a custom text label for use with `confirm`

## API form submission

* See [making and accepting API calls](api.md)

## Accepting input from HTML forms, links or API calls

See [accepting POST requests](pages.md#handling-page-loads) to understand how to add a POST request handler in your
page handler class using `$page->postContent()`.

First, let's reproduce our form from above, which sends the contents of a URL field with name `my_url` to a page at
`/myplugin/custompage/`:

    <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>myplugin/custompage/" method="post">
        <p>
            <label>
                A URL:
                <input type="url" name="my_url" placeholder="Type your URL here" value="">
            </label>
        </p>
        <p>
            <?= \Idno\Core\Idno::site()->actions()->signForm('myplugin/custompage') ?>
            <input type="submit" value="Post the form">
        </p>
    </form>

Next, let's make sure that our [plugin class](class.md) in `/IdnoPlugins/MyPlugin/Main.php` handles that page
appropriately:

    namespace \IdnoPlugins\MyPlugin {
        function registerPages() {
            \Idno\Core\Idno::site()->addPageHandler('/myplugin/custompage/?', '\IdnoPlugins\MyPlugin\Pages\CustomPage');
        }
    }

Now, we need to make sure our page handler, which sits in `/IdnoPlugins/MyPlugin/Pages/CustomPage.php`, handles the form
input appropriately.

First, we set up [the page class](pages.md):

    namespace \IdnoPlugins\MyPlugin\Pages {
        class CustomPage extends \Idno\Common\Page {
            function getContent() { /* We won't use this for now */ }
            function postContent() {

            }
        }
    }

In our form example above, we had a single input field, with name `my_url`. To accept this within `postContent()`, we
simply need to call `$this->getInput('my_url');`.

For example, to output the contents of the field to the screen:

    namespace \IdnoPlugins\MyPlugin\Pages {
        class CustomPage extends \Idno\Common\Page {
            function getContent() { /* We won't use this for now */ }
            function postContent()
            {
                $url = $this->getInput('my_url');
                echo $url;
                exit;
            }
        }
    }

We could also send the `my_url` variable to the page handler in a link:

<?= \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'myplugin/custompage',
                                                 'This link sends a pre-defined value to the page',
                                                 array('my_url' => 'http://some/predefined/url'))); ?>

The page handler doesn't need to be modified in any way.

This page handler _also_ works for [handling API calls](api.md).