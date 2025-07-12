<template>
    <Head :title="`Perfil: ${profile.name}`" />
    
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col border-r border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ profile.name }}</h1>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ profile.status === 'completed' ? 'Completado' : `Pregunta ${profile.current_question}/20` }}
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="p-4 space-y-2">
                <Link 
                    :href="route('personality-profiles.index')"
                    class="block text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                >
                    ← Volver a Perfiles
                </Link>
                <Link 
                    :href="route('chat.index')"
                    class="block text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                >
                    ← Volver al Chat
                </Link>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 overflow-y-auto">
            <div class="max-w-4xl mx-auto p-6">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ profile.name }}
                            </h2>
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full" 
                                          :class="profile.status === 'completed' ? 'bg-green-500' : 'bg-yellow-500'">
                                    </span>
                                    {{ profile.status === 'completed' ? 'Completado' : 'En proceso' }}
                                </span>
                                <span v-if="profile.status === 'in_progress'">
                                    Pregunta {{ profile.current_question }} / 50
                                </span>
                                <span>
                                    Creado {{ new Date(profile.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <Link 
                                :href="route('personality-profiles.index')"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                Volver
                            </Link>
                            <button 
                                v-if="profile.status === 'completed'"
                                @click="generateProfile"
                                :disabled="generating"
                                class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                {{ generating ? 'Generando...' : 'Generar Markdown' }}
                            </button>
                            <Link 
                                v-if="profile.status === 'completed'"
                                :href="route('personality-profiles.download', profile.id)"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Descargar
                            </Link>
                            <button 
                                @click="deleteProfile"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div v-if="profile.status === 'in_progress'" class="mb-8">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span>Progreso del cuestionario</span>
                            <span>{{ Math.round((profile.current_question - 1) / 50 * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div 
                                class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                :style="{ width: `${Math.round((profile.current_question - 1) / 50 * 100)}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Current Question (if in progress) -->
                    <div v-if="profile.status === 'in_progress'" class="mb-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div v-if="loadingQuestion" class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400">Cargando pregunta...</div>
                        </div>
                        <div v-else-if="currentQuestion">
                            <div class="mb-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                                        {{ currentQuestion.section || currentQuestion.category }} - Pregunta {{ currentQuestion.questionNumber }} de 50
                                    </span>
                                    <div v-if="currentQuestion.type === 'multiple_choice' && answerForm.answer && answerForm.answer.length > 0" 
                                         class="text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900 px-3 py-1 rounded-full">
                                        {{ answerForm.answer.length }} seleccionadas
                                    </div>
                                    <div v-else-if="currentQuestion.type === 'ranking' && answerForm.answer && answerForm.answer.length > 0" 
                                         class="text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900 px-3 py-1 rounded-full">
                                        {{ answerForm.answer.length }} seleccionadas
                                    </div>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ currentQuestion.question }}
                                </h3>
                            </div>
                            
                            <form @submit.prevent="submitAnswer" class="space-y-4">
                                <!-- Text Input -->
                                <div v-if="currentQuestion.type === 'text'">
                                    <input
                                        v-model="answerForm.answer"
                                        type="text"
                                        :placeholder="currentQuestion.placeholder || ''"
                                        :required="currentQuestion.required"
                                        :maxlength="currentQuestion.max_length"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="currentQuestion.max_length" class="text-xs text-gray-500 mt-1">
                                        {{ (answerForm.answer || '').length }} / {{ currentQuestion.max_length }} caracteres
                                    </div>
                                </div>

                                <!-- Number Input -->
                                <div v-else-if="currentQuestion.type === 'number'">
                                    <input
                                        v-model.number="answerForm.answer"
                                        type="number"
                                        :placeholder="currentQuestion.placeholder || ''"
                                        :required="currentQuestion.required"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Textarea Input -->
                                <div v-else-if="currentQuestion.type === 'textarea'">
                                    <textarea
                                        v-model="answerForm.answer"
                                        rows="4"
                                        :placeholder="currentQuestion.placeholder || ''"
                                        :required="currentQuestion.required"
                                        :maxlength="currentQuestion.max_length"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    ></textarea>
                                    <div v-if="currentQuestion.max_length" class="text-xs text-gray-500 mt-1">
                                        {{ (answerForm.answer || '').length }} / {{ currentQuestion.max_length }} caracteres
                                    </div>
                                </div>

                                <!-- Single Choice (Radio buttons) -->
                                <div v-else-if="currentQuestion.type === 'single_choice'" class="space-y-2">
                                    <div 
                                        v-for="(option, index) in currentQuestion.options" 
                                        :key="index"
                                        class="flex items-center"
                                    >
                                        <input
                                            :id="`option-${index}`"
                                            v-model="answerForm.answer"
                                            type="radio"
                                            :value="option"
                                            :name="`question-${currentQuestion.questionNumber}`"
                                            class="border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <label 
                                            :for="`option-${index}`"
                                            class="ml-3 text-gray-700 dark:text-gray-300"
                                        >
                                            {{ option }}
                                        </label>
                                    </div>
                                </div>

                                <!-- Multiple Choice (Checkboxes) -->
                                <div v-else-if="currentQuestion.type === 'multiple_choice'" class="space-y-2">
                                    <div v-if="currentQuestion.min_selections || currentQuestion.max_selections" class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        <span v-if="currentQuestion.min_selections && currentQuestion.max_selections">
                                            Selecciona entre {{ currentQuestion.min_selections }} y {{ currentQuestion.max_selections }} opciones
                                        </span>
                                        <span v-else-if="currentQuestion.min_selections">
                                            Selecciona al menos {{ currentQuestion.min_selections }} opciones
                                        </span>
                                        <span v-else-if="currentQuestion.max_selections">
                                            Selecciona máximo {{ currentQuestion.max_selections }} opciones
                                        </span>
                                    </div>
                                    
                                    <!-- Multi-column grid -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        <div 
                                            v-for="(option, index) in currentQuestion.options" 
                                            :key="index"
                                            class="flex items-center"
                                        >
                                            <input
                                                :id="`option-${index}`"
                                                v-model="answerForm.answer"
                                                type="checkbox"
                                                :value="option"
                                                :disabled="currentQuestion.max_selections && answerForm.answer && answerForm.answer.length >= currentQuestion.max_selections && !answerForm.answer.includes(option)"
                                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mr-2 flex-shrink-0"
                                            />
                                            <label 
                                                :for="`option-${index}`"
                                                class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer leading-tight"
                                                :class="{ 'opacity-50': currentQuestion.max_selections && answerForm.answer && answerForm.answer.length >= currentQuestion.max_selections && !answerForm.answer.includes(option) }"
                                            >
                                                {{ option }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scale (1-10) -->
                                <div v-else-if="currentQuestion.type === 'scale'" class="space-y-4">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ currentQuestion.min || 1 }}</span>
                                        <span>{{ currentQuestion.max || 10 }}</span>
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model.number="answerForm.answer"
                                            type="range"
                                            :min="currentQuestion.min || 1"
                                            :max="currentQuestion.max || 10"
                                            class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 custom-range"
                                            style="background: linear-gradient(to right, #4f46e5 0%, #4f46e5 var(--progress, 0%), #e5e7eb var(--progress, 0%), #e5e7eb 100%)"
                                            :style="{ '--progress': ((answerForm.answer - (currentQuestion.min || 1)) / ((currentQuestion.max || 10) - (currentQuestion.min || 1))) * 100 + '%' }"
                                        />
                                    </div>
                                    <div class="text-center">
                                        <span class="inline-block text-2xl font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900 px-4 py-2 rounded-full">
                                            {{ answerForm.answer || currentQuestion.min || 1 }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Ranking (for future implementation) -->
                                <div v-else-if="currentQuestion.type === 'ranking'" class="space-y-2">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        Selecciona {{ currentQuestion.max_selections || 5 }} opciones y ordénalas por importancia
                                    </div>
                                    
                                    <!-- Multi-column grid -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        <div 
                                            v-for="(option, index) in currentQuestion.options" 
                                            :key="index"
                                            class="flex items-center"
                                        >
                                            <input
                                                :id="`option-${index}`"
                                                v-model="answerForm.answer"
                                                type="checkbox"
                                                :value="option"
                                                :disabled="currentQuestion.max_selections && answerForm.answer && answerForm.answer.length >= currentQuestion.max_selections && !answerForm.answer.includes(option)"
                                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mr-2 flex-shrink-0"
                                            />
                                            <label 
                                                :for="`option-${index}`"
                                                class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer leading-tight"
                                                :class="{ 'opacity-50': currentQuestion.max_selections && answerForm.answer && answerForm.answer.length >= currentQuestion.max_selections && !answerForm.answer.includes(option) }"
                                            >
                                                {{ option }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between pt-4">
                                    <button
                                        v-if="currentQuestion.questionNumber > 1"
                                        type="button"
                                        @click="previousQuestion"
                                        class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        Anterior
                                    </button>
                                    <div v-else></div>
                                    <button
                                        type="submit"
                                        :disabled="answerForm.processing || (!answerForm.answer && currentQuestion.required)"
                                        class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md transition-colors"
                                    >
                                        {{ answerForm.processing ? 'Guardando...' : (currentQuestion.questionNumber === 50 ? 'Finalizar' : 'Siguiente') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Completed Profile Summary -->
                    <div v-if="profile.status === 'completed'" class="space-y-6">
                        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium text-green-800 dark:text-green-200">
                                    Perfil completado exitosamente
                                </span>
                            </div>
                            <p class="text-green-700 dark:text-green-300 mt-1">
                                Tu perfil está listo. Puedes descargarlo como archivo Markdown o generar una vista previa.
                            </p>
                        </div>

                        <!-- Basic Info Summary -->
                        <div v-if="profile.name || profile.location || profile.professional_role" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white mb-3">Información Básica</h3>
                            <div class="space-y-2 text-sm text-gray-900 dark:text-white">
                                <div v-if="profile.name">
                                    <span class="font-medium">Nombre:</span> {{ profile.name }}
                                </div>
                                <div v-if="profile.location">
                                    <span class="font-medium">Ubicación:</span> {{ profile.location }}
                                </div>
                                <div v-if="profile.professional_role">
                                    <span class="font-medium">Rol Profesional:</span> {{ profile.professional_role }}
                                </div>
                            </div>
                        </div>

                        <!-- Generated Markdown Preview -->
                        <div v-if="generatedMarkdown" class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Vista previa del Markdown</h3>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 max-h-96 overflow-y-auto">
                                <pre class="text-sm whitespace-pre-wrap font-mono text-black dark:text-white bg-transparent border-0 p-0 m-0">{{ generatedMarkdown }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    profile: Object
})

const currentQuestion = ref(null)
const loadingQuestion = ref(false)
const generating = ref(false)
const generatedMarkdown = ref(null)

const answerForm = useForm({
    answer: ''
})

const loadCurrentQuestion = async () => {
    if (props.profile.status === 'completed') return
    
    loadingQuestion.value = true
    try {
        const response = await axios.get(route('personality-profiles.question', {
            profile: props.profile.id,
            questionNumber: props.profile.current_question
        }))
        
        // The response should be a proper question object, not a nested structure
        const questionData = response.data
        
        currentQuestion.value = {
            section: questionData.section,
            category: questionData.category,
            question: questionData.question,
            type: questionData.type,
            field: questionData.field,
            required: questionData.required,
            options: questionData.options,
            placeholder: questionData.placeholder,
            max_length: questionData.max_length,
            min_selections: questionData.min_selections,
            max_selections: questionData.max_selections,
            min: questionData.min,
            max: questionData.max,
            questionNumber: questionData.questionNumber,
            totalQuestions: questionData.totalQuestions,
            progress: questionData.progress
        }
        
        // Initialize answer based on type
        if (currentQuestion.value.type === 'multiple_choice' || currentQuestion.value.type === 'ranking') {
            answerForm.answer = []
        } else if (currentQuestion.value.type === 'scale') {
            answerForm.answer = currentQuestion.value.min || 1
        } else {
            answerForm.answer = ''
        }
        
        // Load existing answer if available
        const existingAnswer = props.profile.responses?.[currentQuestion.value.questionNumber]
        if (existingAnswer !== undefined && existingAnswer !== null) {
            answerForm.answer = existingAnswer
        }
        
    } catch (error) {
        console.error('Error loading question:', error)
    } finally {
        loadingQuestion.value = false
    }
}

const submitAnswer = async () => {
    answerForm.post(route('personality-profiles.answer', {
        profile: props.profile.id,
        questionNumber: currentQuestion.value.questionNumber
    }), {
        onSuccess: (page) => {
            // Update the profile data with the fresh data from backend
            if (page.props.profile) {
                Object.assign(props.profile, page.props.profile)
            }
            
            // If not completed, load the next question
            if (!page.props.completed && props.profile.status === 'in_progress') {
                loadCurrentQuestion()
            }
            
            // Reset the form
            if (currentQuestion.value.type === 'multiple_choice' || currentQuestion.value.type === 'ranking') {
                answerForm.answer = []
            } else if (currentQuestion.value.type === 'scale') {
                answerForm.answer = currentQuestion.value.min || 1
            } else {
                answerForm.answer = ''
            }
        },
        onError: (errors) => {
            console.error('Error submitting answer:', errors)
        }
    })
}

const previousQuestion = async () => {
    if (currentQuestion.value.questionNumber > 1) {
        loadingQuestion.value = true
        try {
            const response = await axios.get(route('personality-profiles.question', {
                profile: props.profile.id,
                questionNumber: currentQuestion.value.questionNumber - 1
            }))
            currentQuestion.value = response.data
            
            // Load previous answer if exists
            const previousAnswer = props.profile.responses?.[currentQuestion.value.questionNumber]
            if (previousAnswer) {
                answerForm.answer = previousAnswer
            } else {
                if (currentQuestion.value.type === 'multiple_choice' || currentQuestion.value.type === 'ranking') {
                    answerForm.answer = []
                } else if (currentQuestion.value.type === 'scale') {
                    answerForm.answer = currentQuestion.value.min || 1
                } else {
                    answerForm.answer = ''
                }
            }
        } catch (error) {
            console.error('Error loading previous question:', error)
        } finally {
            loadingQuestion.value = false
        }
    }
}

const generateProfile = async () => {
    generating.value = true
    try {
        const response = await axios.post(route('personality-profiles.generate', props.profile.id))
        generatedMarkdown.value = response.data.markdown
    } catch (error) {
        console.error('Error generating profile:', error)
    } finally {
        generating.value = false
    }
}

const deleteProfile = () => {
    if (confirm(`¿Estás seguro de que quieres eliminar el perfil "${props.profile.name}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('personality-profiles.destroy', props.profile.id), {
            onSuccess: () => {
                // The redirect happens automatically from the controller
            },
            onError: (errors) => {
                console.error('Error al eliminar el perfil:', errors)
                alert('Hubo un error al eliminar el perfil. Por favor, inténtalo de nuevo.')
            }
        })
    }
}

onMounted(() => {
    loadCurrentQuestion()
})
</script>

<style scoped>
.custom-range::-webkit-slider-thumb {
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #4f46e5;
    border: 2px solid #ffffff;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.custom-range::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #4f46e5;
    border: 2px solid #ffffff;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    border: none;
}

/* Sin estilos adicionales para markdown */
</style>