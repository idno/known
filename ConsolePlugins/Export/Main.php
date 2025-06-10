<?php

namespace ConsolePlugins\Export {

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
                'posts_authors' => [],
            ],
            'meta' => [
                'exported_on' => time() * 1000,
                'version' => '2.14.0'
            ]
        ];


        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            // Get all the registered content types
            $types = \Idno\Common\ContentType::getRegisteredClasses();

            // Get all posts
            $posts = \Idno\Common\Entity::getFromX($types, [], [], PHP_INT_MAX);
            $safe_post_id = 0;
            foreach($posts as $post) {
                $post_object = [
                    'id' => $safe_post_id,
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
					'mobiledoc' 		=> '{"version":"0.3.1","atoms":[],"cards":[["html",{"html":"'.str_replace(
						array(
							'\n',
							'\\/',
						),
						array(
							'\\n',
							'/',
						),
						json_encode($post->getBody()) ) .'"}]],"markups":[],"sections":[[10,0],[1,"p",[]]]}',
					'feature_image'	=> null,
					'feature_image_alt'	=> null,
					'feature_image_caption'	=> null,
					'featured' => 0,
					'type' => substr_count($post->getClassName, 'StaticPage') ? 'page' : 'post',
					'status' => 'published',
					'meta_title' => $post->getTitle(),
					'meta_description' => $post->getDescription(),
					'created_at' => date('Y-m-d\TH:i:sP', $post->created),
					'updated_at' => date('Y-m-d\TH:i:sP', $post->created),
					'published_at'	=> date('Y-m-d\TH:i:sP', $post->created)
                ];

                $safe_author_id = $this->getSafeAuthorId($post->getOwnerID());
                $this->export['posts_authors'][] = [
                    'post_id' => $safe_post_id,
                    'author_id' => $safe_author_id,
                ];

                $tags = $post->getTags();
                $tags[] = $post->getMicroformats2ObjectType(); // Adding microformat type so it can be added back in Ghost template
                foreach($tags as $tag) {
                    $safe_tag_id = $this->getSafeTagId($tag);
                    $this->export['post_tags'][] = [
                        'post_id' => $safe_post_id,
                        'tag_id' => $safe_tag_id,
                    ];
                }

                $this->export['data']['posts'][] = $post_object;
                
            }

            $output->write(json_encode(['db' => $this->export]));
        }

        private function getSafeAuthorId(string $user_id) {
            if (empty($this->author_map[$user_id])) {
                $user = User::getByID($user_id);
                $id = count($this->author_map);
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
                $this->author_map[$user->getID()] = $user_obj['id'];
                $this->users[] = $user_obj;
            } else {
                $id = $this->author_map[$user_id];
            }
            return $id;
        }

        private function getSafeTagId(string $tag) {
            if (empty($this->tag_map[$tag])) {
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
