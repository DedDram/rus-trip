<?php

namespace Controllers;


use Models\Content\Content;

class MainController extends AbstractUsersAuthController
{
    /**
     * @throws \Exception
     */
    public function main($url)
    {
        if(!empty($url)){
            header('Location: https://rus-trip.ru', true, 301);
        }
        $content = new Content();
        $page = $content->gatPageById(1);

        $this->view->renderHtml('main/main.php',
            [   'title' => $page->title,
                'metaDesc' => $page->descr,
                'metaKey' => $page->keywords,
                'about' => $page->about,
                'menu_title' => $page->menu_title,
            ]);
    }
}
