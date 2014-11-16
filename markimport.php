<?php

    require_once(dirname(__FILE__) . '/Idno/start.php');

    $connection_string = 'mysql:host=localhost;dbname=wpmark;charset=utf8';
    $dbh = new \PDO($connection_string, 'wpmark','wpmark');

    $statement = $dbh->prepare('select * from wp_terms');
    $statement->execute();
    $tag_names = array();
    if ($tags = $statement->fetchAll(\PDO::FETCH_OBJ)) {
        foreach($tags as $tag) {
            $tag_names[$tag->term_id] = $tag->slug;
        }
    }

    $statement = $dbh->prepare('select * from wp_term_relationships');
    $statement->execute();
    $tags = array();
    if ($relationships = $statement->fetchAll(\PDO::FETCH_OBJ)) {
        foreach($relationships as $relationship) {
            if (!empty($tag_names[$relationship->term_taxonomy_id])) {
                $tags[$relationship->object_id][] = '#' . $tag_names[$relationship->term_taxonomy_id];
            }
        }
    }

    $mark = \Idno\Entities\User::getByUUID('http://idno.dev/profile/markpilgrim');

    $statement = $dbh->prepare('select * from wp_posts where post_status = "publish"');
    $statement->execute();
    if ($posts = $statement->fetchAll(\PDO::FETCH_OBJ)) {

        $i = 0;
        foreach($posts as $post) {

            $i++;

            echo '<h2>' . $post->post_title . '</h2>';
            echo date('Y M d', strtotime($post->post_date_gmt));
            if (!empty($tags[$post->ID])) {
                $post->post_content .= '<p>' . implode(' ', $tags[$post->ID]);
            }
            echo \Idno\Core\site()->template()->autop($post->post_content);

            $entry = new IdnoPlugins\Text\Entry();
            $entry->setOwner($mark);
            $entry->created = strtotime($post->post_date_gmt);
            $entry->body = $post->post_content;
            $entry->setTitle($post->post_title);
            $entry->original_url = $post->guid;
            $entry->setAccess('PUBLIC');
            $entry->save();
            $entry->addToFeed();

        }

    }