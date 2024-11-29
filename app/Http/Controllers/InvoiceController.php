<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Chantier;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

/**
 * Class InvoiceController
 * [SPECGT21]
 */
class InvoiceController extends Controller
{

    /**
     * Handle the invoices of a work site
     * Version : 2.2
     * [SPECGT21]
     * @param Request $request (id: int, number: string, return: boolean)
     * @return JsonResponse
     */
    public function handleInvoice(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id' => 'required|int',
            'number' => 'required|string',
            'return' => 'sometimes|boolean', // return field is optional, but must be a boolean if present
        ]);
        $invoiceData = [
            'number' => $validatedData['number'],
        ];
        $workSite = Chantier::find($validatedData['id']);

        // UPDATE
        $currentInvoice = $workSite->invoices()->first();
        try {
            if ($currentInvoice) {
                $updatedInvoice = $currentInvoice->replicate();
                $updatedInvoice->number = $validatedData['number'];
                $updatedInvoice->save();
                $updatedInvoice->cleanTrash($validatedData['id']);
                $currentInvoice->delete();
            }else{
                $workSite->invoices()->create($invoiceData);
            }
        }catch(\Exception $e){
            return response()->json(['errors' => 'Erreur lors de la mise à jour de la facture'], 403);
        }

        $return = $validatedData['return'] ?? false;
        $response = response()->json(['message' => 'Numéro de facture ajouté avec succès'], 200);
        if ($return) {
            $workSite = Chantier::where('id', $validatedData['id'])->with('states')->with('invoices')->first();
            $responseData = $response->getData(true);
            $responseData['additionalData'] = $workSite;
            $response->setData($responseData);
        }
        return $response;
    }

    /**
     * Delete an invoice with soft delete to keep a backup
     * Version : 2.2
     * [SPECGT15]
     * @param Request $request (id: int)
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id' => 'required|int',
        ]);
        $invoice = Invoice::where('work_site_id', $validatedData['id'])->first();
        if (!$invoice) {
            return response()->json(['errors' => 'Numéro de facture introuvable'], 403);
        }
        $copiedInvoice = $invoice->replicate();
        $workSite = Chantier::find($validatedData['id']);

        $copiedInvoice->number = null;
        $workSite->invoices()->save($copiedInvoice);
        $copiedInvoice->cleanTrash($validatedData['id']);
        $invoice->delete();

        return response()->json(['success' => 'Numéro de facture supprimé avec succès'], 200);
    }

    /**
     * Restore a trashed invoice to the current invoice
     * Version : 2.2
     * [SPECGT15]
     * @param Request $request (id: int)
     * @return JsonResponse
     */
    public function recovery(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id' => 'required|int',
        ]);
        // Get the trashed invoice
        $trashedInvoice = Invoice::onlyTrashed()->where('work_site_id', $validatedData['id'])->first();
        if (!$trashedInvoice) {
            return response()->json(['errors' => 'Aucune sauvegarde existante'], 200);
        }

        // Delete the current invoice (it will delete the trashed invoice too by replacing it)
        $currentInvoice = Invoice::where('work_site_id', $validatedData['id'])->first();
        if ($currentInvoice) {
            if ($trashedInvoice->number === null) {
                return response()->json(['errors' => 'Aucun numéro de facture à restaurer'], 200);
            }
            // Copy the trashed invoice to the current invoice
            $copiedInvoice = $currentInvoice->replicate();
            $copiedInvoice->number = $trashedInvoice->number;
        } else {
            // Create a new invoice with the trashed invoice data
            $copiedInvoice = $trashedInvoice->replicate();
        }
        $copiedInvoice->save();
        $copiedInvoice->cleanTrash($validatedData['id']);
        $currentInvoice->delete();
        return response()->json(['success' => 'Numéro de facture restauré avec succès', 'additionalData' => $copiedInvoice], 200);
    }
}
