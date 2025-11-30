@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0"><i class="fas fa-cog text-primary me-2"></i>Parámetros del Sistema</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Regresar al panel
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.system.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="invoice_prefix" class="form-label">Prefijo de facturas</label>
                    <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $settings['invoice_prefix']) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="invoice_next_number" class="form-label">Próximo consecutivo</label>
                    <input type="number" name="invoice_next_number" id="invoice_next_number" class="form-control" min="1" value="{{ old('invoice_next_number', $settings['invoice_next_number']) }}" required>
                    <small class="text-muted">Se incrementa automáticamente cada vez que se crea una orden.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Validaciones obligatorias</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="require_fiscal_data" id="require_fiscal_data" value="1" {{ old('require_fiscal_data', $settings['require_fiscal_data']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="require_fiscal_data">Requerir datos fiscales</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="require_delivery_address" id="require_delivery_address" value="1" {{ old('require_delivery_address', $settings['require_delivery_address']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="require_delivery_address">Requerir dirección de entrega</label>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-1">
                <div class="col-md-6">
                    <label for="purchasing_alert_email" class="form-label">Correo de alertas de compras</label>
                    <input type="email" name="purchasing_alert_email" id="purchasing_alert_email" class="form-control" placeholder="compras@empresa.com" value="{{ old('purchasing_alert_email', $settings['purchasing_alert_email']) }}">
                    <small class="text-muted">Recibirá correos cuando el inventario baje del nivel mínimo.</small>
                </div>
                <div class="col-md-6">
                    <label for="slack_stock_webhook" class="form-label">Webhook de Slack para alertas</label>
                    <input type="url" name="slack_stock_webhook" id="slack_stock_webhook" class="form-control" placeholder="https://hooks.slack.com/services/..." value="{{ old('slack_stock_webhook', $settings['slack_stock_webhook']) }}">
                    <small class="text-muted">Opcional. Envía alertas de inventario al canal configurado.</small>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save me-2"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
