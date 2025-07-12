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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('professional_role')->nullable();
            $table->text('personality_summary')->nullable();
            
            // Mindset and thinking
            $table->json('mindset_traits')->nullable();
            $table->json('conviction_traits')->nullable();
            
            // Communication style
            $table->text('communication_general')->nullable();
            $table->json('characteristic_expressions')->nullable();
            
            // Personality modes
            $table->text('normal_mode')->nullable();
            $table->text('passion_mode')->nullable();
            $table->text('post_passion')->nullable();
            
            // Values and philosophy
            $table->json('fundamental_principles')->nullable();
            $table->text('work_values')->nullable();
            
            // Behavior in situations
            $table->text('technical_discussions')->nullable();
            $table->text('problem_handling')->nullable();
            $table->text('team_interaction')->nullable();
            
            // Personal traits
            $table->json('hobbies_interests')->nullable();
            $table->json('fears_concerns')->nullable();
            $table->json('goals_aspirations')->nullable();
            $table->text('background_story')->nullable();
            
            // Generation metadata
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->integer('current_question')->default(1);
            $table->json('responses')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
