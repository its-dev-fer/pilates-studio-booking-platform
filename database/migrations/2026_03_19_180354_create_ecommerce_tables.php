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
        if (! Schema::hasTable('store_sections')) {
            Schema::create('store_sections', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. CATEGORÍAS
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'store_section_id')) {
                $table->foreignId('store_section_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('categories', 'photo')) {
                $table->string('photo')->nullable();
            }
        });

        // 3. PRODUCTOS
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->unique();
            }
            if (! Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('products', 'promo_start_date')) {
                $table->dateTime('promo_start_date')->nullable();
            }
            if (! Schema::hasColumn('products', 'promo_end_date')) {
                $table->dateTime('promo_end_date')->nullable();
            }
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->unique();
            }
            if (! Schema::hasColumn('products', 'variants')) {
                $table->json('variants')->nullable(); // Ej: {"Talla": ["S", "M"], "Color": ["Rojo", "Azul"]}
            }
            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 4. TABLA PIVOTE (Productos <-> Categorías)
        if (! Schema::hasTable('category_product')) {
            Schema::create('category_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            });
        }

        // 5. CARRITO DE COMPRAS (Soporta Invitados y Panel POS)
        if (! Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); // Sucursal donde se armó
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // Nulo si es invitado
                $table->string('session_id')->nullable()->index(); // Para identificar invitados
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->json('variant_selected')->nullable(); // Ej: {"Talla": "M", "Color": "Rojo"}
                $table->timestamps();
            });
        }

        // 6. ÓRDENES / PEDIDOS
        Schema::table('orders', function (Blueprint $table) {
            // Datos para invitados (o respaldo de clientes)
            if (! Schema::hasColumn('orders', 'guest_name')) {
                $table->string('guest_name')->nullable();
            }
            if (! Schema::hasColumn('orders', 'guest_email')) {
                $table->string('guest_email')->nullable();
            }
            if (! Schema::hasColumn('orders', 'guest_phone')) {
                $table->string('guest_phone')->nullable();
            }

            // Logística
            if (! Schema::hasColumn('orders', 'delivery_type')) {
                $table->enum('delivery_type', ['sucursal', 'domicilio'])->default('sucursal');
            }
            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }

            // Estatus y Pago
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['efectivo', 'transferencia', 'en_linea'])->nullable();
            }
            if (! Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable(); // Folio de transferencia o terminal
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'product_title')) {
                $table->string('product_title'); // Guardamos el nombre por si el producto se borra en el futuro
            }
            if (! Schema::hasColumn('order_items', 'variant_selected')) {
                $table->json('variant_selected')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('category_product');

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_title')) {
                $table->dropColumn('product_title');
            }
            if (Schema::hasColumn('order_items', 'variant_selected')) {
                $table->dropColumn('variant_selected');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
            if (Schema::hasColumn('orders', 'guest_email')) {
                $table->dropColumn('guest_email');
            }
            if (Schema::hasColumn('orders', 'guest_phone')) {
                $table->dropColumn('guest_phone');
            }
            if (Schema::hasColumn('orders', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
            if (Schema::hasColumn('orders', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('orders', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('products', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
            if (Schema::hasColumn('products', 'promo_start_date')) {
                $table->dropColumn('promo_start_date');
            }
            if (Schema::hasColumn('products', 'promo_end_date')) {
                $table->dropColumn('promo_end_date');
            }
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropColumn('sku');
            }
            if (Schema::hasColumn('products', 'variants')) {
                $table->dropColumn('variants');
            }
            if (Schema::hasColumn('products', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('products', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'store_section_id')) {
                $table->dropForeign(['store_section_id']);
                $table->dropColumn('store_section_id');
            }
            if (Schema::hasColumn('categories', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('categories', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::dropIfExists('store_sections');
    }
};
