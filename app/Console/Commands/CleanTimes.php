<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime les enregistrements spécifiques dans la table `times` où les conditions sont remplies.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 🔹 Requête SQL pour supprimer les enregistrements
        $sql = "
            DELETE FROM `times` 
            WHERE hours_day = 0
            AND hours_night = 0
            AND hours_travel = 0
            AND state IS NULL
            AND on_business_trip = 0
            AND unbillable = 0
            AND note IS NULL;
        ";

        try {
            // 🔹 Exécuter la requête DELETE
            $deletedCount = DB::delete($sql); // Utiliser DB::delete pour la suppression

            if ($deletedCount === 0) {
                $this->info('✅ Aucune ligne supprimée : aucun enregistrement ne correspond aux critères.');
            } else {
                $this->info("✅ $deletedCount ligne(s) supprimée(s) avec succès.");
            }
        } catch (\Exception $e) {
            // 🔹 Gérer les erreurs SQL
            $this->error('❌ Erreur lors de la suppression des enregistrements : ' . $e->getMessage());
        }
    }
}
