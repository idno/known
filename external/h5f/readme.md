H5F [![Build Status](https://secure.travis-ci.org/ryanseddon/H5F.png?branch=master)](http://travis-ci.org/ryanseddon/H5F)
===

[![Selenium Test Status](https://saucelabs.com/browser-matrix/rseddon.svg)](https://saucelabs.com/u/rseddon)

### a JavaScript library that allows you to use the HTML5 Forms chapters new field input types, attributes and constraint validation API in non-supporting browsers.

The H5F script will detect if the browser has support for the HTML5 Forms Chapter and either hook into the native methods, attributes and events or emulate the new features in non-supporting browsers.

### What's supported

H5F offers support for most, but not all, of the HTML5 Forms Chapter:

* Input types: email and url
* Input attributes: pattern, placeholder, min, max, step, required
* Textarea attributes: placeholder and required
* Select attributes: required
* Form attributes: novalidate
* Submit attributes: formnovalidate

Also supported is the constraint validation API:

* Field validity object
* `checkValidity()` method on form or individual field
* `setCustomValidity()` method to set custom error message
* `validationMessage` attribute that returns the message set using `setCustomValidity()` method

### Example

```html
<form id="signup">
	<label>Email</label>
	<input type="email" placeholder="e.g. ryan@example.net" required />

	<label>Phone</label> 
	<input type="tel" id="tel" name="tel" pattern="\d{10}" />

	<label>Post code *</label>
	<input type="number" min="1001" max="8000" required />

	<input type="submit" />
</form>
```

On page load you run the H5F setup method:

```js
H5F.setup(document.getElementById("signup"));
```

For a working demo download the demo files.

#### Setting custom error message

With `setCustomValidity()` method you can set a custom error message on a field and it will be in an invalid state until the custom message is set back to an empty string.

```js
document.getElementById("other").setCustomValidity("Please enter some information");
```

This field will be in a permanent invalid state, we can return the custom error message by using the `validationMessage` attribute

```js
document.getElementById("other").validationMessage;
// "Please enter some information"
```
	
A good use case for this functionality is a password comparison field.

```js
var pass = document.getElementById("pass"),
	cpass = document.getElementById("cpass");
	
if(cpass.value !== pass.value) {
	cpass.setCustomValidity("Your password doesn't match");
} else {
	cpass.setCustomValidity("");
}
```

#### Passing multiple forms

You can pass an HTMLFormElement, HTMLCollection of HTMLFormElements, or Array of HTMLFormElements.

```js
H5F.setup([document.getElementById("form1"),document.getElementById("form2"),document.getElementById("form3")]);
```

#### Optional settings argument

The `H5F.setup` method also accepts a second optional argument so you can specify the fields validation class names:

```js
H5F.setup(document.getElementById("signup"), {
	validClass: "valid",
	invalidClass: "invalid",
	requiredClass: "required",
	placeholderClass: "placeholder"
});
```
	
#### Form submission blocking

HTML5 forms will block form submission until the form is valid this can be switched off by setting the `novalidate` attribute on the parent form element.

```html
<form id="signup" novalidate>
	<label>Email</label>
	<input type="email" placeholder="e.g. ryan@example.net" required />

	<input type="submit" />
</form>
```
	
The above form regardless of attributes set won't validate and will submit, default behaviour is to block form submission.

You can also specify the `formnovalidate` attribute on a submittable element e.g. input or button. The attribute will bypass any form validation but only if the button with the `formnovalidate` attribute is clicked.

```html
<form id="signup">
	<label>Email</label>
	<input type="email" placeholder="e.g. ryan@example.net" required />

	<input type="submit" value="Save" formnovalidate />
</form>
```
	
[http://thecssninja.com/javascript/H5F](http://thecssninja.com/javascript/H5F)

### License
Copyright (c) 2012 Ryan Seddon  
Licensed under the MIT license.
