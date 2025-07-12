<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PersonalityProfileController extends Controller
{
    use AuthorizesRequests;
    private function getQuestions(): array
    {
        return [
            // Sección 1: Información Básica y Contexto
            1 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Cuál es tu nombre completo?',
                'type' => 'text',
                'field' => 'full_name',
                'required' => true
            ],
            2 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Tienes apodos o nombres por los que te conocen?',
                'type' => 'text',
                'field' => 'nicknames',
                'required' => false
            ],
            3 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Cuál es tu edad o rango de edad?',
                'type' => 'single_choice',
                'field' => 'age_range',
                'options' => ['18-25', '26-35', '36-45', '46-55', '56-65', '65+'],
                'required' => false
            ],
            4 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Dónde vives actualmente? (ciudad, región, país)',
                'type' => 'text',
                'field' => 'current_location',
                'required' => false
            ],
            5 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Hay lugares donde has vivido que hayan influido en tu personalidad?',
                'type' => 'textarea',
                'field' => 'influential_places',
                'max_length' => 500,
                'required' => false
            ],
            6 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Cuál es tu rol profesional actual?',
                'type' => 'text',
                'field' => 'current_role',
                'required' => false
            ],
            7 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Cuántos años de experiencia tienes en tu campo?',
                'type' => 'number',
                'field' => 'years_experience',
                'required' => false
            ],
            8 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => 'Selecciona las palabras que mejor resumen tu personalidad (elige entre 3 y 8)',
                'type' => 'multiple_choice',
                'field' => 'personality_summary',
                'min_selections' => 3,
                'max_selections' => 8,
                'options' => [
                    'Aventurero/a', 'Ambicioso/a', 'Analítico/a', 'Apasionado/a', 'Auténtico/a',
                    'Bromista', 'Calmado/a', 'Carismático/a', 'Cariñoso/a', 'Competitivo/a',
                    'Conservador/a', 'Creativo/a', 'Crítico/a', 'Curioso/a', 'Decidido/a',
                    'Detallista', 'Determinado/a', 'Divertido/a', 'Directo/a', 'Disciplinado/a',
                    'Eficiente', 'Empático/a', 'Enérgico/a', 'Entusiasta', 'Equilibrado/a',
                    'Espontáneo/a', 'Estructurado/a', 'Exigente', 'Extrovertido/a', 'Filosófico/a',
                    'Fiel', 'Flexible', 'Gamberro/a', 'Generoso/a', 'Honesto/a',
                    'Humilde', 'Idealista', 'Impaciente', 'Independiente', 'Ingenioso/a',
                    'Innovador/a', 'Intenso/a', 'Introvertido/a', 'Intuitivo/a', 'Irónico/a',
                    'Leal', 'Lógico/a', 'Meticuloso/a', 'Observador/a', 'Optimista',
                    'Organizado/a', 'Paciente', 'Perfeccionista', 'Perseverante', 'Pragmático/a',
                    'Protector/a', 'Rebelde', 'Reflexivo/a', 'Reservado/a', 'Resiliente',
                    'Responsable', 'Sarcástico/a', 'Sensible', 'Serio/a', 'Sociable',
                    'Soñador/a', 'Temperamental', 'Terco/a', 'Trabajador/a', 'Tranquilo/a',
                    'Transparente', 'Valiente', 'Versátil'
                ],
                'required' => true
            ],
            9 => [
                'section' => 'Información Básica y Contexto',
                'category' => 'Información Básica',
                'question' => '¿Cómo te presentarías en 30 segundos a alguien nuevo?',
                'type' => 'textarea',
                'field' => 'elevator_pitch',
                'max_length' => 400,
                'required' => true
            ],
            
            // Sección 2: Personalidad Core y Dualidades
            10 => [
                'section' => 'Personalidad Core y Dualidades',
                'category' => 'Personalidad',
                'question' => 'Selecciona tus rasgos principales de personalidad (mínimo 5, máximo 10)',
                'type' => 'multiple_choice',
                'field' => 'core_personality_traits',
                'min_selections' => 5,
                'max_selections' => 10,
                'options' => [
                    'Analítico/a', 'Apasionado/a', 'Bromista', 'Cariñoso/a', 'Competitivo/a',
                    'Creativo/a', 'Crítico/a', 'Curioso/a', 'Detallista', 'Determinado/a',
                    'Directo/a', 'Empático/a', 'Enérgico/a', 'Estructurado/a', 'Exigente',
                    'Extrovertido/a', 'Fiel/Leal', 'Gamberro/a', 'Humilde', 'Impaciente',
                    'Impulsivo/a', 'Independiente', 'Ingenioso/a', 'Intenso/a', 'Introvertido/a',
                    'Intuitivo/a', 'Irónico/a', 'Meticuloso/a', 'Observador/a', 'Optimista',
                    'Organizado/a', 'Paciente', 'Perfeccionista', 'Perseverante', 'Pesimista',
                    'Pragmático/a', 'Protector/a', 'Reflexivo/a', 'Resiliente', 'Responsable',
                    'Sarcástico/a', 'Sensible', 'Serio/a', 'Sociable', 'Terco/a', 'Transparente'
                ],
                'required' => true
            ],
            11 => [
                'section' => 'Personalidad Core y Dualidades',
                'category' => 'Personalidad',
                'question' => '¿Tu personalidad cambia significativamente según el contexto?',
                'type' => 'single_choice',
                'field' => 'personality_consistency',
                'options' => [
                    'Sí, soy una persona completamente diferente en diferentes situaciones',
                    'Sí, pero mantengo cierta esencia',
                    'Cambio solo en situaciones muy específicas',
                    'No, soy bastante consistente',
                    'Depende del día'
                ],
                'required' => true
            ],
            12 => [
                'section' => 'Personalidad Core y Dualidades',
                'category' => 'Personalidad',
                'question' => 'Describe tu "modo por defecto" (cómo eres la mayor parte del tiempo)',
                'type' => 'textarea',
                'field' => 'default_mode',
                'max_length' => 800,
                'required' => true
            ],
            13 => [
                'section' => 'Personalidad Core y Dualidades',
                'category' => 'Personalidad',
                'question' => '¿Tienes un "modo alternativo" que se activa en situaciones específicas?',
                'type' => 'single_choice',
                'field' => 'has_alternative_modes',
                'options' => [
                    'Sí, uno muy marcado',
                    'Sí, varios según la situación',
                    'Sí, pero es sutil',
                    'No realmente'
                ],
                'required' => true
            ],
            14 => [
                'section' => 'Personalidad Core y Dualidades',
                'category' => 'Personalidad',
                'question' => 'Si tienes modos alternativos, describe el principal: ¿Qué lo activa y cómo cambias?',
                'type' => 'textarea',
                'field' => 'alternative_mode_description',
                'max_length' => 800,
                'required' => false
            ],
            
            // Sección 3: Pasiones y Motivaciones
            15 => [
                'section' => 'Pasiones y Motivaciones',
                'category' => 'Pasiones',
                'question' => '¿Qué temas te apasionan profundamente? Lista todos',
                'type' => 'textarea',
                'field' => 'passions',
                'max_length' => 1000,
                'required' => true
            ],
            16 => [
                'section' => 'Pasiones y Motivaciones',
                'category' => 'Pasiones',
                'question' => 'Cuando hablas de tus pasiones, ¿qué cambios experimentas?',
                'type' => 'multiple_choice',
                'field' => 'passion_mode_changes',
                'options' => [
                    'Mi tono de voz cambia', 'Hablo más rápido', 'Hablo más despacio y meticuloso',
                    'Uso más las manos', 'Me acerco físicamente', 'Pierdo noción del tiempo',
                    'Me olvido del contexto social', 'No puedo parar de hablar', 'Me pongo muy serio/a',
                    'Me emociono visiblemente', 'Entro en detalles excesivos', 'Uso más ejemplos y analogías',
                    'Me vuelvo más técnico/a', 'Ignoro las señales de aburrimiento en otros',
                    'Mi vocabulario cambia', 'Desaparece mi humor habitual', 'Me vuelvo más intenso/a',
                    'Busco validación o debate'
                ],
                'required' => false
            ],
            17 => [
                'section' => 'Pasiones y Motivaciones',
                'category' => 'Pasiones',
                'question' => 'Dame un ejemplo específico de cómo te transformas cuando hablas de tus pasiones',
                'type' => 'textarea',
                'field' => 'passion_transformation_example',
                'max_length' => 600,
                'placeholder' => 'Ejemplo: "Cuando alguien menciona arquitectura de software, dejo lo que sea que esté haciendo y..."',
                'required' => false
            ],
            
            // Sección 4: Estilo de Comunicación Detallado
            18 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => 'Tu estilo de comunicación general es:',
                'type' => 'single_choice',
                'field' => 'communication_style',
                'options' => [
                    'Formal y distante',
                    'Formal pero cercano',
                    'Profesional adaptativo',
                    'Casual profesional',
                    'Informal y coloquial',
                    'Variable según audiencia',
                    'Directo sin filtros'
                ],
                'required' => true
            ],
            19 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => '¿Usas palabrotas/tacos?',
                'type' => 'single_choice',
                'field' => 'profanity_usage',
                'options' => [
                    'Nunca, me parece inapropiado',
                    'Solo en situaciones extremas',
                    'Cuando estoy muy cómodo',
                    'Regularmente para enfatizar',
                    'Constantemente, es mi forma de hablar',
                    'Depende mucho del contexto'
                ],
                'required' => true
            ],
            20 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => 'Si usas tacos, ¿cuáles son tus favoritos y en qué contexto los usas?',
                'type' => 'textarea',
                'field' => 'favorite_profanity',
                'max_length' => 400,
                'required' => false
            ],
            21 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => 'Muletillas o frases que repites constantemente:',
                'type' => 'textarea',
                'field' => 'verbal_tics',
                'max_length' => 500,
                'required' => false
            ],
            22 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => '¿Usas analogías o metáforas?',
                'type' => 'single_choice',
                'field' => 'analogy_usage',
                'options' => ['Constantemente', 'Frecuentemente', 'A veces', 'Raramente', 'Nunca'],
                'required' => true
            ],
            23 => [
                'section' => 'Estilo de Comunicación Detallado',
                'category' => 'Comunicación',
                'question' => 'Dame 3 ejemplos de analogías que usarías:',
                'type' => 'textarea',
                'field' => 'analogy_examples',
                'max_length' => 600,
                'required' => false
            ],
            
            // Sección 5: Valores, Principios y Filosofía
            24 => [
                'section' => 'Valores, Principios y Filosofía',
                'category' => 'Valores',
                'question' => 'Selecciona y ordena tus 5 valores fundamentales más importantes:',
                'type' => 'ranking',
                'field' => 'core_values',
                'max_selections' => 5,
                'options' => [
                    'Autenticidad', 'Autonomía', 'Bondad', 'Competencia', 'Creatividad',
                    'Disciplina', 'Eficiencia', 'Equidad', 'Excelencia', 'Familia',
                    'Honestidad', 'Honor', 'Humildad', 'Independencia', 'Innovación',
                    'Integridad', 'Justicia', 'Lealtad', 'Libertad', 'Liderazgo',
                    'Pasión', 'Perseverancia', 'Placer', 'Poder', 'Reconocimiento',
                    'Respeto', 'Responsabilidad', 'Sabiduría', 'Seguridad', 'Servicio',
                    'Solidaridad', 'Tradición', 'Transparencia', 'Valentía'
                ],
                'required' => true
            ],
            25 => [
                'section' => 'Valores, Principios y Filosofía',
                'category' => 'Valores',
                'question' => 'Escribe 3-5 principios de vida que te guían:',
                'type' => 'textarea',
                'field' => 'life_principles',
                'max_length' => 800,
                'required' => true
            ],
            26 => [
                'section' => 'Valores, Principios y Filosofía',
                'category' => 'Valores',
                'question' => 'Tu filosofía sobre el trabajo:',
                'type' => 'textarea',
                'field' => 'work_philosophy',
                'max_length' => 600,
                'required' => true
            ],
            27 => [
                'section' => 'Valores, Principios y Filosofía',
                'category' => 'Valores',
                'question' => 'Tu filosofía sobre el fracaso y los errores:',
                'type' => 'textarea',
                'field' => 'failure_philosophy',
                'max_length' => 600,
                'required' => true
            ],
            28 => [
                'section' => 'Valores, Principios y Filosofía',
                'category' => 'Valores',
                'question' => 'Cosas que NO toleras:',
                'type' => 'multiple_choice',
                'field' => 'intolerances',
                'options' => [
                    'Mentiras', 'Incompetencia', 'Pereza', 'Traición', 'Injusticia',
                    'Mediocridad', 'Hipocresía', 'Impuntualidad', 'Falta de respeto',
                    'Conformismo', 'Cobardía', 'Arrogancia', 'Ignorancia voluntaria',
                    'Manipulación', 'Victimismo'
                ],
                'required' => true
            ],
            
            // Sección 6: Comportamiento Profesional y Liderazgo
            29 => [
                'section' => 'Comportamiento Profesional y Liderazgo',
                'category' => 'Trabajo',
                'question' => 'En reuniones tú eres quien:',
                'type' => 'multiple_choice',
                'field' => 'meeting_behavior',
                'options' => [
                    'Lidera la conversación', 'Aporta ideas constantemente', 'Escucha y analiza',
                    'Hace preguntas incisivas', 'Resume y clarifica', 'Detecta problemas',
                    'Propone soluciones', 'Mantiene el foco', 'Alivia tensiones con humor',
                    'Toma notas detalladas', 'Se impacienta con divagaciones', 'Confronta directamente',
                    'Media en conflictos', 'Se aburre visiblemente'
                ],
                'required' => true
            ],
            30 => [
                'section' => 'Comportamiento Profesional y Liderazgo',
                'category' => 'Trabajo',
                'question' => 'Tu approach para resolver problemas:',
                'type' => 'textarea',
                'field' => 'problem_solving_approach',
                'max_length' => 800,
                'required' => true
            ],
            31 => [
                'section' => 'Comportamiento Profesional y Liderazgo',
                'category' => 'Trabajo',
                'question' => '¿Cómo manejas el conflicto?',
                'type' => 'textarea',
                'field' => 'conflict_handling',
                'max_length' => 600,
                'required' => true
            ],
            32 => [
                'section' => 'Comportamiento Profesional y Liderazgo',
                'category' => 'Trabajo',
                'question' => '¿Cómo reaccionas bajo presión?',
                'type' => 'textarea',
                'field' => 'pressure_reaction',
                'max_length' => 600,
                'required' => true
            ],
            
            // Sección 7: Inteligencia Emocional y Relaciones
            33 => [
                'section' => 'Inteligencia Emocional y Relaciones',
                'category' => 'Relaciones',
                'question' => 'Cuando alguien está mal, tú:',
                'type' => 'multiple_choice',
                'field' => 'emotional_support_style',
                'options' => [
                    'Lo detecto inmediatamente', 'Me acerco proactivamente', 'Espero a que me busquen',
                    'Ofrezco ayuda práctica', 'Ofrezco apoyo emocional', 'Doy espacio',
                    'Uso humor para animar', 'Escucho sin juzgar', 'Doy consejos',
                    'Comparto mi experiencia', 'Me incomoda', 'Evito la situación'
                ],
                'required' => true
            ],
            34 => [
                'section' => 'Inteligencia Emocional y Relaciones',
                'category' => 'Relaciones',
                'question' => '¿Cómo construyes confianza?',
                'type' => 'textarea',
                'field' => 'trust_building',
                'max_length' => 500,
                'required' => true
            ],
            35 => [
                'section' => 'Inteligencia Emocional y Relaciones',
                'category' => 'Relaciones',
                'question' => '¿Cómo manejas tus propias emociones?',
                'type' => 'textarea',
                'field' => 'emotion_management',
                'max_length' => 600,
                'required' => true
            ],
            
            // Sección 8: Humor y Personalidad Social
            36 => [
                'section' => 'Humor y Personalidad Social',
                'category' => 'Humor',
                'question' => '¿Qué tan importante es el humor en tu vida? (1-10)',
                'type' => 'scale',
                'field' => 'humor_importance',
                'min' => 1,
                'max' => 10,
                'required' => true
            ],
            37 => [
                'section' => 'Humor y Personalidad Social',
                'category' => 'Humor',
                'question' => 'Tu tipo de humor principal:',
                'type' => 'single_choice',
                'field' => 'humor_style',
                'options' => [
                    'Sarcástico/irónico', 'Absurdo/surrealista', 'Negro/oscuro',
                    'Intelectual/ingenioso', 'Físico/slapstick', 'Observacional',
                    'Autodeprecativo', 'Referencias culturales', 'Juegos de palabras',
                    'No tengo mucho humor'
                ],
                'required' => true
            ],
            38 => [
                'section' => 'Humor y Personalidad Social',
                'category' => 'Humor',
                'question' => '¿Usas humor como mecanismo de defensa?',
                'type' => 'single_choice',
                'field' => 'humor_as_defense',
                'options' => ['Siempre', 'Frecuentemente', 'A veces', 'Raramente', 'Nunca', 'No me había dado cuenta'],
                'required' => true
            ],
            39 => [
                'section' => 'Humor y Personalidad Social',
                'category' => 'Humor',
                'question' => 'Dame ejemplos de tu humor en diferentes situaciones: rompiendo el hielo, en situación tensa, criticando algo, sobre ti mismo',
                'type' => 'textarea',
                'field' => 'humor_examples',
                'max_length' => 800,
                'required' => false
            ],
            
            // Sección 9: Reacciones y Comportamientos Específicos
            40 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'Cuando alguien te miente, tú:',
                'type' => 'textarea',
                'field' => 'reaction_to_lies',
                'max_length' => 500,
                'required' => true
            ],
            41 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'Cuando cometes un error grave:',
                'type' => 'textarea',
                'field' => 'reaction_to_own_mistakes',
                'max_length' => 500,
                'required' => true
            ],
            42 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'Cuando alguien que supervisas comete un error:',
                'type' => 'textarea',
                'field' => 'reaction_to_others_mistakes',
                'max_length' => 500,
                'required' => true
            ],
            43 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'En una emergencia:',
                'type' => 'textarea',
                'field' => 'emergency_behavior',
                'max_length' => 500,
                'required' => true
            ],
            44 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'Cuando recibes una crítica injusta:',
                'type' => 'textarea',
                'field' => 'reaction_to_unfair_criticism',
                'max_length' => 500,
                'required' => true
            ],
            45 => [
                'section' => 'Reacciones y Comportamientos Específicos',
                'category' => 'Reacciones',
                'question' => 'Cuando tienes que dar malas noticias:',
                'type' => 'textarea',
                'field' => 'delivering_bad_news',
                'max_length' => 500,
                'required' => true
            ],
            
            // Sección 10: Peculiaridades y Detalles Únicos
            46 => [
                'section' => 'Peculiaridades y Detalles Únicos',
                'category' => 'Peculiaridades',
                'question' => 'Gestos o tics característicos:',
                'type' => 'textarea',
                'field' => 'characteristic_gestures',
                'max_length' => 600,
                'required' => false
            ],
            47 => [
                'section' => 'Peculiaridades y Detalles Únicos',
                'category' => 'Peculiaridades',
                'question' => 'Contradicciones en tu personalidad:',
                'type' => 'textarea',
                'field' => 'personality_contradictions',
                'max_length' => 600,
                'required' => false
            ],
            48 => [
                'section' => 'Peculiaridades y Detalles Únicos',
                'category' => 'Peculiaridades',
                'question' => 'Tu "superpoder" social o profesional:',
                'type' => 'textarea',
                'field' => 'superpower',
                'max_length' => 400,
                'required' => false
            ],
            49 => [
                'section' => 'Peculiaridades y Detalles Únicos',
                'category' => 'Peculiaridades',
                'question' => 'Tu "kryptonita" personal:',
                'type' => 'textarea',
                'field' => 'kryptonite',
                'max_length' => 400,
                'required' => false
            ],
            
            // Sección 11: Reflexión Final y Metacognición
            50 => [
                'section' => 'Reflexión Final y Metacognición',
                'category' => 'Reflexión',
                'question' => 'Si alguien quisiera impersonarte, ¿qué es lo más importante que debería saber?',
                'type' => 'textarea',
                'field' => 'impersonation_key',
                'max_length' => 1000,
                'required' => true
            ]
        ];
    }

    public function index()
    {
        $profiles = Auth::user()->profiles()->latest()->get();
        return inertia('Profiles/Index', compact('profiles'));
    }

    public function create()
    {
        $questions = $this->getQuestions();
        return inertia('Profiles/Create', compact('questions'));
    }

    public function store(Request $request)
    {
        $profile = Auth::user()->profiles()->create([
            'name' => $request->name,
            'status' => 'in_progress',
            'current_question' => 2
        ]);

        return response()->json($profile);
    }

    public function show(Profile $profile)
    {
        $this->authorize('view', $profile);
        return inertia('Profiles/Show', compact('profile'));
    }

    public function getQuestion(Profile $profile, $questionNumber, Request $request)
    {
        $this->authorize('view', $profile);
        
        $questions = $this->getQuestions();
        
        if (!isset($questions[$questionNumber])) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        // If this is a direct browser request (not AJAX), redirect to the profile page
        if (!$request->expectsJson()) {
            // Update the profile's current question to the requested question
            $profile->update(['current_question' => $questionNumber]);
            return redirect()->route('personality-profiles.show', $profile);
        }

        $question = $questions[$questionNumber];
        
        return response()->json([
            'section' => $question['section'],
            'category' => $question['category'],
            'question' => $question['question'],
            'type' => $question['type'],
            'field' => $question['field'],
            'required' => $question['required'],
            'options' => $question['options'] ?? null,
            'placeholder' => $question['placeholder'] ?? null,
            'max_length' => $question['max_length'] ?? null,
            'min_selections' => $question['min_selections'] ?? null,
            'max_selections' => $question['max_selections'] ?? null,
            'min' => $question['min'] ?? null,
            'max' => $question['max'] ?? null,
            'questionNumber' => $questionNumber,
            'totalQuestions' => count($questions),
            'progress' => round(($questionNumber / count($questions)) * 100)
        ]);
    }

    public function answerQuestion(Request $request, Profile $profile, $questionNumber)
    {
        $this->authorize('update', $profile);
        
        $questions = $this->getQuestions();
        $question = $questions[$questionNumber];
        
        $answer = $request->input('answer');
        
        // Process answer based on type
        if ($question['type'] === 'multiple_choice') {
            $answer = is_array($answer) ? $answer : [$answer];
        }
        
        // Store response
        $responses = $profile->responses ?? [];
        $responses[$questionNumber] = $answer;
        
        // Update profile field if it's a direct field mapping
        $updateData = ['responses' => $responses];
        
        if (isset($question['field'])) {
            if ($question['type'] === 'multiple_choice') {
                $updateData[$question['field']] = $answer;
            } elseif ($question['field'] === 'characteristic_expressions' && is_string($answer)) {
                // Convert comma-separated string to array for characteristic_expressions
                $expressions = array_map('trim', explode(',', $answer));
                $updateData[$question['field']] = array_filter($expressions);
            } else {
                $updateData[$question['field']] = $answer;
            }
        }
        
        // Update current question
        $nextQuestion = $questionNumber + 1;
        if ($nextQuestion <= count($questions)) {
            $updateData['current_question'] = $nextQuestion;
        } else {
            $updateData['status'] = 'completed';
        }
        
        // Update total questions to 50
        $updateData['total_questions'] = 50;
        
        $profile->update($updateData);
        
        // Return updated profile data to the frontend
        return inertia('Profiles/Show', [
            'profile' => $profile->fresh(),
            'questionUpdated' => true,
            'completed' => $nextQuestion > count($questions)
        ]);
    }

    public function generateProfile(Profile $profile)
    {
        $this->authorize('view', $profile);
        
        if ($profile->status !== 'completed') {
            return response()->json(['error' => 'Profile not completed yet'], 400);
        }

        $markdown = $profile->generateMarkdown();
        
        // Save to storage
        $filename = "profiles/{$profile->name}-" . date('Y-m-d') . ".md";
        Storage::disk('public')->put($filename, $markdown);
        
        return response()->json([
            'markdown' => $markdown,
            'downloadUrl' => Storage::url($filename)
        ]);
    }

    public function downloadProfile(Profile $profile)
    {
        $this->authorize('view', $profile);
        
        $markdown = $profile->generateMarkdown();
        $filename = "{$profile->name}-profile.md";
        
        return response($markdown, 200)
            ->header('Content-Type', 'text/markdown')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function getChatProfile(Profile $profile)
    {
        $this->authorize('view', $profile);
        
        if ($profile->status !== 'completed') {
            return response()->json(['error' => 'Profile not completed yet'], 400);
        }

        $chatProfile = $profile->generateChatProfile();
        
        return response()->json([
            'profile' => $chatProfile,
            'name' => $profile->full_name ?: $profile->name,
            'id' => $profile->id
        ]);
    }

    public function destroy(Profile $profile)
    {
        $this->authorize('delete', $profile);
        
        $profile->delete();
        
        return redirect()->route('personality-profiles.index')
            ->with('success', 'Perfil eliminado exitosamente.');
    }
}
