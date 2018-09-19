
## The basics

* American spelling is preferred for method and variable names. eg, "color" is better than "colour" and "sanitize" is better than "sanitise".
* Lay your code out as beautifully as you can. We're in favor of beautifiers!
* Legibility over cleverness please!

## Naming conventions

* Namespaces should be UpperCamelCase, with a matching /UpperCamelCase/ file path
* Classes should use UpperCamelCase, with a matching UpperCamelCase.php file.
* All method names should use lower camelCase 
* All variable names should use snake_case.

## Indentation

* We favor *spaces over tabs*. Please set your IDE to *four* spaces per tab.
* Multiline associative arrays, each item should appear on a new line, indented, e.g:

```
$var = [
    'example1' => 'value1',
    'example2' => 'value2',
];
```
!!! note "Note"
    Notice the final comma... adding this makes future updates easier, and less fragile, and so is encouraged.

* Switch and cases should also be embedded, with a space after each ```switch``` and ```case```, e.g.:
```
switch ($var) {
    case 'example1':
        some_function();
        break;
    case 'example2':
        another_function();
        break;
}
```
* In general, when in doubt, space it out.

## Error handling

* Notice errors are errors, please develop your code with ```pedantic_mode = true;``` set in your config.ini
* Unless there's a VERY good reason you can explain, never use the '@' error suppression operator.

## Comments

We like comments, and when in doubt, you probably should write a comment! That doesn't mean writing a comment for every line, but 
we'd like to know your thought process behind what you wrote, and why you did something a certain way. 

This will help future developers, maybe even you, so be kind to your future self!

### PHP doc blocks:

Use PHP doc blocks for every method, *except* inherited methods, where they are optional. Use your best judgment here.

* Explain the purpose of the method or class as clearly as you can. For example, "loads ClassName by property_name" is bad; 
  "loads objects that have a property called property_name" is much better.
* Explain what object properties are, and what they're for.
* "Just read the code" isn't a good idea, and we don't like it.
* That said, clear, simple code is good :)

## PHP Files

* Use Unix line endings
* End your file with a single blank line
* You should never end your .php file with ```?>```, as this leads to fragile code.

## PHP tags

* Open your PHP blocks with ```<?php```, and close with ```?>```
* Shorthand ```<?=``` is ok in templates
