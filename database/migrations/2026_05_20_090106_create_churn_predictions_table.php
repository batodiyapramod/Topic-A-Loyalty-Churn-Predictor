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
        Schema::dropIfExists('churn_predictions');

        Schema::create('churn_predictions', function (Blueprint $table) {
            $table->id();

            // 1. Create a signed/unsigned standard integer to match parent
            $table->unsignedInteger('loyalty_member_id');

            $table->decimal('predicted_probability', 5, 4);
            $table->text('generated_retention_offer')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('predicted_probability');

            // 2. Assign the foreign key explicitly
            // $table->foreign('loyalty_member_id')
            //     ->references('id')
            //     ->on('loyalty_members')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('churn_predictions');
    }
};
