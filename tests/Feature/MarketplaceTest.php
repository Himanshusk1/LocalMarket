<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Subcategory;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ListingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed categories for all tests
        $this->seed(CategorySeeder::class);
    }

    /**
     * 1. A new user can register, log in, and log out.
     */
    public function test_user_can_register_login_and_logout(): void
    {
        // Registration
        $registerResponse = $this->post('/register', [
            'name' => 'Vikram Singh',
            'email' => 'vikram@example.com',
            'phone' => '+91 98260 99999',
            'city' => 'Gwalior',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $registerResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'vikram@example.com']);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // Login
        $loginResponse = $this->post('/login', [
            'email' => 'vikram@example.com',
            'password' => 'secret123',
        ]);
        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * 2. A guest cannot create, edit, or delete listings.
     */
    public function test_guest_cannot_create_edit_or_delete_listings(): void
    {
        $this->get('/sell')->assertRedirect('/login');
        $this->post('/sell', ['title' => 'Sample Ad'])->assertRedirect('/login');

        $user = User::factory()->create();
        $cat = Category::first();
        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Guest Protected Item',
            'slug' => 'guest-protected-item-1234',
            'description' => 'A protected listing description here.',
            'price' => 1000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        $this->get("/listings/{$listing->id}/edit")->assertRedirect('/login');
        $this->put("/listings/{$listing->id}", ['title' => 'Updated'])->assertRedirect('/login');
        $this->delete("/listings/{$listing->id}")->assertRedirect('/login');
    }

    /**
     * 3. An authenticated user can publish a valid product listing.
     */
    public function test_authenticated_user_can_publish_product_listing(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::where('slug', 'electronics-appliances')->first();
        $subcategory = $category->subcategories->first();

        $image = UploadedFile::fake()->image('laptop.jpg', 600, 400);

        $response = $this->actingAs($user)->post('/sell', [
            'listing_type' => 'product',
            'title' => 'Dell XPS 13 Core i7 16GB RAM',
            'description' => 'Mint condition Dell XPS 13 laptop with box and original 65W charger.',
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 54000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'area' => 'City Center',
            'contact_phone' => '+91 98260 11111',
            'images' => [$image],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('listings', [
            'user_id' => $user->id,
            'listing_type' => 'product',
            'title' => 'Dell XPS 13 Core i7 16GB RAM',
            'city' => 'Gwalior',
            'price' => 54000,
        ]);

        $listing = Listing::where('title', 'Dell XPS 13 Core i7 16GB RAM')->first();
        $this->assertNotNull($listing);
        $this->assertCount(1, $listing->images);
    }

    /**
     * 4. An authenticated user can publish a service listing.
     */
    public function test_authenticated_user_can_publish_service_listing(): void
    {
        $user = User::factory()->create();
        $category = Category::where('slug', 'education-tutoring')->first();

        $response = $this->actingAs($user)->post('/sell', [
            'listing_type' => 'service',
            'title' => 'Personal Mathematics Coaching for Board Exams',
            'description' => 'Offering 1-on-1 home and online maths tutoring for Class 10 and 12 CBSE students.',
            'category_id' => $category->id,
            'price' => 700,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'area' => 'Morar',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('listings', [
            'user_id' => $user->id,
            'listing_type' => 'service',
            'title' => 'Personal Mathematics Coaching for Board Exams',
            'city' => 'Gwalior',
        ]);
    }

    /**
     * 5. Invalid form submissions display validation errors.
     */
    public function test_invalid_listing_submission_fails_with_errors(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/sell', [
            'listing_type' => 'invalid_type',
            'title' => 'Hi', // too short
            'description' => 'short', // min 20 chars
            'price' => -100, // cannot be negative
            'country' => '',
            'state' => '',
            'city' => '',
        ]);

        $response->assertSessionHasErrors([
            'listing_type',
            'title',
            'description',
            'category_id',
            'price',
            'country',
            'state',
            'city',
        ]);
    }

    /**
     * 6. Category filtering returns correct results (/category/{slug}).
     */
    public function test_category_filtering_returns_correct_results(): void
    {
        $user = User::factory()->create();
        $electronics = Category::where('slug', 'electronics-appliances')->first();
        $vehicles = Category::where('slug', 'vehicles')->first();

        $laptop = Listing::create([
            'user_id' => $user->id,
            'category_id' => $electronics->id,
            'listing_type' => 'product',
            'title' => 'HP Pavilion Laptop 15',
            'slug' => 'hp-pavilion-laptop-15-abc',
            'description' => 'HP pavilion core i5 laptop in good condition.',
            'price' => 32000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        $car = Listing::create([
            'user_id' => $user->id,
            'category_id' => $vehicles->id,
            'listing_type' => 'product',
            'title' => 'Hyundai i20 Magna 2019',
            'slug' => 'hyundai-i20-magna-2019-xyz',
            'description' => 'Hyundai i20 single owner petrol car.',
            'price' => 450000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        $response = $this->get('/category/electronics-appliances');
        $response->assertStatus(200);
        $response->assertSee('HP Pavilion Laptop 15');
        $response->assertDontSee('Hyundai i20 Magna 2019');
    }

    /**
     * 7. City filtering returns correct results (/city/{city}).
     */
    public function test_city_filtering_returns_correct_results(): void
    {
        $user = User::factory()->create();
        $cat = Category::first();

        Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Item in Gwalior',
            'slug' => 'item-in-gwalior-1',
            'description' => 'This is an item situated in Gwalior city.',
            'price' => 1500,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Item in Mumbai',
            'slug' => 'item-in-mumbai-2',
            'description' => 'This is an item situated in Mumbai city.',
            'price' => 2500,
            'country' => 'India',
            'state' => 'Maharashtra',
            'city' => 'Mumbai',
            'status' => 'active',
        ]);

        $response = $this->get('/city/gwalior');
        $response->assertStatus(200);
        $response->assertSee('Item in Gwalior');
        $response->assertDontSee('Item in Mumbai');
    }

    /**
     * 8. City-plus-category filtering applies both conditions (/city/{city}/{category:slug}).
     */
    public function test_city_plus_category_filtering_applies_both_conditions(): void
    {
        $user = User::factory()->create();
        $electronics = Category::where('slug', 'electronics-appliances')->first();
        $vehicles = Category::where('slug', 'vehicles')->first();

        // Match both: Gwalior + Electronics
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $electronics->id,
            'listing_type' => 'product',
            'title' => 'Sony TV in Gwalior',
            'slug' => 'sony-tv-gwalior-1',
            'description' => 'Sony Bravia smart tv in Gwalior city.',
            'price' => 22000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Same city, different category: Gwalior + Vehicles (Should not appear)
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $vehicles->id,
            'listing_type' => 'product',
            'title' => 'Honda Activa in Gwalior',
            'slug' => 'honda-activa-gwalior-2',
            'description' => 'Activa 5G in Gwalior city.',
            'price' => 38000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Different city, same category: Delhi + Electronics (Should not appear)
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $electronics->id,
            'listing_type' => 'product',
            'title' => 'Sony TV in Delhi',
            'slug' => 'sony-tv-delhi-3',
            'description' => 'Sony Bravia tv in Delhi city.',
            'price' => 24000,
            'country' => 'India',
            'state' => 'Delhi',
            'city' => 'Delhi',
            'status' => 'active',
        ]);

        $response = $this->get('/city/gwalior/electronics-appliances');
        $response->assertStatus(200);
        $response->assertSee('Sony TV in Gwalior');
        $response->assertDontSee('Honda Activa in Gwalior');
        $response->assertDontSee('Sony TV in Delhi');
    }

    /**
     * 9. Users cannot modify another user's listing by changing URL.
     */
    public function test_user_cannot_edit_or_delete_another_users_listing(): void
    {
        $owner = User::factory()->create(['email' => 'owner@example.com']);
        $attacker = User::factory()->create(['email' => 'attacker@example.com']);
        $category = Category::first();

        $listing = Listing::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'listing_type' => 'product',
            'title' => 'Owner Private Listing',
            'slug' => 'owner-private-listing-999',
            'description' => 'Listing belonging exclusively to owner.',
            'price' => 9999,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Attacker attempts to access edit form -> Forbidden 403
        $this->actingAs($attacker)
            ->get("/listings/{$listing->id}/edit")
            ->assertStatus(403);

        // Attacker attempts to update -> Forbidden 403
        $this->actingAs($attacker)
            ->put("/listings/{$listing->id}", [
                'listing_type' => 'product',
                'title' => 'Hacked Title',
                'description' => 'Hacked description longer than 20 chars.',
                'category_id' => $category->id,
                'price' => 1,
                'country' => 'India',
                'state' => 'MP',
                'city' => 'Gwalior',
                'status' => 'active',
            ])
            ->assertStatus(403);

        // Attacker attempts to delete -> Forbidden 403
        $this->actingAs($attacker)
            ->delete("/listings/{$listing->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'title' => 'Owner Private Listing']);
    }

    /**
     * 10. Owner can update and delete their own listing.
     */
    public function test_owner_can_update_and_delete_own_listing(): void
    {
        $owner = User::factory()->create();
        $category = Category::first();

        $listing = Listing::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'listing_type' => 'product',
            'title' => 'Original Title 123',
            'slug' => 'original-title-123',
            'description' => 'Original description for testing update.',
            'price' => 5000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Edit form returns 200
        $this->actingAs($owner)->get("/listings/{$listing->id}/edit")->assertStatus(200);

        // Update succeeds
        $this->actingAs($owner)->put("/listings/{$listing->id}", [
            'listing_type' => 'product',
            'title' => 'Updated Title by Owner',
            'description' => 'Updated description with more than 20 characters.',
            'category_id' => $category->id,
            'price' => 4500,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'title' => 'Updated Title by Owner',
            'price' => 4500,
        ]);

        // Delete succeeds
        $this->actingAs($owner)->delete("/listings/{$listing->id}")
            ->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
    }

    /**
     * 11. Invalid category slug returns 404.
     */
    public function test_invalid_category_returns_404(): void
    {
        $this->get('/category/non-existent-category-slug-999')->assertStatus(404);
    }

    /**
     * 12. Combined keyword, price range, and product/service filters work together.
     */
    public function test_combined_filters_work_together(): void
    {
        $user = User::factory()->create();
        $cat = Category::first();

        // Target match: Product, Title containing "iPhone", Price 45000 (between 40000 and 50000)
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Apple iPhone 13 128GB Blue',
            'slug' => 'apple-iphone-13-128gb-blue-test',
            'description' => 'Great condition iPhone 13 in Blue with battery health 90 percent.',
            'price' => 45000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Wrong price: 65000 (above max 50000)
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Apple iPhone 15 Pro Max',
            'slug' => 'apple-iphone-15-pro-max-test',
            'description' => 'Brand new sealed pack Apple iPhone 15 Pro Max.',
            'price' => 65000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        // Wrong type: Service
        Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'service',
            'title' => 'iPhone Screen Repair Service',
            'slug' => 'iphone-screen-repair-service-test',
            'description' => 'Fast doorstep iPhone screen and battery replacement service.',
            'price' => 4500,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        $response = $this->get('/browse?q=iPhone&type=product&min_price=40000&max_price=50000');
        $response->assertStatus(200);
        $response->assertSee('Apple iPhone 13 128GB Blue');
        $response->assertDontSee('Apple iPhone 15 Pro Max');
        $response->assertDontSee('iPhone Screen Repair Service');
    }

    /**
     * 13. Dashboard metrics and status toggle (Active <-> Sold).
     */
    public function test_dashboard_status_toggle(): void
    {
        $user = User::factory()->create();
        $cat = Category::first();

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'listing_type' => 'product',
            'title' => 'Motorola Edge 50 Pro',
            'slug' => 'motorola-edge-50-pro-test',
            'description' => 'Curved pOLED smartphone with 125W charger.',
            'price' => 24000,
            'country' => 'India',
            'state' => 'Madhya Pradesh',
            'city' => 'Gwalior',
            'status' => 'active',
        ]);

        $this->actingAs($user)->get('/dashboard')->assertStatus(200)->assertSee('Motorola Edge 50 Pro');

        // Toggle to sold
        $this->actingAs($user)->post("/dashboard/listings/{$listing->id}/toggle-status")
            ->assertSessionHas('success');

        $this->assertEquals('sold', $listing->fresh()->status);

        // Toggle back to active
        $this->actingAs($user)->post("/dashboard/listings/{$listing->id}/toggle-status")
            ->assertSessionHas('success');

        $this->assertEquals('active', $listing->fresh()->status);
    }
}
