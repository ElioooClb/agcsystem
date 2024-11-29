<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    // DEBUT [SPECGT9] - Work site invoice management
    use HasFactory, SoftDeletes;
    protected $table = 'invoices';
    protected $fillable = [
        'number',
        'filled_at',
        'requested_at',
    ];
    // FIN [SPECGT9] - Work site invoice management

    /**
     * This function listens for the CRUD operations on the invoice table
     * Listens if the invoice number has been modified or if the invoice is new to fill the invoice
     * Version: 2.2
     * [SPECGT21]
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($invoice) {
            // Check if the invoice number has been modified or if the invoice is new
            if ($invoice->number !== null && $invoice->isDirty('number')) {
                $invoice->filled_at = Carbon::now();
            }
        });
    }

    /**
     * This function call for the work site table dependencies of the invoice
     * Version: 2.1
     * [SPECGT9]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chantiers()
    {
        return $this->belongsTo(Chantier::class, 'work_site_id', 'id');
    }

    /**
     * Update or create an invoice
     * Version: 2.2
     * [SPECGT21]
     * @param array $invoiceData - Invoice data ex: ['number' => '123456', 'filled_at' => '2024-05-02', 'requested_at' => '2024-05-02']
     * @return void
     */
    public static function updateOrCreateInvoice($workSite, $invoiceData)
    {
        DB::transaction(function () use ($workSite, $invoiceData) {
            $invoice = $workSite->invoices;
            if ($invoice) {
                $invoice->update($invoiceData);
            } else {
                $workSite->invoices()->create($invoiceData);
            }
        });
    }

    public function cleanTrash($id)
    {
        $trashedInvoice = Invoice::onlyTrashed()->where('work_site_id', $id)->first();
        if ($trashedInvoice) {
            $trashedInvoice->forceDelete();
        }
    }
}
