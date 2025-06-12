<?php

namespace ConsolePlugins\GhostExport {

use Idno\Core\Idno;
use Idno\Entities\User;
use IdnoPlugins\Like\Like;
    class Main extends \Idno\Common\ConsolePlugin
    {

        private $author_map = [];
        private $users = [];
        private $tag_map = [];
        private $tags = [];
        private $posts = [];

        private $export = [];


        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            // Get all the registered content types
            $types = \Idno\Common\ContentType::getRegisteredClasses();

            // Set generated time
            $this->export['meta']['exported_on'] = time() * 1000;

            // Get all posts
            $posts = \Idno\Common\Entity::getFromX($types, [], [], PHP_INT_MAX);

            $post_count = 0;
            $page = 1;
            foreach($posts as $post) {

                if ($post_count == 0) {
                    $this->export = [
                        'data' => [
                            'posts' => [],
                            'tags' => [],
                            'users' => [],
                            'posts_tags' => [],
                        ],
                        'meta' => [
                            'version' => '5.75.1'
                        ]
                    ];
                    $this->author_map = [];
                    $this->users = [];
                    $this->tags = [];
                    $this->posts = [];
                    $this->tag_map = [];
                }

                $safe_post_id = count($this->export['data']['posts']); // $this->generateId(count($this->export['data']['posts']));
                $autop = new \mapkyca\autop\MrClayAutoP();

                $safe_author_id = $this->getSafeAuthorId($post->getOwnerID());

                $mf2type = $post->getMicroformats2ObjectType();
                if ($post->getClassName() == 'Status') continue;
                switch($post->getClassName()) {
                    case 'Like':
                            $description = $post->description . '<p>[<a href="' . $post->body . '" class="u-bookmark-of">Link</a>]</p>';
                            $short_description = '';
                        break;
                    default:
                            $description = $post->getDescription();
                            $short_description = $post->getShortDescription();
                        break;
                }

                $body = $autop->process(Idno::site()->template()->parseURLs(Idno::site()->template()->parseHashtags($description)));

                if ($body == '') continue;

                $post_object = [
                    'id' => $safe_post_id,
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
                    'html' => $body,
					'feature_image'	=> null,
					'feature_image_alt'	=> null,
					'feature_image_caption'	=> null,
					'featured' => 0,
					'type' => substr_count($post->getClassName(), 'StaticPage') ? 'page' : 'post',
					'status' => 'published',
                    'visibility' => 'public',
                    'locale' => null,
					'meta_title' => $post->getTitle(),
					'meta_description' => $short_description,
					'created_at' => date('Y-m-d\TH:i:sP', $post->created),
					'updated_at' => date('Y-m-d\TH:i:sP', $post->created),
					'published_at'	=> date('Y-m-d\TH:i:sP', $post->created),
                    'author_id' => $safe_author_id,
                    'email_only' => false,
                    'class_name' => $post->getClassName()
                ];                

                $tags = $post->getTags();
                if ($post->getClassName() == 'Like') {
                    $tags[] = 'NotableLinks';
                }

                $post_object['tags'] = $tags;

                foreach($tags as $tag) {
                    $safe_tag_id = $this->getSafeTagId($tag);
                    $this->export['data']['posts_tags'][] = [
                        'post_id' => $safe_post_id,
                        'tag_id' => $safe_tag_id,
                        'sort_order' => 0,
                    ];
                }

                $this->export['data']['posts'][] = $post_object;

                $post_count++;
                
                if ($post_count == 500) {
                    $this->export['data']['tags'] = $this->tags;
                    $this->export['data']['users'] = $this->users;

                    $json_output = json_encode($this->export, JSON_UNESCAPED_UNICODE);

                    $json_output = str_replace('\n', '', $json_output);
                    $json_output = str_replace('\r', '', $json_output);
                    $json_output = str_replace('\t', '', $json_output);

                    file_put_contents('export-' . $page . '.json', $json_output);

                    $page++;
                    $post_count = 0;
                }
                
            }
        }

        private function generateId($counter) {
            // Initialize machine ID once
            $machineId = md5(Idno::site()->config()->getURL());
            
            // 4-byte timestamp
            $timestamp = pack('N', time());
            
            // 5-byte machine/process identifier
            $machineProcess = hex2bin($machineId);
            
            // 3-byte counter (increments and wraps at 16777215)
            $string_counter = substr(pack('N', $counter), 1);
            
            return bin2hex($timestamp . $machineProcess . $string_counter);
        }

        private function getSafeAuthorId(string $user_id) {
            if (!isset($this->author_map[$user_id])) {
                $user = User::getByUUID($user_id);
                if ($user) {
                    $id = count($this->author_map); //$this->generateId(count($this->author_map) + 1000000); //md5(count($this->author_map) . $user_id);
                    $user_obj = [
                        'id' => count($this->author_map),
                        'slug' => $user->getHandle(),
                        'bio' => '', // Known bios are too long for Ghost
                        'website' => null,
                        'created_at' => date('Y-m-d\TH:i:sP', $user->created),
                        'email' => $user->email,
                        'name' => $user->getName(),
                        'profile_image' => $user->getIcon(),
                        'roles' => ['Contributor']
                    ];
                    $this->author_map[$user_id] = $user_obj['id'];
                    $this->users[] = $user_obj;
                } else {
                    $id = null;
                }
            } else {
                $id = $this->author_map[$user_id];
            }
            return $id;
        }

        private function getSafeTagId(string $tag) {
            $tag = str_replace('#','',$tag);
            if (!isset($this->tag_map[$tag])) {
                $id = count($this->tag_map);
                $this->tags[] = [
                    'id' => $id,
                    'name' => $tag,
                    'slug' => $tag,
                    'description' => '',
                ];
                $this->tag_map[$tag] = $id;
            } else {
                $id = $this->tag_map[$tag];
            }
            return $id;
        }

        public function getCommand()
        {
            return 'ghost-export';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Exports Known posts to Ghost\'s Lexical JSON format.');
        }

        public function getParameters()
        {
            return [];
        }

    }
}
