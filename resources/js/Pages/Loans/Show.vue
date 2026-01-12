<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Componentes PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';

// Recibimos el Préstamo con sus relaciones (Cliente y Cuotas)
const props = defineProps({
    loan: Object
});

// --- LÓGICA DE COBRO ---
const paymentModal = ref(false);
const selectedInstallment = ref(null);

// Formulario para enviar el pago
const form = useForm({
    amount: 0
});

// Abrir la ventana de pago
const openPayment = (installment) => {
    selectedInstallment.value = installment;
    // Sugerimos el monto total de la cuota por defecto
    form.amount = parseFloat(installment.amount);
    paymentModal.value = true;
};

// Enviar el pago al servidor
const processPayment = () => {
    form.post(route('installments.pay', selectedInstallment.value.id), {
        onSuccess: () => {
            paymentModal.value = false;
            form.reset();
        }
    });
};
</script>

<template>
    <Head title="Detalle del Préstamo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Préstamo #{{ loan.id }} <span class="text-gray-500 text-sm">| {{ loan.client.name }}</span>
                </h2>

                <div class="flex gap-2">
                    <a :href="route('loans.pdf', loan.id)" target="_blank">
                        <Button label="Descargar PDF" icon="pi pi-file-pdf" severity="danger" outlined />
                    </a>

                    <Link :href="route('clients.show', loan.client.id)">
                        <Button label="Volver al Cliente" icon="pi pi-arrow-left" severity="secondary" text />
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="bg-white p-6 shadow sm:rounded-lg grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded border">
                        <p class="text-gray-500 text-sm">Monto Prestado</p>
                        <p class="text-xl font-bold text-gray-800">${{ loan.amount }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded border border-green-200">
                        <p class="text-green-600 text-sm font-semibold">Total a Cobrar</p>
                        <p class="text-xl font-bold text-green-700">${{ loan.total_payable }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded border">
                        <p class="text-gray-500 text-sm">Interés / Frecuencia</p>
                        <p class="font-medium">{{ loan.interest_rate }}% - {{ loan.frequency.toUpperCase() }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded border flex flex-col justify-center items-center">
                        <p class="text-gray-500 text-sm mb-1">Estado Actual</p>
                        <Tag :value="loan.status.toUpperCase()" :severity="loan.status === 'activo' ? 'warning' : 'success'" />
                    </div>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">Plan de Pagos y Cobros</h3>

                    <DataTable :value="loan.installments" stripedRows tableStyle="min-width: 50rem">

                        <Column field="installment_number" header="#"></Column>

                        <Column field="due_date" header="Vencimiento">
                            <template #body="slotProps">
                                {{ new Date(slotProps.data.due_date).toLocaleDateString() }}
                            </template>
                        </Column>

                        <Column field="amount" header="Monto Cuota">
                            <template #body="slotProps">
                                ${{ slotProps.data.amount }}
                            </template>
                        </Column>

                        <Column field="amount_paid" header="Pagado">
                             <template #body="slotProps">
                                <span v-if="slotProps.data.amount_paid > 0" class="text-green-600 font-bold">
                                    ${{ slotProps.data.amount_paid }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </template>
                        </Column>

                        <Column header="Estado">
                            <template #body="slotProps">
                                <Tag :severity="slotProps.data.status === 'pagado' ? 'success' : 'danger'"
                                     :value="slotProps.data.status.toUpperCase()" />
                            </template>
                        </Column>

                        <Column header="Acción">
                            <template #body="slotProps">
                                <Button v-if="slotProps.data.status !== 'pagado'"
                                        icon="pi pi-dollar"
                                        label="Cobrar"
                                        size="small"
                                        severity="success"
                                        @click="openPayment(slotProps.data)" />
                                <span v-else class="text-green-600 text-sm font-bold flex items-center gap-1">
                                    <i class="pi pi-check-circle"></i> Listo
                                </span>
                            </template>
                        </Column>

                    </DataTable>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="paymentModal" modal header="Registrar Pago" :style="{ width: '30rem' }">
            <div class="flex flex-col gap-6">

                <div class="bg-blue-50 p-4 rounded text-sm text-blue-800 border border-blue-200">
                    Estás registrando el cobro de la <strong>Cuota #{{ selectedInstallment?.installment_number }}</strong>.<br>
                    Vencimiento: {{ selectedInstallment ? new Date(selectedInstallment.due_date).toLocaleDateString() : '' }}
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-bold text-gray-700">Monto Recibido ($)</label>
                    <InputNumber v-model="form.amount" mode="currency" currency="ARS" locale="es-AR" class="w-full" :min="0" />
                    <small class="text-gray-500">Ingrese el monto exacto que entregó el cliente.</small>
                </div>

                <div class="flex justify-end gap-2 mt-2 border-t pt-4">
                    <Button label="Cancelar" severity="secondary" @click="paymentModal = false" />
                    <Button label="Confirmar Pago" icon="pi pi-check" @click="processPayment" :loading="form.processing" severity="success" />
                </div>
            </div>
        </Dialog>

    </AuthenticatedLayout>
</template>
