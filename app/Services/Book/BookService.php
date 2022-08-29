<?php

namespace App\Services\Book;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class BookService
{
    /**
     * @param array $data
     * @return mixed
     */
    public function createBook(array $data): mixed
    {
        return Book::create($data);
    }

    /**
     * @param string $uuid
     * @return mixed
     * @throws Throwable
     */
    public function getBook(string $uuid): mixed
    {
        return Cache::remember('book' . $uuid, now()->addDays(3), function () use ($uuid){
            $book = Book::findByUuid($uuid);

            throw_if(!$book, new ModelNotFoundException("Requested book not found.", ResponseAlias::HTTP_NOT_FOUND));

            return $book;
        });
    }

    /**
     * @param array $params
     * @param array $relations
     * @param array $pagination
     * @return mixed
     */
    public function getBooks(array $params = [], array $relations = [], array $pagination = []): mixed
    {
        return Book::getBooks()->with($relations)->where($params)
            ->paginate(data_get($pagination, 'perPage', 100));
    }

    /**
     * @param array $data
     * @param string $uuid
     * @return mixed
     * @throws Throwable
     */
    public function updateBook(array $data, string $uuid): mixed
    {
        $book = $this->getBook($uuid);

        $book->update($data);

        $book->refresh();

        Cache::put('book' . $uuid, $book, now()->addDays(3));

        return $book;
    }

    /**
     * @param string $uuid
     * @return bool
     * @throws Throwable
     */
    public function deleteBook(string $uuid): bool
    {
        $book = $this->getBook($uuid);

        $book->delete();

        Cache::forget('book' . $uuid);

        return true;
    }

    /**
     * @param string $searchTerm
     * @param array $pagination
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm, array $pagination = []): LengthAwarePaginator
    {
        return Book::search($searchTerm)->paginate(data_get($pagination, 'perPage', 100));
    }
}
