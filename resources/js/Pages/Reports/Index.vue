<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// Componentes PrimeVue
import Chart from 'primevue/chart';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';

const props = defineProps({
    filters: Object,
    stats: Object,
    chart: Object
});

// Formulario para filtrar fechas
const form = useForm({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date
});

const filterReport = () => {
    form.get(route('reports.index'), { preserveScroll: true });
};

// Configuración del Gráfico
const chartData = ref(null);
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    }
});

onMounted(() => {
    chartData.value = {
        labels: props.chart.labels,
        datasets: [
            {
                label: 'Cobros del Día',
                data: props.chart.data,
                backgroundColor: 'rgba(34, 197, 94, 0.2)', // Verde clarito
                borderColor: 'rgba(34, 197, 94, 1)',     // Verde fuerte
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }
        ]
    };
});
</script>

<template>
    <Head title="Reporte de Ganancias" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reporte Financiero
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="bg-white p-4 shadow-sm rounded-lg flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex flex-col gap-1 w-full md:w-auto">
                        <label class="text-sm font-bold text-gray-600">Desde</label>
                        <Calendar v-model="form.start_date" dateFormat="yy-mm-dd" showIcon />
                    </div>
                    <div class="flex flex-col gap-1 w-full md:w-auto">
                        <label class="text-sm font-bold text-gray-600">Hasta</label>
                        <Calendar v-model="form.end_date" dateFormat="yy-mm-dd" showIcon />
                    </div>
                    <Button label="Filtrar" icon="pi pi-filter" @click="filterReport" :loading="form.processing" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="bg-white p-6 shadow-sm rounded-lg border-l-4 border-red-400">
                        <p class="text-gray-500 text-sm">Dinero Prestado</p>
                        <p class="text-2xl font-bold text-red-600">- ${{ stats.lent.toLocaleString() }}</p>
                        <p class="text-xs text-gray-400">Salidas de caja</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-lg border-l-4 border-blue-400">
                        <p class="text-gray-500 text-sm">Total Cobrado</p>
                        <p class="text-2xl font-bold text-blue-600">+ ${{ stats.collected.toLocaleString() }}</p>
                        <p class="text-xs text-gray-400">Entradas de caja</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-lg border-l-4 border-green-500">
                        <p class="text-gray-500 text-sm font-bold text-green-700">GANANCIA NETA (Intereses)</p>
                        <p class="text-3xl font-bold text-green-600">+ ${{ stats.profit.toLocaleString() }}</p>
                        <p class="text-xs text-gray-400">Tu ganancia real descontando capital</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-lg border-l-4" :class="stats.balance >= 0 ? 'border-gray-600' : 'border-red-600'">
                        <p class="text-gray-500 text-sm">Flujo de Caja (Balance)</p>
                        <p class="text-2xl font-bold text-gray-800">${{ stats.balance.toLocaleString() }}</p>
                        <p class="text-xs text-gray-400">Entradas - Salidas</p>
                    </div>

                </div>

                <div class="bg-white p-6 shadow-sm rounded-lg">
                    <h3 class="font-bold text-gray-700 mb-4">Evolución de Cobros (Día a día)</h3>
                    <div style="height: 300px">
                        <Chart type="line" :data="chartData" :options="chartOptions" class="h-full" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
