<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['club', 'payments'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->when($request->club_id, fn($q, $clubId) => $q->where('issued_to_club_id', $clubId))
            ->when($request->overdue, fn($q) =>
                $q->where('status', '!=', 'paid')->where('due_date', '<', now())
            )
            ->orderBy('created_at', 'desc');

        $invoices = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($invoices);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['club', 'user', 'payments']);
        $invoice->append(['total_paid', 'balance']);

        return response()->json($invoice);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'description' => 'required|string|max:500',
            'category' => 'required|string',
            'amount_cents' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'due_date' => 'required|date|after:today',
            'issued_to_club_id' => 'nullable|exists:clubs,id',
            'issued_to_user_id' => 'nullable|exists:users,id',
            'line_items' => 'nullable|array',
        ]);

        $validated['invoice_number'] = $this->generateInvoiceNumber();
        $validated['status'] = 'sent';
        $validated['created_by'] = $request->user()->id;

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    public function cancel(Invoice $invoice): JsonResponse
    {
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Cannot cancel paid invoice'], 422);
        }

        $invoice->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Invoice cancelled']);
    }

    private function generateInvoiceNumber(): string
    {
        $count = Invoice::whereDate('created_at', today())->count() + 1;
        return sprintf('INV-%s-%06d', date('Ymd'), $count);
    }
}
