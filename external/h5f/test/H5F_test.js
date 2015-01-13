(function() {
  var form = document.getElementById("qunit-fixture"),
    formElem = document.getElementById("other"),
    disabledElem = document.getElementById("disabled"),
    email = document.getElementById("email"),
    url = document.getElementById("url"),
    postcode = document.getElementById("postcode"),
    chkbox = document.getElementById("chkbox"),
    nickname = document.getElementById("nickname"),
    radioFemale = document.getElementById("female"),
    radioMale = document.getElementById("male");
  
  test("H5F global", function() {
    ok(window.H5F, 'H5F global exists');
  });

  module("Validity API");

  test("Form element js attributes", function() {
    ok( formElem.validity, "validity attribute on form element exists" );
    
    ok( !formElem.validity.customError, "customError attribute exists" );
    ok( !formElem.validity.patternMismatch, "patternMismatch attribute exists" );
    ok( !formElem.validity.rangeOverflow, "rangeOverflow attribute exists" );
    ok( !formElem.validity.rangeUnderflow, "rangeUnderflow attribute exists" );
    ok( !formElem.validity.stepMismatch, "stepMismatch attribute exists" );
    ok( formElem.validity.valid, "valid attribute exists" );
    ok( !formElem.validity.valueMissing, "valueMissing attribute exists" );
  });

  module("Checkboxes/radios");

  // Trigger form validation
  form.checkValidity();

  test("have correct properties", function() {
    ok( chkbox.validity, "Checkbox has validity property" );
    chkbox.checkValidity();
    equal( chkbox.validity.valid, false, "Checkbox is currently invalid" );
  });

  test("checked vs unchecked state", function(){
    equal(chkbox.className, "required", "Checkbox gets class name of 'required' applied on form validation");

    // Check the checkbox
    chkbox.checked = true;
    chkbox.checkValidity();
    ok( chkbox.validity.valid, "Checkbox is valid" );
  });

  test("radio buttons  have correct properties", function() {
    ok( radioFemale.validity, "Female Radio button has validity propery");
    radioFemale.checkValidity();
    equal( radioFemale.validity.valid, false, "Female RadioButton is currently invalid ");
    ok( radioMale.validity, "Male Radio button has validity propery");
    radioMale.checkValidity();
    equal( radioMale.validity.valid, false, "Female RadioButton is currently invalid ");
  });
  test("check validity of radio buttons if one option is checked", function(){
    equal(radioFemale.className, "required", "Female Radio button gets the class name of 'require' applied on form validation");

    // Check male validity if female is checked
    radioFemale.checked = true;
    radioFemale.checkValidity();
    ok( radioFemale.validity, "Female Radio button is valid" );
    radioMale.checkValidity();
    ok( radioMale.validity, "Male Radio button is valid when Female is checked" );
  });

  module("Form validity");
  
  test("checkValidity method", function() {
    ok( form.checkValidity, "checkValidity method exists on parent form" );
    
    ok( formElem.checkValidity, "checkValidity method exists on element" );
  });

  test("disabled element", function() {
    equal( disabledElem.checkValidity(), true, "Disabled element should be exempt from validation" );
    ok( disabledElem.disabled, "Disabled element should return true on disabled property" );
    ok( disabledElem.validity.valid, "Disabled element should return true on disabled property even though it's invalid" );
    ok( !disabledElem.validity.valueMissing, "Disabled element should be false on it's actual error if it weren't disabled" );
  });
  
  module("Custom validation");
  
  function testCustomError(msg) {
    var ret;
    
    formElem.setCustomValidity(msg);
    ret = formElem.checkValidity();
    formElem.setCustomValidity("");
    return !!ret;
  }
  test("setCustomValidity and validationMessage", function() {
    ok( !formElem.validity.customError, "customError attribute is false" );
    equal( formElem.validationMessage, "", "validationMessage is empty" );
    ok( formElem.setCustomValidity, "setCustomValidity method exists" );
    equal( testCustomError("Not valid for some reason"), false, "Setting custom error message on field will always return false until the custom error is an empty string" );
  });
  
  module("Input type email and URL");
  
  function testEmail(address) {
    var ret;
    
    email.value = address;
    ret = email.checkValidity();
    email.value = "";
    
    return !!ret;
  }
  test("Email", function() {
    // A valid email varies between browsers FF4 and Opera: ry@an is valid, where as Chrome requires atleast ry@an.c
    equal( testEmail("notvalidemail"), false, "Setting email value to 'notvalidemail' is invalid" );
    equal( testEmail("ryan@awesome.com"), true, "Setting email value to h5f@awesome.com is valid" );
  });
  
  function testURL(address) {
    var ret;
    
    url.value = address;
    ret = url.checkValidity();
    url.value = "";
    
    return !!ret;
  }
  test("URL", function() {
    equal( testURL("example.com"), false, "Setting URL value to example.com is invalid" );
    equal( testURL("http://example.com"), true, "Setting URL value to http://example.com is valid" );
  });
  
  module("Field attributes");
  
  function testPattern(val) {
    var ret;
    
    nickname.value = val;
    ret = nickname.checkValidity();
    nickname.value = "";
    
    return !!ret;
  }
  test("pattern", function() {
    equal( testPattern("ry"), false, "Nickname field has pattern that requires atleast 4 alphanumeric characters, only set two" );
    equal( testPattern("ryan"), true, "Set four characters on nickname field, will be valid" );
  });
  
  function testRange(val) {
    var ret;
    
    // FF4 will fail as min, max and step aren't supported, better support detection is coming!
    postcode.value = val;
    ret = postcode.checkValidity();
    postcode.value = "";
    
    return !!ret;
  }
  test("min, max and step", function() {
    equal( testRange("1000"), false, "Value of 1000 is below min attribute, 1001, and will be invalid" );
    equal( testRange("8001"), false, "Value of 8001 is above max attribute, 8000, and will be invalid" );
    equal( testRange("1002"), false, "Value is within range but does not increment by specified step attribute of 2" );
    
    equal( testRange("1003"), true, "Value is within range and adheres to the step attribute of incements of 2" );
  });
  
}());
