<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        usort($vars['annotations'], function($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        });

        $rsvps_by_response = ['yes' => '', 'maybe' => '', 'no' => '', 'etc' => ''];

        foreach($vars['annotations'] as $locallink => $annotation) {
            $permalink = !empty($annotation['permalink']) ? $annotation['permalink'] : $locallink;
            $rsvp = !empty($annotation['rsvp']) ? strtolower(trim($annotation['rsvp'])) : 'etc';

            ob_start();
            ?>
            <div class="idno-annotation row">
                <div class="idno-annotation-image col-md-1 hidden-sm">
                    <p>
                        <a href="<?=$annotation['owner_url']?>" class="icon-container"><img src="<?=\Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($annotation['owner_image'])?>" /></a>
                    </p>
                </div>
                <div class="idno-annotation-content col-md-6">
                    <p>
                        <strong><?=$annotation['content']?></strong>
                    </p>
                    <p><small><a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a> on <a href="<?=$permalink?>"><?=parse_url($permalink, PHP_URL_HOST)?></a></small></p>
                </div>
            </div>
        <?php
            $rsvps_by_response[$rsvp] .= ob_get_clean();
        }

        foreach($rsvps_by_response as $rsvp => $list) {

            if (!empty($list)) {
                switch($rsvp) {
                    case 'yes':
                        $title = \Idno\Core\Idno::site()->language()->_('Attending');
                        break;
                    case 'maybe':
                        $title = \Idno\Core\Idno::site()->language()->_('Maybe attending');
                        break;
                    case 'no':
                        $title = \Idno\Core\Idno::site()->language()->_('Not attending');
                        break;
                    case 'etc':
                        $title = \Idno\Core\Idno::site()->language()->_('Other responses');
                        break;
                }

                ?>
                    <div class="row">
                        <div class="col-md-7">
                            <p>
                                <strong><?=$title;?></strong>
                            </p>
                        </div>
                    </div>
                <?php
                echo $list;
            }

        }
    }