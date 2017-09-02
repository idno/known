<div style="font-weight: bold; font-size: 30px; line-height: 32px; color: #333" align="center">
    An error occured on you Known site <?= $vars['site']; ?>!
</div><br>
<hr/>
<br>
<pre>
    <?= $vars['message']; ?>
</pre>
<br><br>
Logged in user was <?= $vars['user']; ?> <br>

Agent: <?= $_SERVER['HTTP_USER_AGENT']; ?> <br>

QS: <?= $_SERVER['QUERY_STRING']; ?> <br>

Referrer: <?= $_SERVER['HTTP_REFERER']; ?> <br>
<br>
<br>
<br>