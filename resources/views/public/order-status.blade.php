@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 text-center">Estado de tu Pedido</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Halcon Logo" class="img-fluid" style="max-height: 80px;">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Información del Pedido</h5>
                            <p><strong>Número de Factura:</strong> {{ $order->invoice_number }}</p>
                            <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                            <p><strong>ID personalizado:</strong> {{ $order->customer_custom_id ?? 'No asignado' }}</p>
                            <p><strong>Fecha del Pedido:</strong> {{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : date('d/m/Y', strtotime($order->created_at)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Estado Actual</h5>
                            <div class="d-flex align-items-center">
                                <div class="p-3 rounded-circle 
                                    @if($order->status->name == 'Ordered') bg-info
                                    @elseif($order->status->name == 'In process') bg-warning
                                    @elseif($order->status->name == 'In route') bg-primary
                                    @elseif($order->status->name == 'Delivered') bg-success
                                    @else bg-secondary @endif
                                    text-white me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas 
                                        @if($order->status->name == 'Ordered') fa-clipboard-list
                                        @elseif($order->status->name == 'In process') fa-cogs
                                        @elseif($order->status->name == 'In route') fa-truck
                                        @elseif($order->status->name == 'Delivered') fa-check-circle
                                        @else fa-question-circle @endif
                                        fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $order->status->name }}</h4>
                                    <p class="text-muted mb-0">Última actualización: {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seguimiento del pedido (pasos) -->
                    @php
                        $steps = ['Ordered','In process','In route','Delivered'];
                        $labels = ['Ordenado','En proceso','En ruta','Entregado'];
                        $activeIndex = array_search($order->status->name, $steps);
                        if ($activeIndex === false) { $activeIndex = 0; }
                        $progressPercent = ($activeIndex / (count($steps) - 1)) * 100; // 0,33,66,100
                    @endphp
                    <div class="mb-4">
                        <div class="position-relative" style="padding-top: 22px;">
                            <div class="progress position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 1;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                @foreach($steps as $i => $step)
                                    <div class="text-center" style="flex: 1;">
                                        <div class="mx-auto rounded-circle {{ $i <= $activeIndex ? 'bg-primary' : 'bg-secondary' }}" style="width: 18px; height: 18px; position: relative; top: -10px; z-index: 3; border: 2px solid #fff;"></div>
                                        <div class="small mt-2">{{ $labels[$i] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($order->end_image)
                    <div class="mt-4">
                        <h5>Evidencia de Entrega</h5>
                        <div class="text-center">
                            <a href="{{ asset('storage/' . $order->end_image) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/' . $order->end_image) }}" alt="Evidencia de entrega" class="img-fluid rounded shadow" style="max-height: 300px;">
                            </a>
                            <p class="small text-muted mt-2">Haz clic en la imagen para verla a tamaño completo.</p>
                        </div>
                    </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('track.index') }}" class="btn btn-outline-primary">Consultar otro pedido</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection