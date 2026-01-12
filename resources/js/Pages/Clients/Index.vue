<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Componentes PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

const props = defineProps({
    clients: Array
});

// --- CONFIGURACIÓN DEL BUSCADOR ---
const filters = ref({
    global: { value: null, matchMode: 'contains' }
});

// --- LÓGICA DE GESTIÓN (ABM) ---
const modalVisible = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    dni: '',
    phone: '',
    address: ''
});

// Abrir modal para CREAR
const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    modalVisible.value = true;
};

// Abrir modal para EDITAR
const openEditModal = (client) => {
    isEditing.value = true;
    editingId.value = client.id;

    // Rellenar datos
    form.name = client.name;
    form.dni = client.dni;
    form.phone = client.phone;
    form.address = client.address;

    modalVisible.value = true;
};

// Guardar o Actualizar
const saveClient = () => {
    if (isEditing.value) {
        form.put(route('clients.update', editingId.value), {
            onSuccess: () => modalVisible.value = false
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => {
                modalVisible.value = false;
                form.reset();
            }
        });
    }
};

// Borrar Cliente
const deleteClient = (client) => {
    if (confirm('¿Estás seguro de borrar a ' + client.name + '?')) {
        router.delete(route('clients.destroy', client.id));
    }
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Agenda de Clientes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-hidden">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

                        <IconField iconPosition="left" class="w-full md:w-1/2">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Buscar por nombre o DNI..." class="w-full" />
                        </IconField>

                        <Button label="Nuevo Cliente" icon="pi pi-user-plus" @click="openCreateModal" severity="success" class="w-full md:w-auto" />
                    </div>

                    <div class="overflow-x-auto">
                        <DataTable :value="clients"
                                   paginator :rows="10"
                                   stripedRows
                                   v-model:filters="filters"
                                   :globalFilterFields="['name', 'dni']"
                                   tableStyle="min-width: 50rem">

                            <template #empty>
                                <div class="text-center p-4">No se encontraron clientes.</div>
                            </template>

                            <Column field="name" header="Nombre" sortable style="width: 25%">
                                <template #body="slotProps">
                                    <span class="font-semibold">{{ slotProps.data.name }}</span>
                                </template>
                            </Column>

                            <Column field="dni" header="DNI" style="width: 15%"></Column>

                            <Column header="Situación Actual" style="width: 35%">
                                <template #body="slotProps">
                                    <div v-if="slotProps.data.loans && slotProps.data.loans.length > 0" class="p-2 bg-green-50 rounded border border-green-100 inline-block min-w-max">
                                        <div class="flex items-center gap-2 mb-1">
                                            <Tag severity="success" value="PRÉSTAMO ACTIVO" class="text-[10px]" />
                                            <span class="font-bold text-gray-800">
                                                ${{ slotProps.data.loans[0].amount }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Faltan pagar <strong>{{ slotProps.data.loans[0].pending_installments }}</strong> cuotas.
                                        </div>
                                    </div>
                                    <div v-else>
                                        <span class="text-gray-400 text-sm italic">- Sin deuda activa -</span>
                                    </div>
                                </template>
                            </Column>

                            <Column header="Acciones" style="width: 25%">
                                <template #body="slotProps">
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-eye" text rounded severity="info"
                                                @click="router.get(route('clients.show', slotProps.data.id))"
                                                v-tooltip.top="'Ver Perfil'" />

                                        <Button icon="pi pi-pencil" text rounded severity="warning"
                                                @click="openEditModal(slotProps.data)"
                                                v-tooltip.top="'Editar'" />

                                        <Button icon="pi pi-trash" text rounded severity="danger"
                                                @click="deleteClient(slotProps.data)"
                                                v-tooltip.top="'Eliminar'" />
                                    </div>
                                </template>
                            </Column>

                        </DataTable>
                    </div> </div>
            </div>
        </div>

        <Dialog v-model:visible="modalVisible" modal :header="isEditing ? 'Editar Cliente' : 'Nuevo Cliente'" :style="{ width: '30rem' }">
            <div class="flex flex-col gap-4 mb-4">

                <div class="flex flex-col gap-2">
                    <label for="name" class="font-semibold text-gray-700">Nombre Completo</label>
                    <InputText id="name" v-model="form.name" autocomplete="off" />
                    <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="dni" class="font-semibold text-gray-700">DNI</label>
                    <InputText id="dni" v-model="form.dni" autocomplete="off" />
                    <small v-if="form.errors.dni" class="text-red-500">{{ form.errors.dni }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="phone" class="font-semibold text-gray-700">Teléfono</label>
                    <InputText id="phone" v-model="form.phone" autocomplete="off" />
                </div>

                <div class="flex flex-col gap-2">
                    <label for="address" class="font-semibold text-gray-700">Dirección</label>
                    <InputText id="address" v-model="form.address" autocomplete="off" />
                </div>

            </div>

            <div class="flex justify-end gap-2 border-t pt-4">
                <Button type="button" label="Cancelar" severity="secondary" @click="modalVisible = false"></Button>
                <Button type="button" :label="isEditing ? 'Actualizar' : 'Guardar'" @click="saveClient" :loading="form.processing"></Button>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>
