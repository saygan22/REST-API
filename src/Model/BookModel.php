<?php declare(strict_types=1);

namespace Books\Model;


use Books\Database\Db;

class BookModel extends AbstractModel
{
    protected string $name;

    protected string $author;

    protected string $publisher;

    protected string $isbn;

    protected string $pages;


    public function __construct(string $name, string $author, string $publisher, string $isbn, string $pages)
    {
        $this->name = $name;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->isbn = $isbn;
        $this->pages = $pages;
    }



    public static function createTable(): void
    {
        $db = Db::get();
        $db->query('CREATE TABLE IF NOT EXISTS `books` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `author` TEXT NOT NULL,
            `publisher` TEXT NOT NULL,
            `isbn` TEXT NOT NULL,
            `pages` TEXT NOT NULL
        )');
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): BookModel
    {
        $this->name = $name;
        return $this;
    }


    public function getAuthor(): string
    {
        return $this->author;
    }


    public function setAuthor(string $author): BookModel
    {
        $this->author = $author;
        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }


    public function setPublisher(string $publisher): BookModel
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getISBN(): string
    {
        return $this->isbn;
    }


    public function setISBN(string $isbn): BookModel
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getPages(): string
    {
        return $this->pages;
    }

    public function setPages(string $pages): BookModel
    {
        $this->pages = $pages;
        return $this;
    }

    /** @inheritDoc */
    public static final function getTableName(): string
    {
        return "books";
    }

    /** @inheritDoc */
    public static final function fromArray(array $data): ?AbstractModel
    {
        if (!isset($data['id']) || !isset($data['name']) || !isset($data['author']) || !isset($data['publisher'])
            || !isset($data['isbn']) || !isset($data['pages'])) {
            return null;
        }

        $book = new BookModel($data['name'], $data['author'], $data['publisher'], $data['isbn'], $data['pages']);
        $book->id = (int)$data['id'];

        return $book;
    }

    /** @inheritDoc */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'pages' => $this->pages
        ];
    }

    public function toArrayGet(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author
        ];
    }




}
