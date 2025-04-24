document.addEventListener('DOMContentLoaded', () => {
    // Fonction pour initialiser l'affichage des boutons
    const initDisplay = () => {
        document.querySelectorAll('.staging-forward-btn, .staging-backward-btn').forEach(btn => {
            const currentStage = btn.getAttribute('data-current-stage');
            const direction = btn.getAttribute('data-direction');
            
            if ((currentStage === 'STAGE_1A' && direction === 'backward') || 
                (currentStage === 'STAGE_1C' && direction === 'forward')) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline-block';
            }
        });
    };

    // Exécution initiale pour régler l'affichage des boutons au chargement
    initDisplay();

    const extractBtnAttributes = (btn) => {
        const chantierId = btn.getAttribute('data-id');
        const nextStage = btn.getAttribute('data-next-stage');
        const currentStage = btn.getAttribute('data-current-stage');
        const direction = btn.getAttribute('data-direction');
        return { chantierId, nextStage, currentStage, direction };
    };

    // Fonction pour mettre à jour l'affichage du stage dans la modal
    const updateStageDisplay = (chantierId, newStage) => {
        const stageLabels = {
            'STAGE_1A': 'Dossier non commencé',
            'STAGE_1B': 'Dossier en cours',
            'STAGE_1C': 'Dossier terminé'
        };
        
        const stageColors = {
            'STAGE_1A': 'bg-danger',
            'STAGE_1B': 'bg-warning',
            'STAGE_1C': 'bg-success'
        };

        const stageLabel = document.querySelector(`.stage-label[data-id="${chantierId}"]`);
        const stageIndicator = document.querySelector(`.stage-indicator[data-id="${chantierId}"]`);
        const modalStageIndicator = document.querySelector(`.modal-stage-indicator[data-id="${chantierId}"]`);

        if (stageLabel && stageIndicator && modalStageIndicator) {
            stageLabel.textContent = stageLabels[newStage];
            stageIndicator.className = `stage-indicator absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border border-dark ${stageColors[newStage]}`;
            modalStageIndicator.className = `modal-stage-indicator w-3 h-3 rounded-full border border-dark ${stageColors[newStage]}`;
        }
    };

    // Fonction pour mettre à jour les attributs des boutons
    const updateButtonAttributes = (chantierId, newStage) => {
        const forwardTransitions = {
            'STAGE_1A': 'STAGE_1B',
            'STAGE_1B': 'STAGE_1C',
            'STAGE_1C': 'STAGE_1C'
        };
        
        const backwardTransitions = {
            'STAGE_1A': 'STAGE_1A',
            'STAGE_1B': 'STAGE_1A',
            'STAGE_1C': 'STAGE_1B'
        };

        const forwardBtn = document.querySelector(`.staging-forward-btn[data-id="${chantierId}"]`);
        const backwardBtn = document.querySelector(`.staging-backward-btn[data-id="${chantierId}"]`);

        if (forwardBtn) {
            forwardBtn.setAttribute('data-current-stage', newStage);
            forwardBtn.setAttribute('data-next-stage', forwardTransitions[newStage]);
            
            // Masquer le bouton forward si on est à l'état final
            if (newStage === 'STAGE_1C') {
                forwardBtn.style.display = 'none';
            } else {
                forwardBtn.style.display = 'inline-block';
            }
        }

        if (backwardBtn) {
            backwardBtn.setAttribute('data-current-stage', newStage);
            backwardBtn.setAttribute('data-next-stage', backwardTransitions[newStage]);
            
            // Masquer le bouton backward si on est à l'état initial
            if (newStage === 'STAGE_1A') {
                backwardBtn.style.display = 'none';
            } else {
                backwardBtn.style.display = 'inline-block';
            }
        }
    };

    // Fonction pour gérer le clic sur les boutons
    const handleStageButtonClick = (e) => {
        e.preventDefault();
        const btn = e.currentTarget;
        const { chantierId, nextStage, currentStage, direction } = extractBtnAttributes(btn);

        // Vérification supplémentaire des états
        if ((currentStage === 'STAGE_1A' && direction === 'backward') || 
            (currentStage === 'STAGE_1C' && direction === 'forward')) {
            flashAlert('error', 'Cette transition n\'est pas autorisée');
            return;
        }

        const messages = {
            'forward': {
                'STAGE_1A': 'Voulez-vous vraiment passer ce chantier en validation ?',
                'STAGE_1B': 'Voulez-vous vraiment archiver ce chantier ?'
            },
            'backward': {
                'STAGE_1B': 'Voulez-vous vraiment revenir à l\'étape initiale ?',
                'STAGE_1C': 'Voulez-vous vraiment réactiver ce chantier ?'
            }
        };

        const message = messages[direction][currentStage];
        
        confirmationAlert(message)
            .then((result) => {
                if (result) {
                    const url = `/handle-stage`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            chantierId: chantierId,
                            direction: direction,
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mise à jour des attributs des boutons et de l'affichage
                            updateStageDisplay(chantierId, nextStage);
                            updateButtonAttributes(chantierId, nextStage);
                            
                            flashAlert('success', `Le chantier a été ${direction === 'forward' ? 'passé' : 'retourné'} à l'étape ${nextStage}`);
                        } else {
                            flashAlert('error', data.message || 'Une erreur est survenue lors de la transition');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        flashAlert('error', 'Une erreur est survenue lors de la transition');
                    });
                } else {
                    flashAlert('info', 'Opération annulée');
                }
            });
    };

    // Attacher les événements aux boutons
    document.querySelectorAll('.staging-forward-btn, .staging-backward-btn').forEach(btn => {
        btn.addEventListener('click', handleStageButtonClick);
    });

    // Pour gérer l'affichage des boutons quand les modales s'ouvrent
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => {
            // Attendre que la modale soit ouverte pour initialiser les boutons
            setTimeout(initDisplay, 100);
        });
    });
});