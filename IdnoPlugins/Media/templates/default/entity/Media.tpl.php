<?php

    $player_id = rand(0,9999);

    if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
    if (empty($vars['feed_view'])) {
        ?>
        <div class="audio-play-wrapper"><a href="#" id="player<?=$player_id?>" class="audio-play-button"><i class="fa fa-play"></i></a></div>
        <h2 class="p-name">
             <a href="<?= $vars['object']->getDisplayURL(); ?>"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
        </h2>
    <?php
    }
    if (empty($vars['feed_view'])) {
        if ($attachments = $vars['object']->getAttachments()) {
            foreach ($attachments as $attachment) {
                $mainsrc = $attachment['url'];
                if (substr($attachment['mime-type'], 0, 5) == 'video') {
                    ?>
                    <p style="text-align: center">
                        <video src="<?= $this->makeDisplayURL($mainsrc) ?>" class="u-video known-media-element" controls preload="none" style="width: 100%"></video>
                    </p>
                    <?php

                } else {

                    ?>
                    <div id="waveform<?=$player_id?>" class="waveform-player"></div>
                    <script>
                        var wavesurfer<?=$player_id?> = WaveSurfer.create({
                            container: '#waveform<?=$player_id?>',
                            waveColor: '#aaa',
                            progressColor: '#333',
                            cursorColor: '#aaa',
                            height: 100
                        });
                        wavesurfer<?=$player_id?>.load('<?=$mainsrc?>');
                        $('#waveform<?=$player_id?>').click(function() {
                            wavesurfer<?=$player_id?>.play();
                        });
                        wavesurfer<?=$player_id?>.on('play', function() {
                            $('#player<?=$player_id?>').html('<i class="fa fa-pause"></i>');
                        });
                        wavesurfer<?=$player_id?>.on('pause', function() {
                            $('#player<?=$player_id?>').html('<i class="fa fa-play"></i>');
                        });
                        wavesurfer<?=$player_id?>.on('finish', function() {
                            $('#player<?=$player_id?>').html('<i class="fa fa-play"></i>');
                        });
                        $('#player<?=$player_id?>').click(function() {
                            wavesurfer<?=$player_id?>.playPause();
                            return false;
                        });
                    </script>
                    <?php

                }
            }
        }
    }
?>
<div class="e-content">
<?= $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel))) ?>
</div>
<?php
    if (!empty($vars['object']->tags)) {
?>

<p class="tag-row"><i class="icon-tag"></i> <?=$this->parseHashtags($vars['object']->tags)?></p>

<?php }
