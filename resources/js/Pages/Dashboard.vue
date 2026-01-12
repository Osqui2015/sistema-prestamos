<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Recibimos las estadísticas desde el Controlador
defineProps({
    stats: Object
});
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de Control</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                                <i class="pi pi-users text-xl"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium">Clientes Totales</div>
                                <div class="text-3xl font-bold text-gray-800">{{ stats.clients }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="pi pi-briefcase text-xl"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium">Préstamos Activos</div>
                                <div class="text-3xl font-bold text-gray-800">{{ stats.active_loans }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4"
                         :class="stats.overdue > 0 ? 'border-red-500' : 'border-green-500'">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full mr-4" :class="stats.overdue > 0 ? 'bg-red-100 text-red-500' : 'bg-green-100 text-green-500'">
                                <i class="pi" :class="stats.overdue > 0 ? 'pi-exclamation-triangle' : 'pi-check-circle'"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium">Cuotas Vencidas</div>
                                <div class="text-3xl font-bold" :class="stats.overdue > 0 ? 'text-red-600' : 'text-green-600'">
                                    {{ stats.overdue }}
                                </div>
                            </div>
                        </div>
                        <div v-if="stats.overdue > 0" class="text-xs text-red-500 mt-1">
                            ¡Revisa el calendario!
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="bg-white p-6 shadow-sm rounded-lg">
                        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">
                            <i class="pi pi-money-bill mr-2"></i> Resumen Financiero
                        </h3>
                        <div class="space-y-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Prestado (Histórico)</span>
                                <span class="font-bold text-lg text-gray-800">${{ stats.total_lent.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pendiente de Cobro</span>
                                <span class="font-bold text-xl text-blue-600">${{ stats.pending.toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-white p-6 shadow-sm rounded-lg flex flex-col justify-center items-center text-center border border-blue-100">
                        <h3 class="text-lg font-bold text-blue-800 mb-2">¿Qué quieres hacer hoy?</h3>
                        <div class="flex flex-wrap justify-center gap-3 mt-4">
                            <Link :href="route('clients.index')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 flex items-center">
                                <i class="pi pi-user-plus mr-2"></i> Nuevo Préstamo
                            </Link>

                            <Link :href="route('calendar.index')" class="bg-white hover:bg-gray-50 text-blue-600 font-bold py-2 px-4 rounded shadow border border-blue-200 transition duration-150 flex items-center">
                                <i class="pi pi-calendar mr-2"></i> Ver Cobros
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
