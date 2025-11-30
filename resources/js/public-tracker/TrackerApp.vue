<template>
    <div class="card shadow-lg border-0 h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0">Rastreador de pedidos</h3>
                <small class="text-muted">Ingresa tu ID personalizado y la factura para consultar el estatus sin recargar.</small>
            </div>
            <!-- <span class="badge bg-primary text-uppercase">Vue + API</span> -->
        </div>
        <div class="card-body p-4">
            <form @submit.prevent="onSubmit" class="row g-3">
                <div class="col-md-6">
                    <label for="invoice" class="form-label">Número de factura</label>
                    <input
                        id="invoice"
                        v-model.trim="form.invoice_number"
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="Ej. FAC-12345"
                        :disabled="loading"
                        required
                    />
                    <div v-if="errors.invoice_number" class="text-danger small mt-1">
                        {{ errors.invoice_number[0] }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="customer" class="form-label">ID personalizado del cliente</label>
                    <input
                        id="customer"
                        v-model.trim="form.customer_custom_id"
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="Ej. CLI-001"
                        :disabled="loading"
                        required
                    />
                    <div v-if="errors.customer_custom_id" class="text-danger small mt-1">
                        {{ errors.customer_custom_id[0] }}
                    </div>
                </div>
                <div class="col-12 d-grid d-md-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Consultar pedido
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-lg" :disabled="loading" @click="resetForm">
                        Limpiar
                    </button>
                </div>
            </form>

            <div v-if="message" class="alert alert-danger mt-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ message }}
            </div>

            <div v-if="result" class="mt-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5 class="text-muted mb-2">Pedido</h5>
                                <p class="mb-1"><strong>Factura:</strong> {{ result.invoice_number }}</p>
                                <p class="mb-1"><strong>Cliente:</strong> {{ result.customer_name }}</p>
                                <p class="mb-0"><strong>Fecha:</strong> {{ result.order_date_label }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-muted mb-2">Estado actual</h5>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        :style="statusBubbleStyle"
                                        style="width: 64px; height: 64px;"
                                    >
                                        <i class="fas fa-2x" :class="statusIcon"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ result.status.name }}</h4>
                                        <small class="text-muted">Actualizado {{ result.updated_at_label }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="position-relative" style="padding-top: 22px;">
                        <div class="progress position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 1;">
                            <div
                                class="progress-bar"
                                role="progressbar"
                                :style="{ width: progressPercent + '%' }"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.key"
                                class="text-center"
                                style="flex: 1;"
                            >
                                <div
                                    class="mx-auto rounded-circle"
                                    :class="index <= activeIndex ? 'bg-primary' : 'bg-secondary'"
                                    style="width: 18px; height: 18px; position: relative; top: -10px; z-index: 3; border: 2px solid #fff;"
                                ></div>
                                <div class="small mt-2">{{ step.label }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="result.evidence_url" class="text-center">
                    <h5>Evidencia de entrega</h5>
                    <img :src="result.evidence_url" alt="Evidencia de entrega" class="img-fluid rounded shadow" style="max-height: 320px;">
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';

const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
});

const form = reactive({
    invoice_number: '',
    customer_custom_id: '',
});

const errors = ref({});
const message = ref(null);
const loading = ref(false);
const result = ref(null);

const fallbackSteps = [
    { key: 'Ordered', label: 'Ordenado' },
    { key: 'In process', label: 'En proceso' },
    { key: 'In route', label: 'En ruta' },
    { key: 'Delivered', label: 'Entregado' },
];

const steps = computed(() => result.value?.steps?.length ? result.value.steps : fallbackSteps);
const activeIndex = computed(() => {
    if (typeof result.value?.active_step_index === 'number') {
        return result.value.active_step_index;
    }
    return 0;
});
const progressPercent = computed(() => {
    if (steps.value.length <= 1) {
        return 0;
    }
    const percentage = (activeIndex.value / (steps.value.length - 1)) * 100;
    return Math.min(100, Math.max(0, percentage));
});

const statusBubbleStyle = computed(() => {
    const color = result.value?.status?.color || '#0d6efd';
    return {
        backgroundColor: color,
        color: '#fff',
    };
});

const statusIcon = computed(() => {
    const status = (result.value?.status?.name || '').toLowerCase();
    if (status.includes('route')) return 'fa-truck';
    if (status.includes('process')) return 'fa-cogs';
    if (status.includes('deliver')) return 'fa-check-circle';
    return 'fa-clipboard-list';
});

const resetForm = () => {
    form.invoice_number = '';
    form.customer_custom_id = '';
    errors.value = {};
    message.value = null;
    result.value = null;
};

const onSubmit = async () => {
    errors.value = {};
    message.value = null;
    loading.value = true;

    try {
        const response = await fetch(props.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(form),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 422 && payload.errors) {
                errors.value = payload.errors;
            } else {
                message.value = payload.message || 'No se pudo recuperar la información del pedido.';
            }
            return;
        }

        result.value = payload.data;
    } catch (error) {
        message.value = 'No se pudo contactar al servidor. Inténtalo nuevamente en unos segundos.';
    } finally {
        loading.value = false;
    }
};
</script>
