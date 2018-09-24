<?php

    $message = $vars['message'];

?>
<script>
    addMessage('<?php echo addslashes($message['message']);?>','<?php echo addslashes($message['message_type']);?>');
</script>
