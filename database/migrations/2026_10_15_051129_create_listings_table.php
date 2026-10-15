<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('listing_type', ['product', 'service'])->default('product');
            $table->string('title', 150);
            $table->string('slug', 191)->index();
            $table->text('description');
            $table->decimal('price', 12, 2)->unsigned()->default(0);
            $table->string('country', 100)->default('India');
            $table->string('state', 100);
            $table->string('city', 100)->index();
            $table->string('area', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index(['city', 'category_id']);
            $table->index(['status', 'created_at']);
            $table->index(['price', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
