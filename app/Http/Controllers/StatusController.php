<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = OrderStatus::all();
        return view('statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('statuses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:order_statuses,name',
            'color' => 'nullable|string|max:20',
        ]);

        OrderStatus::create($data);
        return redirect()->route('statuses.index')->with('success', 'Estado creado correctamente.');
    }

    public function show(OrderStatus $status)
    {
        return view('statuses.show', compact('status'));
    }

    public function edit(OrderStatus $status)
    {
        return view('statuses.edit', compact('status'));
    }

    public function update(Request $request, OrderStatus $status)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:order_statuses,name,' . $status->id,
            'color' => 'nullable|string|max:20',
        ]);

        $status->update($data);
        return redirect()->route('statuses.index')->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(OrderStatus $status)
    {
        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'Estado eliminado.');
    }
}