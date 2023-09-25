<?php

namespace Tests\Feature\app\Http\Controllert\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ProductControllerTest adds test coverage for {@see ProductController}
 *
 * @package Tests\Unit\app
 * @coversDefaultClass \App\Http\Controllers\Api\ProductController
 */
class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Route prefix for product controller.
     *
     * @var string
     */
    const ROUTE_PREFIX = 'api.products.';

    /**
     * @test
     *
     * @covers ::index
     *
     * @return void
     */
    public function index_returns_a_collection_of_all_products(): void
    {
        // Create a product so that the response returns it.
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->getJson(route(self::ROUTE_PREFIX . 'index'));

        // Status is 200 = OK
        $response->assertOk();

        // Assertion that proves we are getting the required JSON structure of the response.
        $response->assertJson([
            'data' => [
                [
                    'id' => $product->id,
                    'user' => $product->user->name,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'created_at' => $product->created_at->format('d/m/Y'),
                    'updated_at' => $product->updated_at->format('d/m/Y'),
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Api\ProductController::index
     *
     * @return void
     */
    public function index_returns_zero_product_if_there_is_no_products_in_the_db(): void
    {
        // There is no product in the database since we don't want to create them.
        $response = $this->actingAs($this->user)->getJson(route(self::ROUTE_PREFIX . 'index'));

        $response->assertOk();
        $this->assertCount(0, $response->getData()->data);
        $response->assertJson([
            'data' => []
        ]);
    }

    /**
     * @test
     *
     * @covers \App\Http\Controllers\Api\ProductController::show
     *
     * @return void
     */
    public function show_returns_a_single_product_resource_by_id(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson(route(self::ROUTE_PREFIX . 'show', [$product]));

        $response->assertOk();
        $this->assertCount(1, [$response]);
        $response->assertJson([
            'data' => [
                'id' => $product->id,
                'user' => $product->user->name,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'created_at' => $product->created_at->format('d/m/Y'),
                'updated_at' => $product->updated_at->format('d/m/Y'),
            ],
        ]);
    }

    /**
     * @test
     * that returns an appropriate message when there is no product in the database.
     * This test is applied for all methods that have the resource ID in the request:
     * SHOW, UPDATE, DELETE.
     *
     * @covers \App\Http\Controllers\Api\ProductController::show
     *
     * @return void
     */
    public function show_returns_a_message_when_there_is_no_product(): void
    {
        $response = $this->actingAs($this->user)->getJson(route(self::ROUTE_PREFIX . 'show', [1]));

        $response->assertNotFound();
        $response->assertJsonFragment([
            "message" => "Product not found",
        ]);
    }

    /**
     * @test
     *
     * @covers ::store
     *
     * @return void
     */
    public function store_can_create_a_new_product(): void
    {
        // Build a non-persisted product factory model.
        $newProduct = Product::factory()->make([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(
            route(self::ROUTE_PREFIX . 'store'),
            $newProduct->toArray()
        );

        // Assertion that the product has been created with status 201.
        $response->assertCreated();

        // Assert that an appropriate JSON response is returned.
        $product = $response->getData()->data;
        $response->assertJson([
            'data' => [
                'id' => $product->id,
                'user' => $product->user,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ]);

        // The table products contains the newly created product.
        $this->assertDatabaseHas(
            'products',
            $newProduct->toArray()
        );
    }

    /**
     * @test
     *
     * @covers ::store
     *
     * @return void
     */
    public function only_authenticated_user_can_create_product(): void
    {
        $newProduct = Product::factory()->make([
            'user_id' => $this->user->id,
        ]);

        $response = $this->postJson(
            route(self::ROUTE_PREFIX . 'store'),
            $newProduct->toArray()
        );

        $response->assertStatus(401);
        $this->assertGuest();

        $this->assertDatabaseMissing(
            'products',
            $newProduct->toArray()
        );
    }

    /**
     * @test
     *
     * @covers ::store
     *
     * @dataProvider create_product_with_various_possible_values
     *
     * @param string $validatedField
     * @param string|int $brokenRule
     * @param string $expectedMessage
     *
     * @return void
     */
    public function store_with_invalid_request_data_returns_validation_message(
        string $validatedField,
        string|int $brokenRule,
        string $expectedMessage
    ): void {

        $product = Product::factory()->make([
            $validatedField => $brokenRule
        ]);

        $response = $this->actingAs($this->user)->postJson(
            route(self::ROUTE_PREFIX . 'store'),
            $product->toArray()
        );

        $response->assertJsonValidationErrors($validatedField);
        $response->assertJsonFragment([
            'message' => $expectedMessage,
        ]);
    }

    /**
     * Data provider for {@see store_with_invalid_request_data_returns_validation_message}
     *
     * @return array[] of possible cases for validation of input fields.
     */
    public static function create_product_with_various_possible_values(): array
    {
        # Validation test cases for ProductCreateRequest validation.
        return [
            'Name Required' => [
                'validatedField' => 'name',
                'brokenRule' => '',
                'expectedMessage' => 'The name field is required.',
            ],
            'Name Must Be String' => [
                'validatedField' => 'name',
                'brokenRule' => 1234567890,
                'exceptedMessage' => 'The name field must be a string.',
            ],
            'Max 255 Characters For Name' => [
                'validatedField' => 'name',
                'brokenRule' => Str::random(500),
                'expectedMessage' => 'The name field must not be greater than 255 characters.',
            ],
            'Description Required' => [
                'validatedField' => 'description',
                'brokenRule' => '',
                'expectedMessage' => 'The description field is required.',
            ],
            'Description Must Be String' => [
                'validatedField' => 'description',
                'brokenRule' => 1234567890,
                'exceptedMessage' => 'The description field must be a string.',
            ],
            'Max 5000 Characters For Description' => [
                'validatedField' => 'description',
                'brokenRule' => Str::random(5001),
                'expectedMessage' => 'The description field must not be greater than 5000 characters.',
            ],
            'Price Required' => [
                'validatedField' => 'price',
                'brokenRule' => '',
                'expectedMessage' => 'The price field is required.',
            ],
            'Price Must Be Number' => [
                'validatedField' => 'price',
                'brokenRule' => 'price',
                'exceptedMessage' => 'The price field must be a number. (and 1 more error)',
            ],
            'Price Must Be Greater Than 0' => [
                'validatedField' => 'price',
                'brokenRule' => -1,
                'expectedMessage' => 'The price field must be greater than 0.',
            ],
        ];
    }

    /**
     * @test
     *
     * @covers ::update
     *
     * @return void
     */
    public function update_can_update_a_product(): void
    {
        // Update name and description.
        $dataForUpdate = [
            'name' => 'UPDATED NAME',
            'description' => 'Updated description',
        ];

        // Create a new product.
        $existingProduct = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // New data for updating the existing product.
        $newProduct = Product::factory()->make(array_merge([
            'user_id' => $this->user->id,
        ], $dataForUpdate));

        $response = $this->actingAs($this->user)->putJson(
            route(self::ROUTE_PREFIX . 'update', [$existingProduct]),
            $newProduct->toArray()
        );

        $response->assertJson([
            'data' => array_merge([
                // We keep the ID of the existing product.
                'id' => $existingProduct->id,
            ], $dataForUpdate),
        ]);

        // Assertion the db has updated product.
        $this->assertDatabaseHas(
            'products',
            $newProduct->toArray()
        );
    }

    /**
     * @test
     *
     * @covers ::update
     *
     * @return void
     */
    public function only_product_owner_can_update_product(): void
    {
        // Update name and description.
        $dataForUpdate = [
            'name' => 'UPDATED NAME',
            'description' => 'Updated description',
        ];

        // Create a new product.
        $user = User::factory()->create();
        $existingProduct = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        // New data for updating the existing product.
        $newProduct = Product::factory()->make(array_merge([
            'user_id' => $this->user->id,
        ], $dataForUpdate));

        $response = $this->actingAs($this->user)->putJson(
            route(self::ROUTE_PREFIX . 'update', [$existingProduct]),
            $newProduct->toArray()
        );

        $response->assertForbidden();

        // Assertion the db doesn't have updated product.
        $this->assertDatabaseMissing(
            'products',
            $dataForUpdate
        );
    }

    /**
     * @test
     *
     * @covers ::destroy
     *
     * @return void
     */
    public function destroy_can_delete_a_product(): void
    {
        $existingProduct = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route(self::ROUTE_PREFIX . 'destroy', $existingProduct)
        );

        $response->assertNoContent();

        // Asertion that `products` table does not contain
        // the model that has been deleted.
        $this->assertDatabaseMissing(
            'products',
            $existingProduct->toArray()
        );
    }

    /**
     * @test
     *
     * @covers ::destroy
     *
     * @return void
     */
    public function destroy_only_product_owener_can_delete_product(): void
    {
        $user = User::factory()->create();
        $existingProduct = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route(self::ROUTE_PREFIX . 'destroy', $existingProduct)
        );

        $response->assertForbidden();

        // Asertion that the product still exists in the database.
        $existingProduct = $existingProduct->toArray();
        // For some reason, timestamps have a little bit different format at the end of the string.
        // They are not so important for thsi assertion, remove them from the array.
        unset($existingProduct['created_at']);
        unset($existingProduct['updated_at']);
        $this->assertDatabaseHas(
            'products',
            $existingProduct
        );
    }
}
