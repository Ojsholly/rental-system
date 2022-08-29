<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Services\Book\BookService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function book(array $attributes = [])
    {
        return Book::factory()->create($attributes);
    }

    public function bookData(): array
    {
        return [
            'title' => fake()->unique()->sentence(),
            'author' => fake()->name(),
            'description' => fake()->paragraph(),
            'isbn' => fake()->isbn13(),
            'publisher' => fake()->company(),
            'published_on' => now()->subYears(mt_rand(1, 100))->toDateString(),
        ];
    }

    public function testBookCreationValidation()
    {
        $this->postJson(route('admin.books.store'), [])
                ->assertUnprocessable()
                ->assertJsonValidationErrors(['title', 'author', 'description', 'isbn', 'publisher', 'published_on']);
    }

    public function testBookServiceCreateReturnsBook()
    {
        $data = $this->bookData();

        $book = (new BookService())->createBook($data);

        $this->assertInstanceOf(Book::class, $book);
    }

    public function testBookCreation()
    {
        $booksCount = Book::count();

        $data = $this->bookData();

        $this->postJson(route('admin.books.store'), $data)
                ->assertCreated()
                ->assertJsonStructure(['status', 'message', 'data' => ['id', 'uuid', 'title', 'author', 'description', 'isbn', 'publisher', 'published_on', 'created_at', 'updated_at', 'deleted_at']])
                ->assertJsonFragment(['status' => 'success', 'message' => 'Book created successfully.'])
                ->assertJson([
                    'data' => [
                        'title' => $data['title'],
                        'author' => $data['author'],
                        'description' => $data['description'],
                        'isbn' => $data['isbn'],
                        'publisher' => $data['publisher'],
                        'published_on' => Carbon::parse($data['published_on'])->toFormattedDateString(),
                    ],
                ]);

        $this->assertEquals($booksCount + 1, Book::count());
    }

    public function testBookRetrievalWithWrongId()
    {
        $this->getJson(route('admin.books.show', ['book' => Str::uuid()]))
                ->assertNotFound()
                ->assertJsonStructure(['status', 'message']);
    }

    public function testBookServiceGetBookReturnsBook()
    {
        $book_id = Book::factory()->create()->uuid;

        $book = (new BookService())->getBook($book_id);

        $this->assertInstanceOf(Book::class, $book);
    }

    public function testBookRetrieval()
    {
        $book = $this->book();

        $this->getJson(route('admin.books.show', ['book' => $book->uuid]))
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data' => ['id', 'uuid', 'title', 'author', 'description', 'isbn', 'publisher', 'published_on', 'created_at', 'updated_at', 'deleted_at']])
                ->assertJson([
                    'data' => [
                        'id' => $book->id,
                        'uuid' => $book->uuid,
                        'title' => $book->title,
                        'author' => $book->author,
                        'description' => $book->description,
                        'isbn' => $book->isbn,
                        'publisher' => $book->publisher,
                        'published_on' => Carbon::parse($book->published_on)->toFormattedDateString(),
                        'created_at' => $book->created_at->toDayDateTimeString(),
                        'updated_at' => $book->updated_at->diffForHumans(),
                        'deleted_at' => $book->deleted_at?->toDayDateTimeString(),
                    ],
                ]);
    }

    public function testBookUpdateValidation()
    {
        $book = $this->book();

        $randomBook = $this->book();

        $this->putJson(route('admin.books.update', ['book' => $book->uuid]), [
            'isbn' => $randomBook->isbn, 'title' => $randomBook->title, 'author' => $randomBook->author, 'description' => $randomBook->description, 'publisher' => $randomBook->publisher, 'published_on' => $randomBook->published_on,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'isbn'])
            ->assertJsonMissingValidationErrors(['author', 'description', 'publisher', 'published_on']);
    }

    public function testBookServiceUpdateBookReturnsBook()
    {
        $book = $this->book();
        $data = $this->bookData();

        $book = (new BookService())->updateBook($data, $book->uuid);

        $this->assertInstanceOf(Book::class, $book);
    }

    public function testBookUpdate()
    {
        $book = $this->book();
        $data = $this->bookData();

        $this->putJson(route('admin.books.update', ['book' => $book->uuid]), $data)
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data' => ['id', 'uuid', 'title', 'author', 'description', 'isbn', 'publisher', 'published_on', 'created_at', 'updated_at', 'deleted_at']])
                ->assertJson([
                    'data' => [
                        'id' => $book->id,
                        'uuid' => $book->uuid,
                        'title' => $data['title'],
                        'author' => $data['author'],
                        'description' => $data['description'],
                        'isbn' => $data['isbn'],
                        'publisher' => $data['publisher'],
                        'published_on' => Carbon::parse($data['published_on'])->toFormattedDateString(),
                        'created_at' => $book->created_at->toDayDateTimeString(),
                        'updated_at' => $book->updated_at->diffForHumans(),
                        'deleted_at' => $book->deleted_at?->toDayDateTimeString(),
                    ],
                ]);
    }

    public function testBookServiceDeleteBookReturnsBool()
    {
        $book = $this->book();

        $book = (new BookService())->deleteBook($book->uuid);
        $this->assertTrue($book);
    }

    public function testBookDeletion()
    {
        $book = $this->book();
        $this->deleteJson(route('admin.books.destroy', ['book' => $book->uuid]))
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data' => []]);
    }

    public function testGetAllBooks()
    {
        $this->getJson(route('admin.books.index'))
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data' => [
                    'meta', 'books' => [
                        '*' => [
                            'id', 'uuid', 'title', 'author', 'description', 'isbn', 'publisher', 'published_on', 'created_at', 'updated_at', 'deleted_at',
                        ],
                    ],
                ]]);
    }

    public function testGetAllReturnsPaginatedBooks()
    {
        $perPage = 10;

        $books = (new BookService())->getBooks([], [], compact('perPage'));

        $this->assertInstanceOf(LengthAwarePaginator::class, $books);
    }

    public function testBookIndexPagination()
    {
        $perPage = 10;
        $books = Book::factory()->count(20)->create();

        $this->getJson(route('admin.books.index', compact('perPage')))
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data' => ['meta', 'books' => ['*' => ['id', 'uuid', 'title', 'author', 'description', 'isbn', 'publisher', 'published_on', 'created_at', 'updated_at', 'deleted_at']]]])
                ->assertSee(['per_page' => $perPage])
                ->assertJsonCount($perPage, 'data.books');
    }
}
