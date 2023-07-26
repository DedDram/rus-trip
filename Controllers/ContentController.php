<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Exceptions\UnauthorizedException;
use Models\Comments\Comments;
use Models\Content\Content;
use Models\Informer\Informer;
use Services\ExternalLinks;
use Services\Pagination;

class ContentController extends AbstractUsersAuthController
{
    /**
     * @throws NotFoundException
     */
    public function view(int $contentId): void
    {
        $content = Content::getPage($contentId);
        if (empty($content[0])) {
            throw new NotFoundException();
        }
        //проверяем соответствие ID алиасу и если не совпадает - редирект на верный алиас
        preg_match('~/(.*)/(\d+)-(.*)~m', $_SERVER['REQUEST_URI'], $uriAlias);
        if ($content[0]->alias !== $uriAlias[3] || $content[0]->catAlias !== $uriAlias[1] && !preg_match('~/edit$~m', $uriAlias[1])) {
            header('Location: ' . 'https://' . $_SERVER['HTTP_HOST'] . '/' . $content[0]->catAlias . '/' . $content[0]->id . '-' . $content[0]->alias, true, 301);
        }
        //внешние ссылки редирект
        $content[0]->text = ExternalLinks::replaceExternalLinks($content[0]->text);

        //комменты
        $limit = 60;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = $start = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = $start = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }
        $comments = Comments::getComments('com_content', $contentId, $limit, $offset, $start, $this->user);
        $pagesCount = $comments['total'];
        //добавление стилей и скриптов комментов в /../header.php
        $style = '<link rel="stylesheet" href="/../templates/maps/css/style.css">' . PHP_EOL;
        $script = '<script src="/../templates/maps/js/map.js"></script>' . PHP_EOL;
        if (!empty($this->user)) {
            $script .= '<script src="/../templates/maps/js/moderation.js"></script>' . PHP_EOL;
        }

        $this->view->setVar('style', $style);
        $this->view->setVar('script', $script);

        $pagination = new Pagination($page, $limit, $pagesCount);

        $this->view->renderHtml('content/view.php', [
            'content' => $content[0],
            'title' => $content[0]->title,
            'metaKey' => $content[0]->metakey,
            'metaDesc' => $content[0]->metadesc,
            'maps' => $comments,
            'pagination' => $pagination,
            'pagesCount' => $pagesCount,
            'object_group' => 'com_content',
            'object_id' => $contentId,
            'user' => $this->user,
        ]);

    }

    /**
     * @throws NotFoundException
     */
    public function viewAllPagination($catId = 0): void
    {
        $content = Content::findAllObjectId('catid', $catId, 'ORDER BY id DESC');
        switch ($catId) {
            case 231:
                $catAlias = 'sochineniya';
                $title = 'Сочинения на тему';
                break;
            case 28:
                $catAlias = 'news';
                $title = 'Новости образования';
                break;
            default:
                throw new \Exceptions\NotFoundException();
        }
        if (empty($content)) {
            $this->view->renderHtml('errors/404.php', ['title' => 'Страница не найдена'], 404);
            return;
        }
        $pagesCount = count($content);
        $limit = 5;
        if (empty($_GET['start'])) {
            $page = 1;
            $offset = 0;
        } else {
            if (is_numeric($_GET['start'])) {
                $page = (int)$_GET['start'];
                $offset = ($_GET['start'] - 1) * $limit;
            } else {
                throw new NotFoundException();
            }
        }


        $pagination = new Pagination($page, $limit, $pagesCount);
        //сообщения об удалении новости
        $successful = '';
        $error = '';
        if (!empty($_COOKIE['delete'])) {
            setcookie('delete', '', 0, '/', '', false, true);
            $successful = 'Новость успешно удалена';
        }

        $this->view->renderHtml('content/viewAll.php', [
            'contents' => array_slice($content, $offset, $limit),
            'pagination' => $pagination,
            'page' => $page,
            'title' => $title,
            'pagesCount' => $pagesCount,
            'successful' => $successful,
            'error' => $error,
            'catAlias' => $catAlias,
        ]);
    }

    /**
     * @throws UnauthorizedException|NotFoundException
     * @throws ForbiddenException
     */
    public function edit(int $contentId)
    {
        $content = Content::getById($contentId);

        if ($content === null) {
            throw new NotFoundException();
        }
        if (!empty($this->user) && !$this->user->isAdmin()) {
            throw new ForbiddenException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!empty($_POST)) {
            try {
                $content->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('content/edit.php', ['error' => $e->getMessage(), 'content' => $content]);
                return;
            }
            $alias = $content->getAlias();
            header('Location: /news/' . $content->getId() . '-' . $alias, true, 301);
            exit();
        }

        $this->view->renderHtml('content/edit.php', ['content' => $content]);
    }

    /**
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function add(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if (!$this->user->isAdmin()) {
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $content = Content::createFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('content/add.php', ['error' => $e->getMessage()]);
                return;
            }

            header('Location: /news/' . $content->getId(), true, 301);
            exit();
        }

        $this->view->renderHtml('content/add.php');
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function delete(int $contentId): void
    {
        $content = Content::getById($contentId);

        if ($content === null) {
            throw new NotFoundException();
        }
        if (!empty($this->user) && !$this->user->isAdmin()) {
            throw new ForbiddenException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        $content->delete();
        setcookie('delete', 'delete', 0, '/', '', false, true);
        header('Location: /content', true, 301);

    }

    public function cookiePolicy()
    {
        $this->view->renderHtml('content/cookiePolicy.php', ['title' => 'rus-trip.ru Network Cookie Policy']);
    }
    public function privacyPolicy()
    {
        $this->view->renderHtml('content/privacyPolicy.php', ['title' => 'Privacy Policy']);
    }
    public function contact()
    {
        $this->view->renderHtml('content/contact.php', ['title' => 'Contact us']);
    }

    /**
     * @throws NotFoundException
     */
    public function city($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $this->view->renderHtml('content/city.php',
            [
                'title' => $city->name.' - путеводитель',
                'city' => $city,
                'navLinks' => $navLinks,
            ]);
    }

    /**
     * @throws NotFoundException
     */
    public function map($city_alias)
    {
        $cities = new Content();
        $city = $cities->getCity((string) $city_alias);
        $navLinks = $cities->getNavLinks((string) $city_alias);
        $cityGenitive = $cities->getCityGenitive((string) $city->name);
        $this->view->renderHtml('content/map.php',
            [
                'title' => 'Карта '.$cityGenitive->genitive.' с улицами и номерами домов',
                'map' => $city->map,
                'navLinks' => $navLinks,
                'cityGenitive' => $cityGenitive,
            ]);
    }
}