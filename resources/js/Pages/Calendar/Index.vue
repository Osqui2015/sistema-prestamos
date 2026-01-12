<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

const props = defineProps({
    events: Array
});

// Configuración del Calendario
const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: 'es', // Idioma español
    events: props.events, // Los eventos que vienen del servidor
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek'
    },
    buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana'
    },
    eventClick: function(info) {
        // Al hacer clic, visitamos la URL del evento (ir al préstamo)
        if (info.event.url) {
            info.jsEvent.preventDefault(); // Evita que el navegador abra una pestaña nueva
            window.location.href = info.event.url;
        }
    }
};
</script>

<template>
    <Head title="Calendario de Cobros" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Calendario de Cobros
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div class="mb-4 bg-blue-50 p-4 rounded border border-blue-200 text-blue-800 text-sm">
                        <i class="pi pi-info-circle mr-2"></i>
                        Aquí ves todas las cuotas pendientes. Haz clic en un evento para ir a cobrar.
                    </div>

                    <FullCalendar :options="calendarOptions" />

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Pequeño ajuste visual para que los eventos se vean mejor */
.fc-event {
    cursor: pointer;
    font-size: 0.85em;
    padding: 2px;
}
</style>
