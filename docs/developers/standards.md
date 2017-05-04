# Coding standards

## Source style

We have a few core rules for legibility:

* We favor *spaces over tabs*. Please set your IDE to four spaces per tab.
* All method names should use camelCase; all variable names should use snake_case.
* American spelling is preferred for method and variable names. eg, "color" is better than "colour" and "sanitize" is better than "sanitise".
* Use PHP doc blocks for every method, *except* inherited methods, where they are optional. Use your best judgment here.
* No short "if" statements: for clarity, please always use { } brackets.
* Lay your code out as beautifully as you can. We're in favor of beautifiers!

Regarding the language in PHP doc blocks:

* Explain the purpose of the method or class as clearly as you can. For example, "loads ClassName by property_name" is bad; "loads objects that have a property called property_name" is much better.
* "Just read the code" isn't a good idea, and we don't like it.
* That said, clear, simple code is good :)

!!! note "Note"
    Notice errors are bad, please develop your code with ```pedantic_mode = true;``` set in your config.ini