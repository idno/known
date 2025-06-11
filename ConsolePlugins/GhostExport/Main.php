<?php

namespace ConsolePlugins\GhostExport {

use Idno\Core\Idno;
use Idno\Entities\User;
    class Main extends \Idno\Common\ConsolePlugin
    {

        private $author_map = [];
        private $users = [];
        private $tag_map = [];
        private $tags = [];
        private $posts = [];

        private $export = [
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


        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            // Get all the registered content types
            $types = \Idno\Common\ContentType::getRegisteredClasses();

            // Set generated time
            $this->export['meta']['exported_on'] = time() * 1000;

            // Get all posts
            $posts = \Idno\Common\Entity::getFromX($types, [], [], PHP_INT_MAX);
            foreach($posts as $post) {
                $safe_post_id = count($this->export['data']['posts']); // $this->generateId(count($this->export['data']['posts']));
                $autop = new \mapkyca\autop\MrClayAutoP();

                $safe_author_id = $this->getSafeAuthorId($post->getOwnerID());

                $post_object = [
                    'id' => $safe_post_id,
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
					/*'mobiledoc' 		=> '{"version":"0.3.1","atoms":[],"cards":[["html",{"html":"'.str_replace(
						array(
							'\n',
							'\\/',
						),
						array(
							'\\n',
							'/',
						),
						json_encode($post->getBody()) ) .'"}]],"markups":[],"sections":[[10,0],[1,"p",[]]]}',*/
                    'html' => $autop->process($post->getDescription()),
					'feature_image'	=> null,
					'feature_image_alt'	=> null,
					'feature_image_caption'	=> null,
					'featured' => 0,
					'type' => substr_count($post->getClassName(), 'StaticPage') ? 'page' : 'post',
					'status' => 'published',
                    'visibility' => 'public',
                    'locale' => null,
					'meta_title' => $post->getTitle(),
					'meta_description' => $post->getShortDescription(),
					'created_at' => date('Y-m-d\TH:i:sP', $post->created),
					'updated_at' => date('Y-m-d\TH:i:sP', $post->created),
					'published_at'	=> date('Y-m-d\TH:i:sP', $post->created),
                    'author_id' => $safe_author_id,
                    'email_only' => false,
                ];                

                $tags = $post->getTags();
                $tags[] = $post->getMicroformats2ObjectType(); // Adding microformat type so it can be added back in Ghost template
                foreach($tags as $tag) {
                    $safe_tag_id = $this->getSafeTagId($tag);
                    $this->export['data']['posts_tags'][] = [
                        'post_id' => $safe_post_id,
                        'tag_id' => $safe_tag_id,
                        'sort_order' => 0,
                    ];
                }

                $this->export['data']['posts'][] = $post_object;
                
            }

            $this->export['data']['tags'] = $this->tags;
            $this->export['data']['users'] = $this->users;

            $json_output = json_encode(['db' => $this->export]);
            // Strip command tags
            $json_output = str_replace('\n', '', $json_output);
            $json_output = str_replace('\r', '', $json_output);
            $json_output = str_replace('\t', '', $json_output);

            $output->write($json_output, JSON_UNESCAPED_UNICODE);
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
                        'bio' => $user->getDescription(),
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
                $id = $this->generateId(count($this->tag_map) + 2000000);
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
