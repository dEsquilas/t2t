<template>
    <Head title="Perfiles" />
    
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col border-r border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Perfiles</h1>
            </div>
            
            <!-- Navigation -->
            <div class="p-4 space-y-2">
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
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Mis Perfiles
                    </h2>
                    <Link 
                        :href="route('personality-profiles.create')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors"
                    >
                        Crear Nuevo Perfil
                    </Link>
                </div>

                <div v-if="profiles.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="text-gray-500 dark:text-gray-400 mb-4">
                        No tienes perfiles creados aún
                    </div>
                    <Link 
                        :href="route('personality-profiles.create')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors"
                    >
                        Crear Mi Primer Perfil
                    </Link>
                </div>

                <div v-else class="grid gap-4">
                    <div 
                        v-for="profile in profiles" 
                        :key="profile.id"
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ profile.name }}
                                </h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full" 
                                              :class="profile.status === 'completed' ? 'bg-green-500' : 'bg-yellow-500'">
                                        </span>
                                        {{ profile.status === 'completed' ? 'Completado' : 'En proceso' }}
                                    </span>
                                    <span>
                                        Creado {{ new Date(profile.created_at).toLocaleDateString() }}
                                    </span>
                                    <span v-if="profile.status === 'in_progress'">
                                        Pregunta {{ profile.current_question }} / 50
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Link 
                                    :href="route('personality-profiles.show', profile.id)"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 px-3 py-1 rounded border border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900 transition-colors"
                                >
                                    {{ profile.status === 'completed' ? 'Ver' : 'Continuar' }}
                                </Link>
                                <Link 
                                    v-if="profile.status === 'completed'"
                                    :href="route('personality-profiles.download', profile.id)"
                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 px-3 py-1 rounded border border-green-600 hover:bg-green-50 dark:hover:bg-green-900 transition-colors"
                                >
                                    Descargar
                                </Link>
                                <button 
                                    @click="deleteProfile(profile.id, profile.name)"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 px-3 py-1 rounded border border-red-600 hover:bg-red-50 dark:hover:bg-red-900 transition-colors"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

defineProps({
    profiles: Array
})

const deleteProfile = (profileId, profileName) => {
    if (confirm(`¿Estás seguro de que quieres eliminar el perfil "${profileName}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('personality-profiles.destroy', profileId), {
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
</script>