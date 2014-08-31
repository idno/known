<?php if (\Idno\Core\site()->currentPage->getInput('q')) {
    // Search term
    ?>
    <div class="row" style="margin-top: 5em">
        <div class="span6 offset3">
            <div class="no-content explanation">
                <p>


                    We couldn't find anything that matches your search.

                </p>
            </div>
        </div>
    </div>
<?php
}
