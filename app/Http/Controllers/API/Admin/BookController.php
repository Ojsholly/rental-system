<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\CreateBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Book\BookResourceCollection;
use App\Services\Book\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class BookController extends Controller
{
    public BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $books = $this->bookService->getBooks([], [], request()->query());
        } catch (Throwable $exception) {
            report($exception);

            return response()->error("An error occurred while fetching books.", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new BookResourceCollection($books), "Books fetched successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateBookRequest $request
     * @return JsonResponse
     */
    public function store(CreateBookRequest $request): JsonResponse
    {
        try {
            $book = $this->bookService->createBook($request->validated());
        } catch (Throwable $exception) {
            report($exception);

            return response()->error("An error occurred while creating book.", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new BookResource($book), "Book created successfully.", ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $book = $this->bookService->getBook($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error("Requested book not found.", $exception->getCode());
            }
            report($exception);

            return response()->error("An error occurred while fetching book.", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new BookResource($book), "Book fetched successfully.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBookRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateBookRequest $request, string $id): JsonResponse
    {
        try {
            $book = $this->bookService->updateBook(array_filter($request->validated()), $id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error("Requested book not found.", $exception->getCode());
            }
            report($exception);

            return response()->error("An error occurred while updating book.", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new BookResource($book), "Book updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $book = $this->bookService->deleteBook($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error("Requested book not found.", $exception->getCode());
            }

            report($exception);

            return response()->error("An error occurred while deleting book.", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return match ($book) {
            true => response()->success(null, "Book deleted successfully."),
            false => response()->error("Book not found.", ResponseAlias::HTTP_NOT_FOUND),
        };
}
}
