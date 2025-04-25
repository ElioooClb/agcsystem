<?php

namespace App\Listeners;

use App\Events\WorksiteStageUpdated;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class SendWorksiteStageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private EmailService $emailService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Handle the event.
     */
    public function handle(WorksiteStageUpdated $event): void
    {
        $worksite = $event->worksite;
        $oldStage = $event->oldStage;
        $newStage = $event->newStage;
        $direction = $event->direction;

        // Configuration de l'email en fonction du stage
        $config = $this->getEmailConfig($oldStage, $newStage, $direction);

        if (!$config) {
            return;
        }

        // Définir dynamiquement le titre et le message
        $message_title = '';
        $message_content = '';

        // Exemple de logique pour ajuster le titre et le message en fonction des stages
        switch ($oldStage) {
            case 'STAGE_1A':
                $message_title = '🔔 Demande de bon de travaux';
                $message_content = 'Le bon de travaux a été demandé. Veuillez vérifier les informations.';
                break;

            case 'STAGE_1B':
                $message_title = '✅ Bon de travaux validé';
                $message_content = 'Le bon de travaux a été validé par l\'administration.';
                break;

            case 'STAGE_1C':
                $message_title = '🔄 Demande de vérification du bon de travaux';
                $message_content = 'Veuillez procéder à la vérification du bon de travaux.';
                break;

            default:
                $message_title = 'Notification de chantier';
                $message_content = 'Une mise à jour du bon de travaux a été effectuée.';
                break;
        }

        // Préparation des données du chantier
        $worksiteData = [
            'title' => $worksite->title,
            'idaff' => $worksite->getIdAff(),
            'hours' => $worksite->hours,
            'materialamount' => $worksite->montantmateriel,
            'serviceamount' => $worksite->montantservice,
            'type' => $worksite->type(),
            'supervisor' => Auth::user()->name,
            'updated_at' => $worksite->updated_at->format('d/m/Y H:i'),
            'message_title' => $message_title,
            'message_content' => $message_content,
        ];
        // Envoi de l'email avec les données préparées
        $this->emailService->sendEmailNotification($config, $worksiteData);
    }

    /**
     * Détermine la configuration de l'email en fonction des stages
     */
    private function getEmailConfig(string $oldStage, string $newStage, string $direction): ?array
    {
        // Configuration par défaut pour les transitions
        $configs = [
            'STAGE_1A' => [
                'forward' => [
                    'to' => ['worker_02'],
                    'template' => 'emails.workorder.notification',
                    'subject' => '🔔 Demande de bon de travaux',
                ],
            ],
            'STAGE_1B' => [
                'forward' => [
                    'to' => ['worker_01'],
                    'template' => 'emails.workorder.notification',
                    'subject' => '✅ Bon de travaux validé',
                ],
            ],
            'STAGE_1C' => [
                'backward' => [
                    'to' => ['worker_02'],
                    'template' => 'emails.workorder.notification',
                    'subject' => '🔄 Demande de vérification du bon de travaux',
                ],
            ],
        ];
        return $configs[$oldStage][$direction] ?? null;
    }
}
