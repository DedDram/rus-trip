<?php

namespace Controllers;

use Exceptions\ForbiddenException;
use Exceptions\InvalidArgumentException;
use Exceptions\NotFoundException;
use Exceptions\UnauthorizedException;
use Models\Articles\Article;


class ArticlesController extends AbstractUsersAuthController
{
    public function view(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

        $this->view->renderHtml('articles/view.php', [
            'article' => $article
        ]);
    }

    /**
     * @throws UnauthorizedException|NotFoundException
     * @throws ForbiddenException
     */
    public function edit(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }
        if(!$this->user->isAdmin()){
            throw new ForbiddenException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!empty($_POST)) {
            try {
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }

            header('Location: /articles/' . $article->getId(), true, 301);
            exit();
        }

        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
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
        if(!$this->user->isAdmin()){
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/add.php', ['error' => $e->getMessage()]);
                return;
            }

            header('Location: /articles/' . $article->getId(), true, 301);
            exit();
        }

        $this->view->renderHtml('articles/add.php');
    }

    public function delete(int $articleId): void
    {
        $article = Article::getById($articleId);
        if ($article !== null) {
            $article->delete();
            echo ' Статья удалена';
        }else{
            echo ' Статьи с таким id не существует';
        }

    }
}