# Templates in Known

Known splits templating into *templates* and *template types*.

## Templates

Templates are the styling information for each individual page element.

These have names of the form `foo`, or `foo/bar`, and may be split up with an indefinite number of forward-slashes. They are structured like relative folder paths; whatever works in a UNIX-style relative folder path, works in a Known template name.

By convention, Known uses lower-case template names.

Examples:

* `shell` is always the name of the page shell.
* `page/navigation` might be the name of an app navigation menu.
* `page/navigation/item` might be the name of an item in the aforementioned app navigation menu.

## Template types

The template type is the *kind* or *category* of template we're dealing with.

By default, Known assumes we're talking HTML5 (referred to internally as the `default` template type).

However, if you want to build an RSS feed, for example, your template type might be `rss`.

Each template type contains its own set of templates. However, templates always fall back to the "default" template type, so that if you try to draw, for example, `page/navigation` in the `rss` template type, and it doesn't exist, `page/navigation` in the `default` template type will be called instead (if it exists).

## Template locations

All template files are PHP scripts with the extension `.tpl.php`. These script filenames correspond to the template name, such that:

* `page/navigation` would sit in `templates/default/page/navigation.tpl.php` for the `default` template type
* `page/navigation` would sit in `templates/rss/page/navigation.tpl.php` for the `rss` template type
* `page/navigation` would sit in `templates/comicsans/page/navigation.tpl.php` for the `comicsans` template type (hey, you never know)

You can have multiple top-level `templates` folders; Known will search them in the order they were added using `Bon::additionalPath($path)`, falling back to the main Known folder at the end.

## Template variables

Variables are accessible in a template file through the `$vars` array. The `BonTemp` template object is accessible in template files through a variable called `$t`.

Examples:

* `<?=$vars['title']?>` echoes a passed variable called `title`.
* `<?=$t->draw('page/navigation')?>` inserts the `page/navigation` template using the same variables that this template was called with.

## Calling a template

Templates are called by accessing the current site template::

    $t = \Idno\Core\Idno::site()->template();

You can add variables to pass to the templates by simply adding them as properties to the template object::

    $t->title = 'Example title';
    $t->foo = $bar;

If you want to set a template type other than `default`, you call it using `setTemplateType($templateType)`::

    $t->setTemplateType('comicsans'); // See?

Finally, to draw a particular template, you call `$t->draw($templateName)`, which returns the rendered template as a string::

    $renderedContent = $t->draw('foo/bar');

Or to echo it directly::

    echo $t->draw('foo/bar');

The template object also contains a special function to draw the page. This echoes the `shell` template, and assumes you have (at a bare minimum) set the `title` and `body` variables::

    $t->drawPage();

## Example

For example, to display a stream of items stored in an array called `$items`, using a template called `example/items` you might call the templating engine as follows::

    $t = \Idno\Core\Idno::site()->template();
    $t->title = 'Stream';
    $t->items = $items;
    $t->body = $t->draw('example/items');
    $t->drawPage();

The `example/items` template file itself needs to be stored in `templates/default/example/items.tpl.php`, and might look something like this::

    <?php
        if (!empty($vars['items'])) {
            foreach($vars['items'] as $item) {
                echo $this->__(['item' => $item])->draw('example/item');
            }
        }

The template file could also contain raw HTML.