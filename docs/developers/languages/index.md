# Translating Known

Known has a mechanism for translating strings used into other languages. When Known boots, 
it creates a new Language() object on the Idno object for the current language, which is addressable 
by ```\Idno\Core\Idno::site()->language();```.

Your code/plugin can add strings to this object for later use, usually by registering them on the ```registerTranslations()``` 
method hook.

## Adding a translation for a language

It is possible to add single strings, one by one, for the current language, however the easiest way to register multiple strings is to extend ```Idno/Core/Translation``` for each language you want to translate, and then implement its ```getStrings()``` method.

It is then possible to add them all at once for each language (this way, Known will automatically select the appropriate translation for the loaded language).

E.g.

```
\Idno\Core\Idno::site()->language()->register(new \IdnoPlugins\Example\Languages\English('en'));
\Idno\Core\Idno::site()->language()->register(new \IdnoPlugins\Example\Languages\French('fr'));
```

## Using a translation

Once a string has been registered, it is possible to echo the string, and have it translated:

E.g.

```
echo \Idno\Core\Idno::site()->language()->_('This is the string to translate');
```
