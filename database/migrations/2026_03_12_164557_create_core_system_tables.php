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
        // 1. Tenants (Sucursales)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->integer('max_appointments_per_day')->default(20);
            $table->json('business_hours')->nullable(); // Para horarios y días cerrados
            $table->timestamps();
        });

        // 2. Pivot User-Tenant
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Catálogo de Paquetes de Créditos
        Schema::create('credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('credits_amount');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // 4. Créditos de Usuarios
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->integer('balance');
            $table->timestamp('expires_at');
            $table->boolean('is_special')->default(false);
            $table->timestamps();
        });

        // 5. Citas (Appointments)
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time_slot');
            $table->string('status')->default('scheduled'); // scheduled, cancelled, completed
            $table->timestamps();
        });

        // 6. E-commerce: Categorías
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        // 7. E-commerce: Productos (Con UUID)
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->json('images')->nullable();
            $table->timestamps();
        });

        // 8. E-commerce: Órdenes
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_fee', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['creado', 'pagado', 'empacado', 'entregado', 'cancelado'])->default('creado');
            $table->timestamps();
        });

        // 9. E-commerce: Detalle de Órdenes
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('user_credits');
        Schema::dropIfExists('credit_packages');
        Schema::dropIfExists('tenant_user');
        Schema::dropIfExists('tenants');

        Schema::enableForeignKeyConstraints();
    }
};
