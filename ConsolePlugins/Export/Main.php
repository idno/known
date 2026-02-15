<?php

namespace ConsolePlugins\Export {

use Idno\Core\Idno;
use Idno\Entities\User;
    class Main extends \Idno\Common\ConsolePlugin
    {

        private $author_map = [];
        private $users = [];
        private $tag_map = [];
        private $tags = [];

        private $export = [
            'posts' => [],
            'users' => [],
        ];


        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            // Get all the registered content types
            $types = \Idno\Common\ContentType::getRegisteredClasses();

            // Initialize autop
            $autop = new \mapkyca\autop\MrClayAutoP();

            // Get all posts
            $posts = \Idno\Common\Entity::getFromX($types, [], [], PHP_INT_MAX);
            foreach($posts as $post) {
                $safe_post_id = count($this->export['posts']);

                $safe_author_id = $this->getSafeAuthorId($post->getOwnerID());

                $post_object = [
                    'id' => $safe_post_id,
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
                    'html' => $autop->process($post->getDescription()),
					'type' => $post->getClassName(),
					'access' => $post->access,
					'meta_title' => $post->getTitle(),
					'meta_description' => $post->getShortDescription(),
					'created_at' => date('Y-m-d\TH:i:sP', $post->created),
                    'mf2_object_type' => $post->getActivityStreamsObjectType(),
                    'owner_id' => $safe_author_id,
                ];                

                $tags = $post->getTags();
                $tags[] = $post->getActivityStreamsObjectType(); // Adding microformat type so it can be added back in Ghost template
                $tags = str_replace('#','',$tags);
                $post_object['tags'] = $tags;

                $this->export['posts'][] = $post_object;
                
            }

            $this->export['users'] = $this->users;

            $json_output = json_encode($this->export);
            // Strip command tags
            $json_output = str_replace('\n', '', $json_output);
            $json_output = str_replace('\r', '', $json_output);
            $json_output = str_replace('\t', '', $json_output);

            $output->write($json_output, JSON_UNESCAPED_UNICODE);
        }

        private function getSafeAuthorId(string $user_id) {
            if (!isset($this->author_map[$user_id])) {
                $user = User::getByUUID($user_id);
                if ($user) {
                    $id = count($this->author_map); //$this->generateId(count($this->author_map) + 1000000); //md5(count($this->author_map) . $user_id);
                    $user_obj = [
                        'id' => count($this->author_map),
                        'username' => $user->getHandle(),
                        'bio' => $user->getDescription(),
                        'website' => $user->profile['url'],
                        'created_at' => date('Y-m-d\TH:i:sP', $user->created),
                        'email' => $user->email,
                        'name' => $user->getName(),
                        'profile_image' => $user->getIcon(),
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

        public function getCommand()
        {
            return 'export';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Exports Idno posts to a simple JSON format.');
        }

        public function getParameters()
        {
            return [];
        }

    }
}
