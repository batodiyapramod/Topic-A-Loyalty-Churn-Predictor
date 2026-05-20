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
        Schema::create('loyalty_members', function (Blueprint $table) {
            $table->id();
            $table->string('tier'); // Bronze, Silver, Gold, Platinum
            $table->integer('tenure_months');
            $table->integer('visits_30d');
            $table->decimal('spend_30d', 10, 2);
            $table->timestamp('last_visit_at');
            $table->boolean('churned')->default(false); // Label column
            $table->timestamps();

            // Performance Indexes
            $table->index(['tier', 'churned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_members');
    }
};
