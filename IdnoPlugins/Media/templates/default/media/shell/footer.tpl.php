<script>
    $('video').mediaelementplayer({
        features: ['playpause','progress','current','tracks','volume','fullscreen'],
        alwaysShowControls: false,
        pauseOtherPlayers: true,
        enableAutosize: false,
        mediaWidth: -1,
        mediaHeight: -1
    });
    $('audio').mediaelementplayer({
        features: ['playpause','progress','current','tracks','volume','fullscreen'],
        alwaysShowControls: false,
        pauseOtherPlayers: true,
        enableAutosize: false,
        mediaWidth: -1,
        mediaHeight: -1
    });
</script>