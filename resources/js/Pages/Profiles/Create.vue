<template>
    <Head title="Crear Perfil" />
    
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col border-r border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Crear Perfil</h1>
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
            <div class="max-w-3xl mx-auto p-6">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            Crear Nuevo Perfil
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Te haremos 50 preguntas estructuradas para crear un perfil completo y detallado que capture tu personalidad, 
                            valores, forma de ser y peculiaridades únicas. Este perfil podrá usarse para generar un archivo Markdown 
                            similar al ejemplo de Dani.md.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nombre del perfil
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Ej: Daniel Esquilas, María García, etc."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Este será el nombre que aparezca en el perfil generado
                            </p>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                ¿Qué esperar?
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Información básica</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Nombre, ubicación y rol profesional</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">2</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Personalidad</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Carácter, forma de pensar y comportarse</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">3</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Comunicación</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Estilo, expresiones características</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">4</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Valores y filosofía</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Principios, ética personal y laboral</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">5</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Situaciones específicas</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Comportamiento en trabajo, problemas, equipo</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">6</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Aspectos personales</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Aficiones, metas, miedos, historia personal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-6">
                            <Link 
                                :href="route('personality-profiles.index')"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || !form.name"
                                class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md transition-colors"
                            >
                                {{ form.processing ? 'Creando...' : 'Comenzar Cuestionario' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'

defineProps({
    questions: Object
})

const form = useForm({
    name: ''
})

const submit = () => {
    form.post(route('personality-profiles.store'), {
        onSuccess: (response) => {
            const profile = response.props.flash?.profile || response.data
            if (profile?.id) {
                router.visit(route('personality-profiles.show', profile.id))
            }
        }
    })
}
</script>