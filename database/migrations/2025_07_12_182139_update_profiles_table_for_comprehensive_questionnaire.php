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
        Schema::table('profiles', function (Blueprint $table) {
            // Drop old fields that are being replaced
            $table->dropColumn([
                'location',
                'professional_role',
                'mindset_traits',
                'conviction_traits',
                'communication_general',
                'characteristic_expressions',
                'normal_mode',
                'passion_mode',
                'post_passion',
                'fundamental_principles',
                'work_values',
                'technical_discussions',
                'problem_handling',
                'team_interaction',
                'hobbies_interests',
                'fears_concerns',
                'goals_aspirations',
                'background_story'
            ]);
            
            // Section 1: Basic Information and Context
            $table->string('full_name')->nullable();
            $table->string('nicknames')->nullable();
            $table->string('age_range')->nullable();
            $table->string('current_location')->nullable();
            $table->text('influential_places')->nullable();
            $table->string('current_role')->nullable();
            $table->integer('years_experience')->nullable();
            $table->text('elevator_pitch')->nullable();
            
            // Section 2: Core Personality and Dualities
            $table->json('core_personality_traits')->nullable();
            $table->string('personality_consistency')->nullable();
            $table->text('default_mode')->nullable();
            $table->string('has_alternative_modes')->nullable();
            $table->text('alternative_mode_description')->nullable();
            
            // Section 3: Passions and Motivations
            $table->text('passions')->nullable();
            $table->json('passion_mode_changes')->nullable();
            $table->text('passion_transformation_example')->nullable();
            
            // Section 4: Detailed Communication Style
            $table->string('communication_style')->nullable();
            $table->string('profanity_usage')->nullable();
            $table->text('favorite_profanity')->nullable();
            $table->text('verbal_tics')->nullable();
            $table->string('analogy_usage')->nullable();
            $table->text('analogy_examples')->nullable();
            
            // Section 5: Values, Principles and Philosophy
            $table->json('core_values')->nullable();
            $table->text('life_principles')->nullable();
            $table->text('work_philosophy')->nullable();
            $table->text('failure_philosophy')->nullable();
            $table->json('intolerances')->nullable();
            
            // Section 6: Professional Behavior and Leadership
            $table->json('meeting_behavior')->nullable();
            $table->text('problem_solving_approach')->nullable();
            $table->text('conflict_handling')->nullable();
            $table->text('pressure_reaction')->nullable();
            
            // Section 7: Emotional Intelligence and Relationships
            $table->json('emotional_support_style')->nullable();
            $table->text('trust_building')->nullable();
            $table->text('emotion_management')->nullable();
            
            // Section 8: Humor and Social Personality
            $table->integer('humor_importance')->nullable();
            $table->string('humor_style')->nullable();
            $table->string('humor_as_defense')->nullable();
            $table->text('humor_examples')->nullable();
            
            // Section 9: Reactions and Specific Behaviors
            $table->text('reaction_to_lies')->nullable();
            $table->text('reaction_to_own_mistakes')->nullable();
            $table->text('reaction_to_others_mistakes')->nullable();
            $table->text('emergency_behavior')->nullable();
            $table->text('reaction_to_unfair_criticism')->nullable();
            $table->text('delivering_bad_news')->nullable();
            
            // Section 10: Peculiarities and Unique Details
            $table->text('characteristic_gestures')->nullable();
            $table->text('personality_contradictions')->nullable();
            $table->text('superpower')->nullable();
            $table->text('kryptonite')->nullable();
            
            // Section 11: Final Reflection and Metacognition
            $table->text('impersonation_key')->nullable();
            
            // Update total questions count
            $table->integer('current_question')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Restore old structure
            $table->dropColumn([
                'full_name', 'nicknames', 'age_range', 'current_location', 'influential_places',
                'current_role', 'years_experience', 'elevator_pitch', 'core_personality_traits',
                'personality_consistency', 'default_mode', 'has_alternative_modes',
                'alternative_mode_description', 'passions', 'passion_mode_changes',
                'passion_transformation_example', 'communication_style', 'profanity_usage',
                'favorite_profanity', 'verbal_tics', 'analogy_usage', 'analogy_examples',
                'core_values', 'life_principles', 'work_philosophy', 'failure_philosophy',
                'intolerances', 'meeting_behavior', 'problem_solving_approach',
                'conflict_handling', 'pressure_reaction', 'emotional_support_style',
                'trust_building', 'emotion_management', 'humor_importance', 'humor_style',
                'humor_as_defense', 'humor_examples', 'reaction_to_lies',
                'reaction_to_own_mistakes', 'reaction_to_others_mistakes', 'emergency_behavior',
                'reaction_to_unfair_criticism', 'delivering_bad_news', 'characteristic_gestures',
                'personality_contradictions', 'superpower', 'kryptonite', 'impersonation_key'
            ]);
            
            // Restore old fields
            $table->string('location')->nullable();
            $table->string('professional_role')->nullable();
            $table->text('personality_summary')->nullable();
            $table->json('mindset_traits')->nullable();
            $table->json('conviction_traits')->nullable();
            $table->text('communication_general')->nullable();
            $table->json('characteristic_expressions')->nullable();
            $table->text('normal_mode')->nullable();
            $table->text('passion_mode')->nullable();
            $table->text('post_passion')->nullable();
            $table->json('fundamental_principles')->nullable();
            $table->text('work_values')->nullable();
            $table->text('technical_discussions')->nullable();
            $table->text('problem_handling')->nullable();
            $table->text('team_interaction')->nullable();
            $table->json('hobbies_interests')->nullable();
            $table->json('fears_concerns')->nullable();
            $table->json('goals_aspirations')->nullable();
            $table->text('background_story')->nullable();
        });
    }
};