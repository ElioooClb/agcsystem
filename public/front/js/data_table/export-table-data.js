/*Export Table Init*/

"use strict";

$(document).ready(function () {
    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: 'Copier'
            },
            {
                extend: 'csv',
                text: 'CSV',
                exportOptions: {
                    columns: ':not(.no-export)', // Exclure les colonnes avec la classe .no-export
                    // DEBUT - [SPECGT9] - Exporter les colonnes visibles uniquement
                    modifier: {
                        visible: 'true', // Exporte uniquement les données visibles (après filtrage ou tri)
                        order: 'applied' // Exporte uniquement les données triées selon le tri actuel
                    }
                    // FIN - [SPECGT9] - Exporter les colonnes visibles uniquement
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: ':not(.no-export)',
                    // DEBUT - [SPECGT9] - Exporter les colonnes visibles uniquement
                    modifier: {
                        visible: 'true', // Exporte uniquement les données visibles (après filtrage ou tri)
                        order: 'applied' // Exporte uniquement les données triées selon le tri actuel
                    }
                    // FIN - [SPECGT9] - Exporter les colonnes visibles uniquement
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: ':not(.no-export)',
                    // DEBUT - [SPECGT9] - Exporter les colonnes visibles uniquement
                    modifier: {
                        visible: 'applied', // Exporte uniquement les données visibles (après filtrage ou tri)
                        order: 'applied' // Exporte uniquement les données triées selon le tri actuel
                    }
                    // FIN - [SPECGT9] - Exporter les colonnes visibles uniquement
                }
            },
            {
                extend: 'print',
                text: 'Imprimer',
                exportOptions: {
                    columns: ':not(.no-export)',
                    // DEBUT - [SPECGT9] - Exporter les colonnes visibles uniquement
                    modifier: {
                        visible: 'applied', // Exporte uniquement les données visibles (après filtrage ou tri)
                        order: 'applied' // Exporte uniquement les données triées selon le tri actuel
                    }
                    // FIN - [SPECGT9] - Exporter les colonnes visibles uniquement
                }
            }
        ],
        "language": {
            "sProcessing": "Traitement en cours ...",
            "sLengthMenu": "Afficher _MENU_ lignes",
            "sZeroRecords": "Aucun résultat trouvé",
            "sEmptyTable": "Aucune donnée disponible",
            "sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
            "sInfoEmpty": "Aucune ligne affichée",
            "sInfoFiltered": "(Filtrer un maximum de_MAX_)",
            "sInfoPostFix": "",
            "sSearch": "Rechercher:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Chargement...",
            "oPaginate": {
                "sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": "Précédent"
            },
            "oAria": {
                "sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
            }
        }
    });
});


