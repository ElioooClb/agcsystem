/**
 * StageManager - Manages the stage transitions for construction projects
 * Handles progression through stages STAGE_1A, STAGE_1B, and STAGE_1C
 */
class StageManager {
    constructor() {
      this.stageLabels = {
        'STAGE_1A': 'Bon de travaux non commencé',
        'STAGE_1B': 'Bon de travaux en cours',
        'STAGE_1C': 'Bon de travaux terminé'
      };
      
      this.stageColors = {
        'STAGE_1A': 'bg-danger',
        'STAGE_1B': 'bg-warning',
        'STAGE_1C': 'bg-success'
      };
      
      this.forwardTransitions = {
        'STAGE_1A': 'STAGE_1B',
        'STAGE_1B': 'STAGE_1C',
        'STAGE_1C': 'STAGE_1C'
      };
      
      this.backwardTransitions = {
        'STAGE_1A': 'STAGE_1A',
        'STAGE_1B': 'STAGE_1A',
        'STAGE_1C': 'STAGE_1B'
      };
      
      this.confirmationMessages = {
        'forward': {
          'STAGE_1A': 'Voulez-vous commencer le bon de travaux ?',
          'STAGE_1B': 'Voulez-vous passer le bon de travaux en terminé ?'
        },
        'backward': {
          'STAGE_1B': 'Voulez-vous revenir à l\'étape "Bon de travaux non commencé" ?',
          'STAGE_1C': 'Voulez-vous revenir à l\'étape "Bon de travaux en cours" ?'
        }
      };
  
      this.init();
    }
  
    /**
     * Initialize the stage manager
     */
    init() {
      // Initialize button display
      this.initDisplay();
      
      // Set up event listeners
      this.attachEventListeners();
    }
  
    /**
     * Initialize the display of stage buttons
     */
    initDisplay() {
      document.querySelectorAll('.staging-forward-btn, .staging-backward-btn').forEach(btn => {
        const currentStage = btn.getAttribute('data-current-stage');
        const direction = btn.getAttribute('data-direction');
        
        // Hide forward button at final stage and backward button at initial stage
        if ((currentStage === 'STAGE_1A' && direction === 'backward') || 
            (currentStage === 'STAGE_1C' && direction === 'forward')) {
          btn.style.display = 'none';
        } else {
          btn.style.display = 'inline-block';
        }
      });
    }
  
