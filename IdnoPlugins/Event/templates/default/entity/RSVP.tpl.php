<?php
$currentPage = \Idno\Core\Idno::site()->currentPage();
if (!empty($currentPage) && \Idno\Core\Idno::site()->currentPage()->isPermalink()) {
    $rel = 'rel="in-reply-to" class="u-in-reply-to"';
} else {
    $rel = '';
}
?>
<p class="p-name e-content entry-content"><data class="p-rsvp" value="<?php echo $vars['object']->rsvp?>"><strong><?php echo ucfirst($vars['object']->rsvp)?>:</strong> <?php echo $this->parseURLs($this->parseHashtags($vars['object']->body), $rel)?></data></p>
<?php echo $this->draw('entity/content/embed');
