<?php

    $message = $vars['message'];

?>
<script>
    addMessage('<?=addslashes($message['message']);?>','<?=addslashes($message['message_type']);?>');
</script>
