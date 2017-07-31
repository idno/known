# Fragmentions

Fragmentions are anchors to individual words or phrases in a document.

```html
<a href="##this+specific+text+">Find this specific text</a>
```

- [Google Chrome Extension](https://chrome.google.com/webstore/detail/fragmentions/pgajkeekgcmgglngchhmcmnkffnhihck)
- [Indie Web Camp Article](http://indiewebcamp.com/fragmention)
- [WordPress Plugin](https://christiaanconover.com/code/wp-fragmention)
- [Drupal Module](https://drupal.org/node/2247785)

## Usage

Fragmentions use **##** double-hash changes to match words or phrases in a document, jumping to their corresponding element. Matches are case sensitive and whitespace insensitive. Corresponding elements may be spans, paragraphs, headings, buttons, inputs, or any other container element.

In the following example, clicking **TL;DR** would jump to the `<strong>` element containing **Life, Liberty and the pursuit of Happiness**.

```html
<article>
	<p>
		<a href="##pursuit">TL;DR</a>
	</p>

	<p>
		When in the Course of human events, it becomes necessary for one people 
		to dissolve the political bands which have connected them with another, 
		and to assume among the powers of the earth, the separate and equal 
		station to which the Laws of Nature and of Nature’s God entitle them, a 
		decent respect to the opinions of mankind requires that they should 
		declare the causes which impel them to the separation.
	</p>

	<p>
		We hold these truths to be self-evident, that all men are created 
		equal, that they are endowed by their Creator with certain unalienable 
		Rights, that among these are <strong>Life, Liberty and the pursuit of 
		Happiness</strong>.
	</p>
</article>
```

In another example, a `##★★★★☆` unicode fragmention would jump to the 4/5 star rating.

```html
<abbr class="rating" title="4" tabindex="0">★★★★☆</abbr>
```

Additionally, **location.fragmention** returns a decoded fragmention, in the same manner that **location.hash** returns a decoded fragment.

While elements should not use IDs leading with a **#** single-hash, **##** double-hash fragments with a matching ID (e.g. **##term** and **id="#term"**) will not be interpretted as fragmentions.

While fragmentions should lead with a **##** double-hash, single-hash fragments with no matching ID (e.g. **#and+justice+for+all**) will be interpretted as fragmentions.

## JavaScript polyfill

The [fragmention polyfill](https://github.com/chapmanu/fragmentions/blob/master/fragmention.js) lets documents respond to fragmentions. When a fragmention is detected, the document is searched for its matching text. If a match is found, the window jumps to its corresponding element, adding a **fragmention** attribute for styling.

Additionally, the **window.location** object is given a **fragmention** property.

### Browser support

The fragmention polyfill has been successfully tested in desktop Chrome, Firefox, Safari, Opera, and Internet Explorer, as well as Firefox on Android and Safari on iOS. Legacy Internet Explorer browsers (6-8) are also supported, but marked as deprecated.

<small>Notes: If existing fragmention support is detected on the **window.location** object, the polyfill is ignored. To work around an issue with Firefox decoding **location.hash**, **location.href** is used to interpret fragmentions instead. To work around various issues with old IE, light hacking ensues.</small>

## Chrome extension

The [fragmention extension](https://chrome.google.com/webstore/detail/fragmentions/pgajkeekgcmgglngchhmcmnkffnhihck) for Google Chrome duplicates the functionality of the JavaScript polyfill for all pages on the internet. Thanks to feature detection, the extension will not conflict with pages already using the JavaScript polyfill or future versions of Chrome that may support fragmentions.

## Challenges

While most find the idea of fragmentions delightful, there are differing ideas on how they should work. We ask that contributors justify feature requests with concrete real world examples, as tests in the wild may reveal best practices. Otherwise, any of these challenges could be appended with, *“So, uh, what do you think?”*

### Double-hashes in the wild

The current [URL specification](http://url.spec.whatwg.org/#url-code-points) *does not allow* fragments to contain **#** hashes, so links with double-hashes like `<a href="##foo">` will fail current validation. These specifications can be updated, and the *invaliding* weakness of **##** may be conversely interpreted as a *non-conflicting* quality.

Browsers, on the other hand, *do allow* hashes, so invalid links in the wild like `<a href="##foo">` might generate conflict. As a result, fragmentions will always defer to fragments with matching IDs.

Other *spec-valid* alternatives to the **##** double-hash convention include **#@** (*hash + mention*) and **#*** (*hash + footnote*).

### Sensitivity

Fragmentions are case sensitive, making it easier to target specific text. However, if fragmentions were to be case insensitive, it would be easier to write them by hand. At first, there was no consensus on which practice was better, and over time case sensitivity has been accepted as the better choice.

### Encoding

Fragmentions are decoded before search, which means **+** plus signs are interpreted as spaces. This makes for prettier, conforming URLs, but may also be confusing for users targeting phrases using the space character. Therefore, plus signs in content must be escaped as **%2B**.

## Looks good to me

Thanks, now [test it yourself](https://github.com/chapmanu/fragmentions/blob/master/example.html), [give us feedback](https://github.com/chapmanu/fragmentions/issues), and have fun!

---

The fragmention script is 2.8KB or 578B minified + gzipped.
