<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'current_question',
        'responses',
        // Section 1: Basic Information and Context
        'full_name',
        'nicknames',
        'age_range',
        'current_location',
        'influential_places',
        'current_role',
        'years_experience',
        'personality_summary',
        'elevator_pitch',
        // Section 2: Core Personality and Dualities
        'core_personality_traits',
        'personality_consistency',
        'default_mode',
        'has_alternative_modes',
        'alternative_mode_description',
        // Section 3: Passions and Motivations
        'passions',
        'passion_mode_changes',
        'passion_transformation_example',
        // Section 4: Detailed Communication Style
        'communication_style',
        'profanity_usage',
        'favorite_profanity',
        'verbal_tics',
        'analogy_usage',
        'analogy_examples',
        // Section 5: Values, Principles and Philosophy
        'core_values',
        'life_principles',
        'work_philosophy',
        'failure_philosophy',
        'intolerances',
        // Section 6: Professional Behavior and Leadership
        'meeting_behavior',
        'problem_solving_approach',
        'conflict_handling',
        'pressure_reaction',
        // Section 7: Emotional Intelligence and Relationships
        'emotional_support_style',
        'trust_building',
        'emotion_management',
        // Section 8: Humor and Social Personality
        'humor_importance',
        'humor_style',
        'humor_as_defense',
        'humor_examples',
        // Section 9: Reactions and Specific Behaviors
        'reaction_to_lies',
        'reaction_to_own_mistakes',
        'reaction_to_others_mistakes',
        'emergency_behavior',
        'reaction_to_unfair_criticism',
        'delivering_bad_news',
        // Section 10: Peculiarities and Unique Details
        'characteristic_gestures',
        'personality_contradictions',
        'superpower',
        'kryptonite',
        // Section 11: Final Reflection and Metacognition
        'impersonation_key',
    ];

    protected $casts = [
        'personality_summary' => 'array',
        'core_personality_traits' => 'array',
        'passion_mode_changes' => 'array',
        'core_values' => 'array',
        'intolerances' => 'array',
        'meeting_behavior' => 'array',
        'emotional_support_style' => 'array',
        'responses' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generateMarkdown(): string
    {
        $name = $this->full_name ?: $this->name;
        $markdown = "# Perfil de {$name} - Guía de Impersonación\n\n";
        
        // Introducción y contexto
        $markdown .= "## 📋 Introducción\n\n";
        $markdown .= "Este documento contiene un perfil detallado de personalidad diseñado para capturar los matices, ";
        $markdown .= "peculiaridades y patrones de comportamiento que definen a {$name}. ";
        $markdown .= "El objetivo es proporcionar una guía completa para entender y potencialmente impersonar ";
        $markdown .= "su forma de ser, comunicarse y reaccionar en diferentes situaciones.\n\n";
        
        // Section 1: Basic Information and Context
        $markdown .= "## 📌 Información Básica y Contexto\n\n";
        
        if ($this->full_name || $this->nicknames || $this->age_range || $this->current_location) {
            $markdown .= "### Datos Personales\n";
            if ($this->full_name) {
                $markdown .= "**Nombre completo:** {$this->full_name}\n";
            }
            if ($this->nicknames) {
                $markdown .= "**Apodos:** {$this->nicknames}\n";
            }
            if ($this->age_range) {
                $markdown .= "**Rango de edad:** {$this->age_range}\n";
            }
            if ($this->current_location) {
                $markdown .= "**Ubicación actual:** {$this->current_location}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->influential_places) {
            $markdown .= "### Influencias Geográficas\n";
            $markdown .= $this->influential_places . "\n\n";
        }
        
        if ($this->current_role || $this->years_experience) {
            $markdown .= "### Contexto Profesional\n";
            if ($this->current_role) {
                $markdown .= "**Rol actual:** {$this->current_role}\n";
            }
            if ($this->years_experience) {
                $markdown .= "**Años de experiencia:** {$this->years_experience}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->personality_summary && is_array($this->personality_summary) && count($this->personality_summary) > 0) {
            $markdown .= "### Resumen de Personalidad\n";
            $markdown .= "Palabras clave que definen su personalidad:\n";
            foreach ($this->personality_summary as $trait) {
                $markdown .= "* **{$trait}**\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->elevator_pitch) {
            $markdown .= "### Presentación Personal (30 segundos)\n";
            $markdown .= "> \"{$this->elevator_pitch}\"\n\n";
        }

        // Section 2: Core Personality and Dualities
        $markdown .= "## 🧠 Personalidad Core y Dualidades\n\n";
        
        if ($this->core_personality_traits && is_array($this->core_personality_traits) && count($this->core_personality_traits) > 0) {
            $markdown .= "### Rasgos Principales de Personalidad\n";
            foreach ($this->core_personality_traits as $trait) {
                $markdown .= "* **{$trait}**\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->personality_consistency) {
            $markdown .= "### Consistencia de Personalidad\n";
            $markdown .= "**¿Cambia según el contexto?** {$this->personality_consistency}\n\n";
        }
        
        if ($this->default_mode) {
            $markdown .= "### Modo por Defecto\n";
            $markdown .= "**Cómo es la mayor parte del tiempo:**\n";
            $markdown .= $this->default_mode . "\n\n";
        }
        
        if ($this->has_alternative_modes && $this->alternative_mode_description) {
            $markdown .= "### Modos Alternativos\n";
            $markdown .= "**¿Tiene modos alternativos?** {$this->has_alternative_modes}\n\n";
            $markdown .= "**Descripción del modo alternativo:**\n";
            $markdown .= $this->alternative_mode_description . "\n\n";
        }

        // Section 3: Passions and Motivations
        if ($this->passions) {
            $markdown .= "## 🔥 Pasiones y Motivaciones\n\n";
            
            $markdown .= "### Temas que le Apasionan\n";
            $markdown .= $this->passions . "\n\n";
            
            if ($this->passion_mode_changes && is_array($this->passion_mode_changes) && count($this->passion_mode_changes) > 0) {
                $markdown .= "### Cambios al Hablar de Pasiones\n";
                $markdown .= "Cuando habla de sus pasiones experimenta:\n";
                foreach ($this->passion_mode_changes as $change) {
                    $markdown .= "* {$change}\n";
                }
                $markdown .= "\n";
            }
            
            if ($this->passion_transformation_example) {
                $markdown .= "### Ejemplo de Transformación\n";
                $markdown .= $this->passion_transformation_example . "\n\n";
            }
        }

        // Section 4: Detailed Communication Style
        $markdown .= "## 💬 Estilo de Comunicación Detallado\n\n";
        
        if ($this->communication_style) {
            $markdown .= "### Estilo General\n";
            $markdown .= "**Enfoque comunicativo:** {$this->communication_style}\n\n";
        }
        
        if ($this->profanity_usage) {
            $markdown .= "### Uso de Palabrotas\n";
            $markdown .= "**Frecuencia:** {$this->profanity_usage}\n";
            if ($this->favorite_profanity) {
                $markdown .= "**Detalles específicos:** {$this->favorite_profanity}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->verbal_tics) {
            $markdown .= "### Muletillas y Frases Recurrentes\n";
            $markdown .= $this->verbal_tics . "\n\n";
        }
        
        if ($this->analogy_usage) {
            $markdown .= "### Uso de Analogías\n";
            $markdown .= "**Frecuencia:** {$this->analogy_usage}\n";
            if ($this->analogy_examples) {
                $markdown .= "**Ejemplos:**\n";
                $markdown .= $this->analogy_examples . "\n";
            }
            $markdown .= "\n";
        }

        // Section 5: Values, Principles and Philosophy
        $markdown .= "## 🛡️ Valores, Principios y Filosofía\n\n";
        
        if ($this->core_values && is_array($this->core_values) && count($this->core_values) > 0) {
            $markdown .= "### Valores Fundamentales (en orden de importancia)\n";
            foreach ($this->core_values as $index => $value) {
                $markdown .= ($index + 1) . ". **{$value}**\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->life_principles) {
            $markdown .= "### Principios de Vida\n";
            $markdown .= $this->life_principles . "\n\n";
        }
        
        if ($this->work_philosophy) {
            $markdown .= "### Filosofía del Trabajo\n";
            $markdown .= $this->work_philosophy . "\n\n";
        }
        
        if ($this->failure_philosophy) {
            $markdown .= "### Filosofía sobre el Fracaso y los Errores\n";
            $markdown .= $this->failure_philosophy . "\n\n";
        }
        
        if ($this->intolerances && is_array($this->intolerances) && count($this->intolerances) > 0) {
            $markdown .= "### Cosas que NO Tolera\n";
            foreach ($this->intolerances as $intolerance) {
                $markdown .= "* **{$intolerance}**\n";
            }
            $markdown .= "\n";
        }

        // Section 6: Professional Behavior and Leadership
        $markdown .= "## 🎯 Comportamiento Profesional y Liderazgo\n\n";
        
        if ($this->meeting_behavior && is_array($this->meeting_behavior) && count($this->meeting_behavior) > 0) {
            $markdown .= "### Comportamiento en Reuniones\n";
            $markdown .= "En reuniones es quien:\n";
            foreach ($this->meeting_behavior as $behavior) {
                $markdown .= "* {$behavior}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->problem_solving_approach) {
            $markdown .= "### Enfoque para Resolver Problemas\n";
            $markdown .= $this->problem_solving_approach . "\n\n";
        }
        
        if ($this->conflict_handling) {
            $markdown .= "### Manejo de Conflictos\n";
            $markdown .= $this->conflict_handling . "\n\n";
        }
        
        if ($this->pressure_reaction) {
            $markdown .= "### Reacción Bajo Presión\n";
            $markdown .= $this->pressure_reaction . "\n\n";
        }

        // Section 7: Emotional Intelligence and Relationships
        $markdown .= "## ❤️ Inteligencia Emocional y Relaciones\n\n";
        
        if ($this->emotional_support_style && is_array($this->emotional_support_style) && count($this->emotional_support_style) > 0) {
            $markdown .= "### Estilo de Apoyo Emocional\n";
            $markdown .= "Cuando alguien está mal:\n";
            foreach ($this->emotional_support_style as $style) {
                $markdown .= "* {$style}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->trust_building) {
            $markdown .= "### Construcción de Confianza\n";
            $markdown .= $this->trust_building . "\n\n";
        }
        
        if ($this->emotion_management) {
            $markdown .= "### Manejo de Emociones Propias\n";
            $markdown .= $this->emotion_management . "\n\n";
        }

        // Section 8: Humor and Social Personality
        $markdown .= "## 😄 Humor y Personalidad Social\n\n";
        
        if ($this->humor_importance) {
            $markdown .= "### Importancia del Humor\n";
            $markdown .= "**Nivel de importancia (1-10):** {$this->humor_importance}/10\n\n";
        }
        
        if ($this->humor_style) {
            $markdown .= "### Tipo de Humor Principal\n";
            $markdown .= "**Estilo:** {$this->humor_style}\n\n";
        }
        
        if ($this->humor_as_defense) {
            $markdown .= "### Humor como Mecanismo de Defensa\n";
            $markdown .= "**Frecuencia:** {$this->humor_as_defense}\n\n";
        }
        
        if ($this->humor_examples) {
            $markdown .= "### Ejemplos de Humor en Diferentes Situaciones\n";
            $markdown .= $this->humor_examples . "\n\n";
        }

        // Section 9: Reactions and Specific Behaviors
        $markdown .= "## ⚡ Reacciones y Comportamientos Específicos\n\n";
        
        if ($this->reaction_to_lies) {
            $markdown .= "### Cuando Alguien le Miente\n";
            $markdown .= $this->reaction_to_lies . "\n\n";
        }
        
        if ($this->reaction_to_own_mistakes) {
            $markdown .= "### Cuando Comete un Error Grave\n";
            $markdown .= $this->reaction_to_own_mistakes . "\n\n";
        }
        
        if ($this->reaction_to_others_mistakes) {
            $markdown .= "### Cuando Alguien que Supervisa Comete un Error\n";
            $markdown .= $this->reaction_to_others_mistakes . "\n\n";
        }
        
        if ($this->emergency_behavior) {
            $markdown .= "### En una Emergencia\n";
            $markdown .= $this->emergency_behavior . "\n\n";
        }
        
        if ($this->reaction_to_unfair_criticism) {
            $markdown .= "### Ante Críticas Injustas\n";
            $markdown .= $this->reaction_to_unfair_criticism . "\n\n";
        }
        
        if ($this->delivering_bad_news) {
            $markdown .= "### Al Dar Malas Noticias\n";
            $markdown .= $this->delivering_bad_news . "\n\n";
        }

        // Section 10: Peculiarities and Unique Details
        $markdown .= "## ✨ Peculiaridades y Detalles Únicos\n\n";
        
        if ($this->characteristic_gestures) {
            $markdown .= "### Gestos y Tics Característicos\n";
            $markdown .= $this->characteristic_gestures . "\n\n";
        }
        
        if ($this->personality_contradictions) {
            $markdown .= "### Contradicciones en su Personalidad\n";
            $markdown .= $this->personality_contradictions . "\n\n";
        }
        
        if ($this->superpower) {
            $markdown .= "### Su \"Superpoder\" Social/Profesional\n";
            $markdown .= $this->superpower . "\n\n";
        }
        
        if ($this->kryptonite) {
            $markdown .= "### Su \"Kryptonita\" Personal\n";
            $markdown .= $this->kryptonite . "\n\n";
        }

        // Section 11: Final Reflection and Metacognition
        if ($this->impersonation_key) {
            $markdown .= "## 🎭 Clave para la Impersonación\n\n";
            $markdown .= "### Lo Más Importante para Impersonarle\n";
            $markdown .= $this->impersonation_key . "\n\n";
        }

        // Footer
        $markdown .= "---\n\n";
        $markdown .= "*Perfil generado el " . date('d/m/Y') . " a través del sistema de cuestionario ";
        $markdown .= "de personalidad de 50 preguntas estructuradas.*\n\n";
        $markdown .= "**Nota:** Este perfil captura patrones de comportamiento y personalidad en un momento ";
        $markdown .= "específico. Las personas evolucionan y pueden cambiar con el tiempo.";

        return $markdown;
    }

    public function generateChatProfile(): string
    {
        $name = $this->full_name ?: $this->name;
        $markdown = "# Perfil de Personalidad: {$name}\n\n";
        $markdown .= "**INSTRUCCIONES:** Actúa como {$name}. Este perfil contiene información detallada sobre su personalidad, forma de comunicarse, valores, reacciones y comportamientos. Usa esta información para responder como lo haría esta persona específica.\n\n";
        
        // Core Identity
        if ($this->personality_summary && is_array($this->personality_summary) && count($this->personality_summary) > 0) {
            $markdown .= "## Esencia de la Personalidad\n";
            $markdown .= "**Rasgos principales:** " . implode(', ', $this->personality_summary) . "\n\n";
        }
        
        if ($this->elevator_pitch) {
            $markdown .= "**Autopresentación:** \"{$this->elevator_pitch}\"\n\n";
        }

        // Communication Style
        $markdown .= "## Estilo de Comunicación\n";
        
        if ($this->communication_style) {
            $markdown .= "**Enfoque:** {$this->communication_style}\n";
        }
        
        if ($this->profanity_usage && $this->profanity_usage !== 'Nunca, me parece inapropiado') {
            $markdown .= "**Uso de palabrotas:** {$this->profanity_usage}";
            if ($this->favorite_profanity) {
                $markdown .= " - {$this->favorite_profanity}";
            }
            $markdown .= "\n";
        }
        
        if ($this->verbal_tics) {
            $markdown .= "**Muletillas frecuentes:** {$this->verbal_tics}\n";
        }
        
        if ($this->analogy_usage && $this->analogy_usage !== 'Nunca') {
            $markdown .= "**Uso de analogías:** {$this->analogy_usage}";
            if ($this->analogy_examples) {
                $markdown .= " - Ejemplos: {$this->analogy_examples}";
            }
            $markdown .= "\n";
        }
        $markdown .= "\n";

        // Personality Modes
        if ($this->default_mode) {
            $markdown .= "## Personalidad\n";
            $markdown .= "**Modo habitual:** {$this->default_mode}\n";
            
            if ($this->alternative_mode_description) {
                $markdown .= "**Modo alternativo:** {$this->alternative_mode_description}\n";
            }
            $markdown .= "\n";
        }

        // Passions and Transformations
        if ($this->passions) {
            $markdown .= "## Pasiones\n";
            $markdown .= "**Temas que le apasionan:** {$this->passions}\n";
            
            if ($this->passion_transformation_example) {
                $markdown .= "**Cómo cambia al hablar de pasiones:** {$this->passion_transformation_example}\n";
            }
            $markdown .= "\n";
        }

        // Values and Philosophy
        if ($this->core_values && is_array($this->core_values) && count($this->core_values) > 0) {
            $markdown .= "## Valores Fundamentales\n";
            foreach ($this->core_values as $index => $value) {
                $markdown .= ($index + 1) . ". {$value}\n";
            }
            $markdown .= "\n";
        }
        
        if ($this->work_philosophy) {
            $markdown .= "**Filosofía del trabajo:** {$this->work_philosophy}\n\n";
        }
        
        if ($this->failure_philosophy) {
            $markdown .= "**Ante el fracaso:** {$this->failure_philosophy}\n\n";
        }

        // Professional Behavior
        if ($this->meeting_behavior && is_array($this->meeting_behavior) && count($this->meeting_behavior) > 0) {
            $markdown .= "## Comportamiento Profesional\n";
            $markdown .= "**En reuniones:** " . implode(', ', $this->meeting_behavior) . "\n";
        }
        
        if ($this->problem_solving_approach) {
            $markdown .= "**Resolviendo problemas:** {$this->problem_solving_approach}\n";
        }
        
        if ($this->conflict_handling) {
            $markdown .= "**Manejando conflictos:** {$this->conflict_handling}\n";
        }
        
        if ($this->pressure_reaction) {
            $markdown .= "**Bajo presión:** {$this->pressure_reaction}\n\n";
        }

        // Emotional Style
        if ($this->emotional_support_style && is_array($this->emotional_support_style) && count($this->emotional_support_style) > 0) {
            $markdown .= "## Inteligencia Emocional\n";
            $markdown .= "**Cuando alguien está mal:** " . implode(', ', $this->emotional_support_style) . "\n";
        }
        
        if ($this->emotion_management) {
            $markdown .= "**Manejo de emociones propias:** {$this->emotion_management}\n\n";
        }

        // Humor
        if ($this->humor_style) {
            $markdown .= "## Humor\n";
            $markdown .= "**Tipo de humor:** {$this->humor_style}";
            if ($this->humor_importance) {
                $markdown .= " (Importancia: {$this->humor_importance}/10)";
            }
            $markdown .= "\n";
            
            if ($this->humor_as_defense && $this->humor_as_defense !== 'Nunca') {
                $markdown .= "**Como mecanismo de defensa:** {$this->humor_as_defense}\n";
            }
            
            if ($this->humor_examples) {
                $markdown .= "**Ejemplos:** {$this->humor_examples}\n";
            }
            $markdown .= "\n";
        }

        // Specific Reactions
        $markdown .= "## Reacciones Específicas\n";
        
        if ($this->reaction_to_lies) {
            $markdown .= "**Ante mentiras:** {$this->reaction_to_lies}\n";
        }
        
        if ($this->reaction_to_own_mistakes) {
            $markdown .= "**Ante errores propios:** {$this->reaction_to_own_mistakes}\n";
        }
        
        if ($this->reaction_to_others_mistakes) {
            $markdown .= "**Ante errores de otros:** {$this->reaction_to_others_mistakes}\n";
        }
        
        if ($this->emergency_behavior) {
            $markdown .= "**En emergencias:** {$this->emergency_behavior}\n";
        }
        
        if ($this->reaction_to_unfair_criticism) {
            $markdown .= "**Ante críticas injustas:** {$this->reaction_to_unfair_criticism}\n";
        }
        
        if ($this->delivering_bad_news) {
            $markdown .= "**Dando malas noticias:** {$this->delivering_bad_news}\n\n";
        }

        // Peculiarities
        if ($this->characteristic_gestures || $this->personality_contradictions || $this->superpower || $this->kryptonite) {
            $markdown .= "## Peculiaridades\n";
            
            if ($this->characteristic_gestures) {
                $markdown .= "**Gestos característicos:** {$this->characteristic_gestures}\n";
            }
            
            if ($this->personality_contradictions) {
                $markdown .= "**Contradicciones:** {$this->personality_contradictions}\n";
            }
            
            if ($this->superpower) {
                $markdown .= "**Superpoder:** {$this->superpower}\n";
            }
            
            if ($this->kryptonite) {
                $markdown .= "**Kryptonita:** {$this->kryptonite}\n\n";
            }
        }

        // Key for Impersonation
        if ($this->impersonation_key) {
            $markdown .= "## CLAVE PARA IMPERSONACIÓN\n";
            $markdown .= "**LO MÁS IMPORTANTE:** {$this->impersonation_key}\n\n";
        }

        // Instructions footer
        $markdown .= "---\n";
        $markdown .= "**RECORDATORIO:** Mantén estos patrones de personalidad consistentemente en todas las respuestas. ";
        $markdown .= "Responde como {$name} respondería, con su estilo, valores, humor y peculiaridades específicas.";

        return $markdown;
    }
}
