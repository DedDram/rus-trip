<?php
namespace Models\Articles;

use Exceptions\InvalidArgumentException;
use Models\Users\User;
use Models\ActiveRecordEntity;

class Article extends ActiveRecordEntity
{
    /** @var string */
    protected string $name;

    /** @var string */
    protected string $text;

    /** @var string */
    protected string $authorId;

    /** @var string */
    protected string $createdAt;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return User::getById($this->authorId);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }


    protected static function getTableName(): string
    {
        return 'articles';
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $fields, User $author): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }
        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }
        $article = new Article();
        $article->setAuthor($author);
        $article->setName($fields['name']);
        $article->setText($fields['text']);
        $article->save();
        return $article;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function updateFromArray(array $fields): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }

        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }
        $this->setName($fields['name']);
        $this->setText($fields['text']);
        $this->save();
        return $this;
    }
}
