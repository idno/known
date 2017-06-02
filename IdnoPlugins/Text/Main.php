<?php

    namespace IdnoPlugins\Text {

        class Main extends \Idno\Common\Plugin {
           
           
           function init()
           {
             parent::init();
             
             if(empty(\Idno\Core\Idno::site()->config()->truncate_character)){
             \Idno\Core\Idno::site()->config()->truncate_character=300; //Default Truncate length
             }
             
             if(empty(\Idno\Core\Idno::site()->config()->truncate)){
             \Idno\Core\Idno::site()->config()->truncate=false; //Default Truncate 
             }
             
           }


            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('/entry/edit/?', '\IdnoPlugins\Text\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/entry/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Text\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/entry/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Text\Pages\Delete');
                \Idno\Core\Idno::site()->addPageHandler('/entry/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
                \Idno\Core\Idno::site()->addPageHandler('/admin/blog/?', '\IdnoPlugins\Text\Pages\Admin\Blog');
                
                \Idno\Core\Idno::site()->template()->extendTemplate('admin/menu/items', 'admin/menu/items/blog');


            }
        }

    }