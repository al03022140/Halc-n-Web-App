<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin,Purchasing');
    }

    public function addUpdate(Request $request, PurchaseRequest $purchaseRequest)
    {
        $data = $request->validate([
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,ordered,received',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('purchase_evidence', 'public');
        }

        $update = PurchaseUpdate::create([
            'purchase_request_id' => $purchaseRequest->id,
            'user_id' => Auth::id(),
            'status' => $data['status'] ?? null,
            'notes' => $data['notes'],
            'attachment_path' => $attachmentPath,
        ]);

        $statusChanged = ! empty($data['status']) && $data['status'] !== $purchaseRequest->status;
        if ($statusChanged) {
            $purchaseRequest->status = $data['status'];
            if ($data['status'] === 'received') {
                $purchaseRequest->resolved_at = now();
            }
            $purchaseRequest->save();
        }

        $message = match ($data['status'] ?? null) {
            'ordered' => 'Material marcado como comprado. Recuerda avisar a Almacén cuando llegue.',
            'received' => 'Recepción confirmada. Notifica a Warehouse para que avance el pedido.',
            default => 'Seguimiento registrado correctamente.',
        };

        return redirect()->route('purchasing.dashboard')->with('success', $message);
    }
}