    /**
     * Attach event listeners to buttons and modal triggers
     */
    attachEventListeners() {
      // Stage transition buttons
      document.querySelectorAll('.staging-forward-btn, .staging-backward-btn').forEach(btn => {
        btn.addEventListener('click', (e) => this.handleStageButtonClick(e));
      });
  
      // Modal opener buttons - ensure buttons display correctly when modals open
      document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => {
          // Wait for modal to open before initializing buttons
          setTimeout(() => this.initDisplay(), 100);
        });
      });
    }
  
    /**
     * Extract button attributes into an object
     * @param {HTMLElement} btn - The button element
     * @returns {Object} Object containing button attributes
     */
    extractBtnAttributes(btn) {
      return {
        chantierId: btn.getAttribute('data-id'),
        nextStage: btn.getAttribute('data-next-stage'),
        currentStage: btn.getAttribute('data-current-stage'),
        direction: btn.getAttribute('data-direction')
      };
    }
  
    /**
     * Update the stage display in UI
     * @param {string} chantierId - ID of the chantier
     * @param {string} newStage - New stage value
     */
    updateStageDisplay(chantierId, newStage) {
      const stageLabel = document.querySelector(`.stage-label[data-id="${chantierId}"]`);
      const stageIndicator = document.querySelector(`.stage-indicator[data-id="${chantierId}"]`);
      const modalStageIndicator = document.querySelector(`.modal-stage-indicator[data-id="${chantierId}"]`);
  
      if (stageLabel) {
        stageLabel.textContent = this.stageLabels[newStage];
      }
      
      if (stageIndicator) {
        stageIndicator.className = `stage-indicator absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border border-dark ${this.stageColors[newStage]}`;
      }
      
      if (modalStageIndicator) {
        modalStageIndicator.className = `modal-stage-indicator w-3 h-3 rounded-full border border-dark ${this.stageColors[newStage]}`;
      }
    }
  
    /**
     * Update button attributes after stage transition
     * @param {string} chantierId - ID of the chantier
     * @param {string} newStage - New stage value
     */
    updateButtonAttributes(chantierId, newStage) {
      const forwardBtn = document.querySelector(`.staging-forward-btn[data-id="${chantierId}"]`);
      const backwardBtn = document.querySelector(`.staging-backward-btn[data-id="${chantierId}"]`);
  
      if (forwardBtn) {
        forwardBtn.setAttribute('data-current-stage', newStage);
        forwardBtn.setAttribute('data-next-stage', this.forwardTransitions[newStage]);
        
        // Hide forward button at final stage
        forwardBtn.style.display = newStage === 'STAGE_1C' ? 'none' : 'inline-block';
      }
  
      if (backwardBtn) {
        backwardBtn.setAttribute('data-current-stage', newStage);
        backwardBtn.setAttribute('data-next-stage', this.backwardTransitions[newStage]);
        
        // Hide backward button at initial stage
        backwardBtn.style.display = newStage === 'STAGE_1A' ? 'none' : 'inline-block';
      }
    }
  
    /**
     * Handle click on stage transition buttons
     * @param {Event} e - Click event
     */
    handleStageButtonClick(e) {
      e.preventDefault();
      const btn = e.currentTarget;
      const { chantierId, nextStage, currentStage, direction } = this.extractBtnAttributes(btn);
  
      // Check if transition is allowed
      if ((currentStage === 'STAGE_1A' && direction === 'backward') || 
          (currentStage === 'STAGE_1C' && direction === 'forward')) {
        flashAlert('error', 'Cette transition n\'est pas autorisée');
        return;
      }
  
      // Get confirmation message based on current stage and direction
      const message = this.confirmationMessages[direction][currentStage];
      
      // Show confirmation dialog
      confirmationAlert(message)
        .then((result) => {
          if (result) {
            this.performStageTransition(chantierId, nextStage, direction);
          } else {
            flashAlert('info', 'Opération annulée');
          }
        });
    }
  
    /**
     * Send stage transition request to server
     * @param {string} chantierId - ID of the chantier
     * @param {string} nextStage - New stage value
     * @param {string} direction - Direction of transition (forward/backward)
     */
    performStageTransition(chantierId, nextStage, direction) {
      const url = `/handle-stage`;
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          chantierId: chantierId,
          direction: direction,
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update UI elements
          this.updateStageDisplay(chantierId, nextStage);
          this.updateButtonAttributes(chantierId, nextStage);
          
          // Show success message
          const directionText = direction === 'forward' ? 'passé' : 'retourné';
          flashAlert('success', `Le chantier a été ${directionText} à l'étape ${nextStage}`);

          // Mise à jour de l'indicateur dans la liste
          const dropEvent = document.querySelector(`.dropEvent[data-id-chantier="${chantierId}"]`);
          if (dropEvent) {
            // Mise à jour du data-stage sur l'élément li
            dropEvent.setAttribute('data-stage', nextStage);
            
            // Mise à jour de l'indicateur
            const stageIndicator = dropEvent.querySelector('.stage-indicator');
            if (stageIndicator) {
              stageIndicator.setAttribute('data-stage', nextStage);
              stageIndicator.className = `stage-indicator absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full border border-dark ${this.stageColors[nextStage]}`;
            }
          }

          // Mise à jour des indicateurs dans le calendrier
          if (window.WorksiteCalendar) {
            window.WorksiteCalendar.updateStageIndicators(chantierId, this.stageColors[nextStage]);
          }

          // Mise à jour du label du stage
          const stageLabel = document.querySelector(`.stage-label[data-id="${chantierId}"]`);
          if (stageLabel) {
            stageLabel.textContent = this.stageLabels[nextStage];
          }
        } else {
          flashAlert('error', data.message || 'Une erreur est survenue lors de la transition');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        flashAlert('error', 'Une erreur est survenue lors de la transition');
      });
    }
  
    /**
     * Get CSRF token from meta tag
     * @returns {string} CSRF token
     */
    getCsrfToken() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
  }
  
  // Initialize the stage manager when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    new StageManager();
  });