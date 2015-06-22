<img src="images/logo/logo_347x50_PPa11y.png" alt="PayPal accessibility logo">
# Bootstrap Accessibility Plugin, v1.0
## by the PayPal Accessibility Team
See the [Authors](#authors) section below for more information.

## What is it?
This plugin adds accessibility mark-up to the [default components of Bootstrap 3](http://getbootstrap.com/javascript/) to make them accessible for keyboard and screen reader users. Do not worry, the plugin does not affect the performance or the visual layout of your website. Let the magic remain magic!

## Why do I want it?
If you use Bootstrap library (version 3.x) on your website, your pages will now be much more usable and navigable for and by keyboard and screen reader users with no work on your part. Believe us, for this they will thank you! Read on to learn about all the enhancements introduced by this plugin.

## How do I get it on my website?
1. Download and include Bootstrap.js from [getbootstrap.com](http://getbootstrap.com/).
2. Download and include the [bootstrap accessibility plugin js](plugins/js).
3. Download and include the [bootstrap accessibility plugin css](plugins/css) to override css styles.
4. Optional: Lazily load the JavaScript plugin after the page is loaded ([example](demo.html)).
5. For basic implementation:

  ```html
   <link rel="stylesheet" href="/css/bootstrap.min.css">
   <link rel="stylesheet" href="/css/bootstrap-accessibility.css">

   <script src="http://code.jquery.com/jquery.js"></script>
   <script src="/js/bootstrap.min.js"></script>
   <script src="/js/bootstrap-accessibility.min.js"></script>
  ```
6. You can also install it from npm or bower:

  ```sh
   bower install bootstrapaccessibilityplugin
   npm install bootstrap-accessibility-plugin
  ```

## Which components become accessible?
- Alert
- Tooltip
- Popover
- Modal dialog
- Dropdown menu
- Tab panel
- Collapse
- Carousel

## Plugin Live Demo
Feel free to play with the [live demo](https://paypal.github.io/bootstrap-accessibility-plugin/demo.html) of the components listed above and the Bootstrap Accessibility Plugin in action. Seeing how "accessified" widgets work in this demo will help you verify whether the plugin is installed correctly on your website.

## Details

### Alert
1. Add role of Alert to Alert, Warning, and Success Bootstrap Messages.
2. Increase the color contrast. The foreground to background color contrast ratio for the message was too low.
3. Add instructions in message dialog, so that the developer using the alert knows to manage keyboard focus on alert dismissal.
4. Close button now accessible to screen readers.

### Tooltip
1. Add role of Tooltip to tooltip div.
2. Generate a random id, assign it to the tooltip div, and reference it from the Tooltip element with the ARIA attribute "aria-describedby".
3. Remove aria-describedby when the tooltip is hidden.

### Popover 
1. Add role of Tooltip to popover div.
2. Generate a random id, assign it to Popover div, and reference it from the Tooltip element with the ARIA attribute "aria-describedby".
3. Remove aria-describedby when the popover is dismissed.

### Modal Dialog
1. Add role of Document to content div inside dialog, so that NVDA can force document mode and read contents inside Dialog.
2. When the Modal is closed, return the focus to the element which opened the dialog.
3. Change the focus outline of close button to visible.
4. Close button now accessible to screen readers.

### Dropdown 
1. Add aria-haspopup and and aria-expanded attributes to dropdown toggle link.
2. Dynamically change aria-expanded when the dropdown closes or opens.
3. Focus to first item on activating dropdown.
4. Add ability to open dropdown with spacebar.
5. Close dropdown when tabbing out from dropdown.
6. Change the focus outline of dropdown to visible.

### Tab Panel
1. Add ARIA roles like tablist, presentation, and tab for tabs UL, LI.
2. Add tabIndex, aria-expanded, aria-selected, aria-controls for tab.
3. Add ARIA roles of tabPanel, tabIndex, aria-hidden, and aria-labelledBy for tabPanel.
4. Add keydown event listener for the tab to work with keyboard.
5. Dynamically flip tabIndex, aria-selected, and aria-expanded for tab when it is activated and add aria-hidden to hide the previously visible tab.

### Collapse 
1. Add tab role, aria-selected, aria-expanded, aria-controls, and tabIndex for collapse tab.
2. Add ARIA roles of tabPanel, tabIndex, aria-hidden, and aria-labelledBy for collapsible panel.
3. Add role of tabList and aria-multiselectable for collapse container div.
4. Dynamically flip tabIndex, aria-selected, and aria-expanded for tab when it is activated and add aria-hidden to hide the previously visible collapse tabpanel.
5. Add keydown event listener for the collapse component to work with keyboard.

### Carousel
1. Prevent automatic cycling of the carousel.
2. Prevent wrapping to first item on next button navigation or wrapping to last item on previous button navigation.
3. Add role of listbox for carousel div.
4. Add ARIA role of option, aria-selected, and tabIndex for individual carousel items.
5. Add role of button for previous and next anchor links and a hidden screen reader text of "Previous" and "Next".
6. Add keydown event listener for the carousel to work with keyboard.
7. Dynamically change tabIndex and aria-selected property of active and inactive tabs.
8. Remove display:none and hide (offscreen) of the inactive carousel items, so that screen readers can count the total number of carousel items.

## Re-compiling
You may want to extend the plugin further or change some of the code. Here is how to do it:

1. Get NodeJS from [http://nodejs.org](http://nodejs.org)
2. Clone the latest code from [https://github.com/paypal/bootstrap-accessibility-plugin.git](https://github.com/paypal/bootstrap-accessibility-plugin.git)
3. Go to the root of this project and install Compass and Sass:

  ```sh
   cd bootstrap-accessibility-plugin
   sudo gem install compass
  ```
4. Install and run grunt:

  ```sh
   sudo npm install grunt-cli -g
   npm install
   grunt
  ```
5. To run the examples, initialize the git submodules:
 
  ```sh
  git submodule init
  git submodule update
  ```

## Feedback and Contributions
Please do not hesitate to open an issue or send a pull request if something doesn't work or you have ideas for improvement. For instructions on how to contribute to this project please read the [contribution guide](CONTRIBUTING.md).

## Authors

 - Prem Nawaz Khan, primary developer || [https://github.com/mpnkhan](https://github.com/mpnkhan) || [@mpnkhan](https://twitter.com/mpnkhan)
 - Victor Tsaran, project manager, user interaction, testing, documentation || [https://github.com/vick08](https://github.com/vick08) || [@vick08](https://twitter.com/vick08)
 - Dennis Lembree, developer, user interaction, testing || [https://github.com/weboverhauls](https://github.com/weboverhauls) || [@dennisl](https://twitter.com/dennisl)
 - Srinivasu Chakravarthula, user interaction, testing || [@csrinivasu](https://twitter.com/csrinivasu)
 - Cathy O'Connor, design || [@cagocon](https://twitter.com/cagocon)

## Related Resources

 -  [Bootstrap a11y theme](https://github.com/bassjobsen/bootstrap-a11y-theme) - makes web accessibility easier for Bootstrap developers, a pure LESS/CSS solution.

## Copyright and License

Copyright 2015, eBay Software Foundation under [the BSD license](LICENSE.md).
