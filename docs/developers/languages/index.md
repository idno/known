# Translating Known

Known has a mechanism for translating strings used into other languages. When Known boots, 
it creates a new Language() object on the Idno object for the current language, which is addressable 
by ```\Idno\Core\Idno::site()->language();```.

Your code/plugin can add strings to this object for later use, usually by registering them on the ```registerTranslations()``` 
method hook.

## Adding a translation for a language

In order to add a translation, you need to register a ```Translation``` object for a given language short code. To do this you need to extend ```Idno/Core/ArrayKeyTranslation``` for each language you want to translate, and then implement its ```getStrings()``` method.

It is then possible to add them all at once for each language (this way, Known will automatically select the appropriate translation for the loaded language).

E.g.

```
\Idno\Core\Idno::site()->language()->register(new \IdnoPlugins\Example\Languages\English('en_GB'));
\Idno\Core\Idno::site()->language()->register(new \IdnoPlugins\Example\Languages\French('fr_FR'));
```

## Using a translation

Once a string has been registered, it is possible to echo the string, and have it translated:

E.g.

```
echo \Idno\Core\Idno::site()->language()->_('This is the string to translate');
```

## Gettext support

Known also now supports [gettext](https://en.wikipedia.org/wiki/Gettext), which is a widely supported localisation platform.

### Creating .POT file

The first step, after you've used ```\Idno\Core\Idno::site()->language()->_()``` to write your strings, is to generate a POT template translation file. To do this, in ```/languages/``` there's a helpful script, go into this directory and run the script

```
./makepot.sh /path/to/your/plugin > /path/to/your/plugin/languages/language.pot
```

This will parse all your plugin's PHP files and extract translatable strings.

!!! note "Note"
    If you have added a new translation string to Known's core code or templates, you should use the Grunt ```build-lang``` task to update the ```known.pot``` file.


### Creating your translation

Open up your .POT file with a suitable tool, e.g. [poedit](https://poedit.net/), and save your .mo and .po files as ```/path/to/your/plugin/languages/*LOCALE*/LC_MESSAGES/*DOMAIN*.mo|po```, where:

* LOCALE is the locale you're writing for, e.g. pt_BR
* DOMAIN is the domain, e.g. your plugin name 'myplugin'

### Registering your translation

In your plugin, register your language by registering a new ```GetTextTranslation``` class, passing the path of your languages directory, and the domain you used.

So, for the above example this might look like:

```
function registerTranslations() 
{
    \Idno\Core\Idno::site()->language()->register(
        new \Idno\Core\GetTextTranslation(
            'myplugin',
            dirname(__FILE__) . '/languages/'
        )
    );   
}
```
