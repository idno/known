# Mention.js

<img src="http://i.imgur.com/pCvqL.png">

Lightweight (min: 1.92kb, full: 4.07kb) wrapper for adding @user mention functionality to Twitter Bootstraps Typeahead plugin.

This enables you to have Twitter-like user mentions in textareas

<b>View the demo <a href="http://jakiestfu.github.com/Mention.js/" target="_blank">here</a>.</b>

## Dependencies
* <a href="https://github.com/jquery/jquery" target="_blank">jQuery</a>
* <a href="https://github.com/twitter/bootstrap" target="_blank">Typeahead</a>

## Usage
The `users` parameter accepts an array of objects. Each object ***must*** have a username key in order for this script to work.

`````javascript
$("#multi-users").mention({
    delimiter: '@',
    users: [{
        username: "ashley"
    }, { 
        username: "roger"
    }, { 
        username: "frecklefart123"
    }]
});
`````

## Sensitivity
`````javascript
$("#multi-users").mention({
    sensitive: true,
    users: [{
        username: "sarah"
    }, { 
        username: "bigRat"
    }, { 
        username: "roger"
    }, { 
        username: "Ricky"
    }]
});
`````
With sensitivity set to true, items are ordered by the following divisions of priority:
* Highest: If first letter matches exactly
* High: If first letter matches regardless of case
* Med: If target has matching letters' case
* Low: if target has matching character regardless of case

### Sensitivity Examples:
If you were to query `"@r"`, with sensitivity on, the resulting list will be `["roger", "Ricky", "sarah", "bigRat"]`, but if you were to query `"@R"`, the resulting list would be `["Ricky", "roger", "bigRat", "sarah"]`


## Full Names, Images, and Query By
If you have a `name` and/or `image` key in one of your user objects, there name and image will appear as such in the dropdown list.

The `queryBy` parameter accepts an array of strings that represent keys in your user object that you would like to query against. For example, if you were to type in the `name` "@Scott", the script would match the `username` "@bigCat". `username` is required for this script to work.

`````javascript
$("#multi-users").mention({
    queryBy: ['name', 'username'],
    users: [{
        username: "sarah",
        name: "Sarah Jones",
        image: "http://placekitten.com/25/25"
    }, { 
        username: "bigCat",
        name: "Scott Pfaff",
        image: "http://placekitten.com/25/25"
    }, { 
        username: "coderDude",
        name: "Roger Penn",
        image: "http://placekitten.com/25/25"
    }]
});
`````

## Empty Querying
You may query for users simply by pressing your delimiter. For example, pressing the @ symbol will return all users that are a part of your users list, so long as those users adhere to the `typeaheadOpts.items` limit
```javascript
$('#multi-users').mention({
    emptyQuery: true,
    typeaheadOpts: {
        items: 10 // Max number of items you want to show
    },
    users: [...]
});
```


## Defaults
`````javascript
$("#multi-users").mention({
    users: [], // Array of Objects
    delimiter: '@', // Username Delimiter
    sensitive : true,
    queryBy: ['name', 'username'],
    typeaheadOpts: { // Settings for Typeahead
        matcher: _matcher, // Mention.js's custom matcher function, don't change
        updater: _updater, // Mention.js's custom updater function, don't change
        sorter: _sorter, // Mention.js's custom sorter function, don't change
    }
});
 
`````
 
### License

(The MIT license)

Copyright (c) 2013 Jacob Kelley

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
