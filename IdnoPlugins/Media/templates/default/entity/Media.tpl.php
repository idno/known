<?php

    $player_id = rand(0,9999);

    if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
    $tags = "";
    if (!empty($vars['object']->tags)) {
        $tags = $this->__(['tags' => $vars['object']->tags])->draw('forms/output/tags');
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
            
            if (substr($vars['object']->media_type, 0, 5) == 'video') {
                // Video
                ?>
                <p style="text-align: center">
                    <video class="u-video known-media-element" controls preload="none" style="width: 100%">
                        <?php
                        foreach ($attachments as $attachment) {
                            $mainsrc = $attachment['url'];
                            
                            ?>
                            <source src="<?= $mainsrc ?>" type="<?= $attachment['mime-type']; ?>">
                            <?php
                        }
                        ?>
                            
                            <?= \Idno\Core\Idno::site()->language()->_('Sorry, your browser does not support the video tag.'); ?>
                    </video>
                </p>
                <?php
            } else {
                // Audio
                foreach ($attachments as $attachment) {
                    $mainsrc = $attachment['url'];
                    
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
<?= $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel))) . $tags; ?>
</div>

