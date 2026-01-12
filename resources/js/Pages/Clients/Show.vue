<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import dayjs from 'dayjs'; // Librería para cálculo de fechas

// Componentes Visuales de PrimeVue
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import Tag from 'primevue/tag';

const props = defineProps({
    client: Object
});

// --- LÓGICA DEL SIMULADOR ---

// Estado del Modal
const loanModal = ref(false);

// Opciones de Frecuencia
const frequencies = ref([
    { name: 'Semanal', code: 'semanal' },
    { name: 'Mensual', code: 'mensual' }
]);

// Formulario (Datos que se enviarán al servidor)
const form = useForm({
    client_id: props.client.id,
    amount: 10000,       // Valor por defecto
    interest: 10,        // 10% por defecto
    installments: 4,     // 4 cuotas por defecto
    frequency: 'semanal',
    start_date: new Date() // Fecha de hoy
});

// Variables para la vista previa (Simulación)
const simulation = ref([]);
const totalToPay = ref(0);
const installmentAmount = ref(0);

// Función Matemática: Calcula la tabla antes de guardar
const calculateSimulation = () => {
    // Si faltan datos clave, no calculamos nada
    if (!form.amount || !form.installments) return;

    // 1. Cálculo Financiero (Interés Simple)
    const total = form.amount * (1 + (form.interest / 100));
    totalToPay.value = total;

    // Valor de cada cuota
    const quotaValue = total / form.installments;
    installmentAmount.value = quotaValue;

    // 2. Generación de Fechas
    let dates = [];
    let currentDate = dayjs(form.start_date); // Usamos la fecha seleccionada en el calendario

    for (let i = 1; i <= form.installments; i++) {
        // Sumar tiempo según frecuencia
        if (form.frequency === 'semanal') {
            currentDate = currentDate.add(1, 'week');
        } else {
            currentDate = currentDate.add(1, 'month');
        }

        dates.push({
            number: i,
            date: currentDate.format('DD/MM/YYYY'), // Formato legible para el usuario
            amount: quotaValue.toFixed(2)
        });
    }

    simulation.value = dates;
};

// Observador: Si cambia algún dato del formulario, recalcula la tabla automáticamente
watch(
    () => [form.amount, form.interest, form.installments, form.frequency, form.start_date],
    () => { calculateSimulation(); },
    { immediate: true }
);

// Función para enviar al Backend
const saveLoan = () => {
    form.post(route('loans.store'), {
        onSuccess: () => {
            loanModal.value = false; // Cerrar modal
            form.reset(); // Limpiar formulario
        }
    });
};
</script>

<template>
    <Head :title="'Perfil de ' + client.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Perfil de {{ client.name }}
                </h2>
                <Link :href="route('clients.index')">
                    <Button label="Volver a la Agenda" icon="pi pi-arrow-left" severity="secondary" text />
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="bg-white p-6 shadow sm:rounded-lg flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Datos Personales</h3>
                        <p class="text-gray-600"><span class="font-semibold">DNI:</span> {{ client.dni }}</p>
                        <p class="text-gray-600"><span class="font-semibold">Teléfono:</span> {{ client.phone || 'No registrado' }}</p>
                        <p class="text-gray-600"><span class="font-semibold">Dirección:</span> {{ client.address || 'No registrada' }}</p>
                    </div>
                    <div>
                        <Button label="Nuevo Préstamo" icon="pi pi-wallet" severity="success" @click="loanModal = true" />
                    </div>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Historial de Préstamos</h3>

                    <DataTable :value="client.loans" tableStyle="min-width: 50rem" stripedRows>
                        <template #empty> Este cliente no tiene préstamos activos. </template>

                        <Column field="id" header="# ID"></Column>
                        <Column field="amount" header="Monto Prestado">
                            <template #body="slotProps">
                                ${{ slotProps.data.amount }}
                            </template>
                        </Column>
                        <Column field="start_date" header="Fecha Inicio">
                            <template #body="slotProps">
                                {{ new Date(slotProps.data.start_date).toLocaleDateString() }}
                            </template>
                        </Column>
                        <Column field="status" header="Estado">
                            <template #body="slotProps">
                                <Tag :severity="slotProps.data.status === 'activo' ? 'warning' : 'success'"
                                     :value="slotProps.data.status.toUpperCase()" />
                            </template>
                        </Column>

                        <Column header="Acciones">
                            <template #body="slotProps">
                                <Link :href="route('loans.show', slotProps.data.id)">
                                    <Button icon="pi pi-eye" text rounded severity="info" v-tooltip="'Ver Detalle y Cobrar'" />
                                </Link>
                            </template>
                        </Column>

                    </DataTable>
                </div>

            </div>
        </div>

        <Dialog v-model:visible="loanModal" modal header="Simular Nuevo Préstamo" :style="{ width: '60rem' }">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="flex flex-col gap-5">
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <h4 class="font-bold text-blue-800 mb-1">Configuración</h4>
                        <p class="text-sm text-blue-600">Ajusta los valores para calcular las cuotas.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-semibold">Monto a Prestar ($)</label>
                        <InputNumber v-model="form.amount" mode="currency" currency="ARS" locale="es-AR" class="w-full" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="font-semibold">Interés (%)</label>
                            <InputNumber v-model="form.interest" suffix="%" :min="0" :max="100" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-semibold">Nro. Cuotas</label>
                            <InputNumber v-model="form.installments" showButtons :min="1" :max="48" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-semibold">Frecuencia de Pago</label>
                        <Dropdown v-model="form.frequency" :options="frequencies" optionLabel="name" optionValue="code" placeholder="Seleccione..." class="w-full" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-semibold">Fecha de Inicio</label>
                        <Calendar v-model="form.start_date" showIcon dateFormat="dd/mm/yy" />
                    </div>

                    <div class="mt-2 p-4 bg-gray-100 rounded-lg text-center">
                        <p class="text-sm text-gray-500">El cliente devolverá en total:</p>
                        <p class="text-3xl font-bold text-green-600">${{ totalToPay.toFixed(2) }}</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 border-l pl-4 md:pl-8">
                    <h4 class="font-bold text-gray-700">Plan de Pagos (Vista Previa)</h4>

                    <DataTable :value="simulation" scrollable scrollHeight="400px" size="small" class="text-sm">
                        <Column field="number" header="#"></Column>
                        <Column field="date" header="Vencimiento"></Column>
                        <Column field="amount" header="Monto Cuota">
                             <template #body="slotProps">
                                <span class="font-semibold">${{ slotProps.data.amount }}</span>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8 pt-4 border-t">
                <Button label="Cancelar" severity="secondary" @click="loanModal = false" />
                <Button label="Aprobar y Guardar" icon="pi pi-check" @click="saveLoan" :loading="form.processing" />
            </div>

        </Dialog>

    </AuthenticatedLayout>
</template>
