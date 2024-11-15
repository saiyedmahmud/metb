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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoiceCategoryId');
            $table->string('categoryName');
            $table->date('date');
            $table->double('amount' , 10, 2);
            $table->unsignedBigInteger('createdBy');
            $table->string('donnerName')->nullable();
            $table->string('status')->default('true');

            $table->foreign('invoiceCategoryId')->references('id')->on('invoiceCategory');
            $table->foreign('createdBy')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
