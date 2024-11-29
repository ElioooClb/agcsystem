<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Services\WorkSiteStatesService;
use App\Models\Chantier;
use App\Models\Invoice;
use Faker\Generator as Faker;
use Database\Factories\InvoiceFactory;

class WorkSiteStatesServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkSiteStatesService $workSiteStatesService;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->workSiteStatesService = new WorkSiteStatesService();
    }

    // --------------------------------------------------------------------

    /**
     * Test the factory initial state of a worksite when it is created
     */
    public function test_initial_state_of_work_site()
    {
        $this->initAndAssertWorkSite($this->createWorkSite('ST_FACT1A')->id, 'initial');
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a billable state
     */
    public function test_complete_final_progression_state_of_work_site()
    {
        $workSite = $this->createWorkSite('ST_FACT1A');
        $this->initAndAssertWorkSite($workSite->id, 'initial');
        $this->updateAndAssertState('billable');
        $this->addInvoiceToWorkSite();
        $this->assertNotNull($workSite->refresh()->invoices);
        $this->updateAndAssertState('pendingArchiving');
        $this->updateAndAssertState('archived');
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a partially billed state
     */
    public function test_complete_partial_progression_state_of_work_site()
    {
        $randomInvoiceNumber = rand(1, 3);

        $workSite = $this->createWorkSite('ST_FACT1A');
        $this->initAndAssertWorkSite($workSite->id, 'initial');
        $this->updateAndAssertState('partiallyBilled');
        $this->addInvoiceToWorkSite();
        $this->updateAndAssertInvoice($workSite);
        $this->updateAndAssertState('pendingApproval');

        for ($i = $randomInvoiceNumber; $i > 0; $i--) {
            $this->updateAndAssertState('partiallyBilled');
            $this->updateAndAssertInvoice($workSite);
            $this->updateAndAssertState('pendingApproval');
        }

        $this->updateAndAssertState('billable');
        $this->updateAndAssertState('pendingArchiving');
        $this->updateAndAssertState('archived');
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a billable state
     */
    public function test_complete_final_regression_state_of_work_site()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('billable', false);
        $this->updateAndAssertState('initial', false);
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a partially billed state
     */
    public function test_complete_final_partial_regression_state_of_work_site()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('billable', false);
        $this->updateAndAssertState('partiallyBilled', false);
        $this->updateAndAssertState('initial', false);
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a billable state
     */
    public function test_complete_partial_regression_state_of_work_site()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('billable', false);
        $this->updateAndAssertState('partiallyBilled', false);
        $this->updateAndAssertState('initial', false);
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a partially billed state
     */
    public function test_unauthorized_forward_transition_without_invoice_from_billable()
    {
        $workSite = $this->createWorkSite('ST_FACT2A');
        $this->initAndAssertWorkSite($workSite->id, 'billable');
        $this->updateAndAssertState('pendingArchiving', true, false);
    }

    /**
     * Test the progression of the state of a worksite from 'initial' to 'archived' from a partially billed state
     */
    public function test_unauthorized_forward_transition_without_invoice_from_partially_billed()
    {
        $workSite = $this->createWorkSite('ST_FACT2B');
        $this->initAndAssertWorkSite($workSite->id, 'partiallyBilled');
        $this->updateAndAssertState('pendingApproval', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the initial state
     */
    public function test_unauthorized_forward_transition_from_initial()
    {
        $workSite = $this->createWorkSite('ST_FACT1A');
        $this->initAndAssertWorkSite($workSite->id, 'initial');
        $this->updateAndAssertState('pendingApproval', true, false);
        $this->updateAndAssertState('pendingArchiving', true, false);
        $this->updateAndAssertState('archived', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the partially billed state
     */
    public function test_unauthorized_forward_transition_from_partially_billed()
    {
        $workSite = $this->createWorkSite('ST_FACT2B');
        $this->initAndAssertWorkSite($workSite->id, 'partiallyBilled');
        $this->updateAndAssertState('initial', true, false);
        $this->updateAndAssertState('billable', true, false);
        $this->updateAndAssertState('pendingArchiving', true, false);
        $this->updateAndAssertState('archived', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the pending approval state
     */
    public function test_unauthorized_forward_transition_from_pendingApproval()
    {
        $workSite = $this->createWorkSite('ST_FACT1B');
        $this->initAndAssertWorkSite($workSite->id, 'pendingApproval');
        $this->updateAndAssertState('initial', true, false);
        $this->updateAndAssertState('pendingArchiving', true, false);
        $this->updateAndAssertState('archived', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the billable state
     */
    public function test_unauthorized_forward_transition_from_billable()
    {
        $workSite = $this->createWorkSite('ST_FACT2A');
        $this->initAndAssertWorkSite($workSite->id, 'billable');
        $this->updateAndAssertState('initial', true, false);
        $this->updateAndAssertState('partiallyBilled', true, false);
        $this->updateAndAssertState('pendingApproval', true, false);
        $this->updateAndAssertState('archived', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the pending archiving state
     */
    public function test_unauthorized_forward_transition_from_pendingArchiving()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('initial', true, false);
        $this->updateAndAssertState('billable', true, false);
        $this->updateAndAssertState('partiallyBilled', true, false);
        $this->updateAndAssertState('pendingApproval', true, false);
    }

    /**
     * Test the unauthorized forward transitions from the pending archiving state
     */
    public function test_unauthorized_forward_transition_from_archived()
    {
        $workSite = $this->createWorkSite('ST_FACT1C');
        $this->initAndAssertWorkSite($workSite->id, 'archived');
        $this->updateAndAssertState('initial', true, false);
        $this->updateAndAssertState('billable', true, false);
        $this->updateAndAssertState('partiallyBilled', true, false);
        $this->updateAndAssertState('pendingApproval', true, false);
        $this->updateAndAssertState('pendingArchiving', true, false);
    }

    /**
     * Test the unauthorized backward transitions from the archived state
     */
    public function test_unauthorized_backward_transition_from_archived()
    {
        $workSite = $this->createWorkSite('ST_FACT1C');
        $this->initAndAssertWorkSite($workSite->id, 'archived');
        // This transition was allowed in the previous version and disabled in the current version
        // $this->updateAndAssertState('pendingArchiving', false, false);
        $this->updateAndAssertState('billable', false, false);
        $this->updateAndAssertState('partiallyBilled', false, false);
        $this->updateAndAssertState('pendingApproval', false, false);
        $this->updateAndAssertState('initial', false, false);
    }

    /**
     * Test the unauthorized backward transitions from the pending archiving state
     */
    public function test_unauthorized_backward_transition_from_pendingArchiving()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('archived', false, false);
        $this->updateAndAssertState('pendingApproval', false, false);
        $this->updateAndAssertState('partiallyBilled', false, false);
        $this->updateAndAssertState('initial', false, false);
    }

    /**
     * Test the unauthorized backward transitions from the billable state
     */
    public function test_unauthorized_backward_transition_from_billable()
    {
        $workSite = $this->createWorkSite('ST_FACT2A');
        $this->initAndAssertWorkSite($workSite->id, 'billable');
        $this->updateAndAssertState('archived', false, false);
        $this->updateAndAssertState('pendingArchiving', false, false);
        $this->updateAndAssertState('pendingApproval', false, false);
    }

    /**
     * Test the unauthorized backward transitions from the pending approval state
     */
    public function test_unauthorized_backward_transition_from_pendingApproval()
    {
        $workSite = $this->createWorkSite('ST_FACT1B');
        $this->initAndAssertWorkSite($workSite->id, 'pendingApproval');
        $this->updateAndAssertState('archived', false, false);
        $this->updateAndAssertState('pendingArchiving', false, false);
        $this->updateAndAssertState('billable', false, false);
        $this->updateAndAssertState('initial', false, false);
    }

    /**
     * Test the unauthorized backward transitions from the partially billed state
     */
    public function test_unauthorized_backward_transition_from_partiallyBilled()
    {
        $workSite = $this->createWorkSite('ST_FACT2B');
        $this->initAndAssertWorkSite($workSite->id, 'partiallyBilled');
        $this->updateAndAssertState('archived', false, false);
        $this->updateAndAssertState('pendingArchiving', false, false);
        $this->updateAndAssertState('billable', false, false);
        $this->updateAndAssertState('pendingApproval', false, false);
    }

    /**
     * Test the unauthorized backward transitions from the initial state
     */
    public function test_unauthorized_self_transition_from_initial()
    {
        $workSite = $this->createWorkSite('ST_FACT1A');
        $this->initAndAssertWorkSite($workSite->id, 'initial');
        $this->updateAndAssertState('initial', true, false, true);
        $this->updateAndAssertState('initial', false, false, true);
    }

    /**
     * Test the unauthorized self transitions from the billable state
     */
    public function test_unauthorized_self_transition_from_billable()
    {
        $workSite = $this->createWorkSite('ST_FACT2A');
        $this->initAndAssertWorkSite($workSite->id, 'billable');
        $this->updateAndAssertState('billable', true, false, true);
        $this->updateAndAssertState('billable', false, false, true);
    }

    /**
     * Test the unauthorized self transitions from the pending archiving state
     */
    public function test_unauthorized_self_transition_from_pendingArchiving()
    {
        $workSite = $this->createWorkSite('ST_FACT3A');
        $this->initAndAssertWorkSite($workSite->id, 'pendingArchiving');
        $this->updateAndAssertState('pendingArchiving', true, false, true);
        $this->updateAndAssertState('pendingArchiving', false, false, true);
    }

    /**
     * Test the unauthorized self transitions from the partially billed state
     */
    public function test_unauthorized_self_transition_from_partiallyBilled()
    {
        $workSite = $this->createWorkSite('ST_FACT2B');
        $this->initAndAssertWorkSite($workSite->id, 'partiallyBilled');
        $this->updateAndAssertState('partiallyBilled', true, false, true);
        $this->updateAndAssertState('partiallyBilled', false, false, true);
    }

    /**
     * Test the unauthorized self transitions from the pending approval state
     */
    public function test_unauthorized_self_transition_from_pendingApproval()
    {
        $workSite = $this->createWorkSite('ST_FACT1B');
        $this->initAndAssertWorkSite($workSite->id, 'pendingApproval');
        $this->updateAndAssertState('pendingApproval', true, false, true);
        $this->updateAndAssertState('pendingApproval', false, false, true);
    }

    /**
     * Test the unauthorized self transitions from the archived state
     */
    public function test_unauthorized_self_transition_from_archived()
    {
        $workSite = $this->createWorkSite('ST_FACT1C');
        $this->initAndAssertWorkSite($workSite->id, 'archived');
        $this->updateAndAssertState('archived', true, false, true);
        $this->updateAndAssertState('archived', false, false, true);
    }

    // ========================================
    // Private functions
    // ========================================

    /**
     * Create a worksite with a specific state
     * @param $state
     * @return Chantier
     */
    private function createWorkSite($state): Chantier
    {
        return Chantier::factory()->create([
            'state' => $state,
        ]);

    }

    /**
     * Initialize a worksite and assert the state
     * @param $chantierId
     * @param $expectedState
     * @return void
     */
    private function initAndAssertWorkSite($workSiteId, $expectedState): void
    {
        $this->workSiteStatesService->initWorkSite($workSiteId);
        $state = $this->workSiteStatesService->getState()->refresh();
        $this->assertEquals($expectedState, $state->status);
    }

    /**
     * Update the state of a worksite and assert the new state
     * @param $newState
     * @return void
     */
    private function updateAndAssertState($newState, $isForward = true, $isValid = true, $isSelf = false): void
    {
        $oldState = $this->workSiteStatesService->getState()->status;
        $response = $isForward ? $this->workSiteStatesService->updateState($newState) : $this->workSiteStatesService->downgradeState($newState);
        $this->assertJson($response->getContent());
        if ($isValid) {
            $this->assertArrayHasKey('success', $response->getData(true));
            $this->workSiteStatesService->getWorkSite()->refresh();
            $state = $this->workSiteStatesService->getState();
            $this->assertEquals($newState, $state->status);
        } else {
            $this->assertArrayHasKey('errors', $response->getData(true));
            $this->workSiteStatesService->getWorkSite()->refresh();
            $state = $this->workSiteStatesService->getState();
            $isSelf ? $this->assertEquals($newState, $state->status) : $this->assertNotEquals($newState, $state->status);
            $this->assertEquals($oldState, $state->status);
        }
    }

    /**
     * Add an invoice to a worksite
     * @param $worksite
     * @return void
     */
    private function addInvoiceToWorkSite(): void
    {
        $workSite = $this->workSiteStatesService->getWorkSite();
        $invoiceFactory = InvoiceFactory::new();
        $number = $invoiceFactory->generateFormattedString();
        $workSite->invoices->number = $number;
        $workSite->invoices->save();
        $workSite->refresh();
        $this->workSiteStatesService->getWorkSite()->refresh();
        $this->assertNotNull($workSite->invoices);
        $this->assertNotNull($workSite->invoices->number);
    }

    /**
     * Update the invoice number of a worksite
     * @param $worksite
     * @return void
     */
    private function updateAndAssertInvoice($worksite): void
    {
        $invoiceFactory = InvoiceFactory::new();
        $newInvoiceNumber = $invoiceFactory->generateFormattedString();
        $worksite->invoices->number = $newInvoiceNumber;
        $worksite->invoices->save();
        $this->assertInvoice($worksite, $newInvoiceNumber);
    }

    /**
     * Assert the invoice of a worksite
     * @param $worksite
     * @param $invoiceNumber
     * @return void
     */
    private function assertInvoice($worksite, $invoiceNumber): void
    {
        $this->assertNotNull($worksite->invoices);
        $this->assertNotNull($invoiceNumber);
        $this->assertEquals($invoiceNumber, $worksite->invoices->number);
        $this->assertNotNull($worksite->invoices->filled_at);
        $this->assertNotNull($worksite->invoices->requested_at);
    }
}
