<script>

    $.getJSON('<?=\Idno\Core\site()->config()->getURL()?>search/mentions.json', function(data) {
        $(".mentionable").mention({
            delimiter: '@',
            sensitive : true,
            queryBy: ['name','username'],
            users: data
        });
    });

</script>