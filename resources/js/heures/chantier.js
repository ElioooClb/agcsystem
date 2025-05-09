import { flashAlert } from "../utils/flashAlert";

/**
 * Chantier Manager - Handles construction site planning and management
 */
class ChantierManager {
  constructor() {
    this.modal = null;
    this.modalInfo = null;
    this.chantierId = null;
    this.assignedUsers = null;
    this.selectUsers = null;
    
    // Attendre que le DOM soit chargé avant d'initialiser
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.initializeEventListeners());
    } else {
      this.initializeEventListeners();
    }
  }

  /**
   * Initialize all event listeners
   */
  initializeEventListeners() {
    this.initializeDropEvents();
    this.initializeTitleButtons();
    this.initializeHourButtons();
    this.initializeAssignButtons();
    this.initializeObservationButtons();
    this.initializeArchiveButtons();
    this.initializeAmountButtons();
    this.initializeParameterCheckboxes();
    this.initializeColorSelectors();
  }

  /**
   * Initialize dropEvent click listeners
   */
  initializeDropEvents() {
    const dropEvents = document.querySelectorAll(".dropEvent");
    
    dropEvents.forEach(dropEvent => {
      dropEvent.addEventListener("click", () => {
        this.chantierId = dropEvent.getAttribute("data-id-chantier");
        this.modal = document.getElementById(`chantierModal_${this.chantierId}`);
        this.modalInfo = document.getElementById(`chantierModalInfo_${this.chantierId}`);
        
        // Vérifier que les éléments modaux existent
        if (!this.modal || !this.modalInfo) {
          console.error(`Modal elements not found for chantier ${this.chantierId}`);
          return;
        }
        
        // Display modal
        this.modal.style.display = "block";
        
        // Setup modal close handlers
        const span = this.modal.getElementsByClassName("close")[0];
        span.onclick = () => {
          this.modal.style.display = "none";
        };
        
        window.onclick = (event) => {
          if (event.target == this.modal) {
            this.modal.style.display = "none";
          }
        };
        
        this.selectUsers = this.modal.querySelector(".selected-users");
        this.assignedUsers = this.modal.querySelectorAll(".assignedUser");
        this.setupDeleteUserHandlers();
      });
    });
  }

  /**
   * Initialize title update buttons
   */
  initializeTitleButtons() {
    document.querySelectorAll(".btn-title").forEach(btnTitle => {
      btnTitle.addEventListener("click", () => {
        const title = this.modal.querySelector(".title").value;
        const titleModal = this.modal.querySelector(".title-modal");
        titleModal.innerHTML = `Informations sur le chantier ${title}`;
        
        // Update display
        document.querySelector(`.dropEvent[data-id-chantier='${this.chantierId}']`).innerHTML = title;
        
        // Update on server
        this.updateChantierTitle(title);
      });
    });
  }

  /**
   * Update chantier title via API
   * @param {string} title - New title 
   */
  updateChantierTitle(title) {
    const url = `/modifier-titre-chantier/${this.chantierId}?title=${title}`;
    
    fetch(url, {
      method: "GET",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      // Update all related UI elements
      this.modalInfo.querySelector(".title_info").innerHTML = `Informations sur le chantier ${title}`;
      document.querySelectorAll(`.idChantierEvent${this.chantierId}`).forEach(event => {
        event.innerHTML = title;
        event.style.color = "white";
      });
      
      flashAlert("success", "Le titre a été modifiée avec succès");
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize hour update buttons
   */
  initializeHourButtons() {
    document.querySelectorAll(".btn-hour").forEach(btnUpdateHour => {
      btnUpdateHour.addEventListener("click", () => {
        const hour = this.modal.querySelector(".hour").value;
        this.updateChantierHours(hour);
      });
    });
  }

  /**
   * Update chantier hours via API
   * @param {string} hour - New hours value
   */
  updateChantierHours(hour) {
    const url = `/modifier-devis-chantier/${this.chantierId}?hour=${hour}`;
    
    fetch(url, {
      method: "PUT",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      this.modalInfo.querySelector(".hourInfo").innerHTML = `Heure Prévue : ${hour}h`;
      flashAlert("success", "L'heure a été modifiée avec succès");
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize assign user buttons
   */
  initializeAssignButtons() {
    document.querySelectorAll(".btn-assign").forEach(btnAssign => {
      btnAssign.addEventListener("click", () => {
        const selectedOptions = this.modal.querySelector(".selected-users").selectedOptions;
        const users = Array.from(selectedOptions).map(option => option.value);
        
        // Assign each selected user
        users.forEach(userId => this.assignUserToChantier(userId));
      });
    });
  }

  /**
   * Assign a user to the current chantier
   * @param {string} userId - User ID to assign
   */
  assignUserToChantier(userId) {
    const url = `/ajouter-user-chantier/${this.chantierId}/${userId}`;
    
    fetch(url, {
      method: "PUT",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      const option = this.modal.querySelector(`option[value='${userId}']`);
      const userName = option.text;
      
      // Add user to assigned list in modal
      this.addUserToAssignedList(userId, userName);
      
      // Add user to info panel
      this.addUserToInfoPanel(userName);
      
      // Remove from selection options
      option.remove();
      
      // Re-setup delete handlers for the new elements
      this.assignedUsers = this.modal.querySelectorAll(".assignedUser");
      this.setupDeleteUserHandlers();
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Add user to the assigned users list in the modal
   * @param {string} userId - User ID
   * @param {string} userName - User name
   */
  addUserToAssignedList(userId, userName) {
    const listeTeck = this.modal.querySelector(".ulListeTeck");
    const newAssignedUser = document.createElement("li");
    newAssignedUser.classList.add("d-flex", "assignedUser");
    
    newAssignedUser.innerHTML = `
      <p>${userName}</p>
      <button class="deleteAssignedUser" data-id-user="${userId}" data-name-user="${userName}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
          <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
        </svg>
      </button>
    `;
    
    listeTeck.appendChild(newAssignedUser);
  }

  /**
   * Add user to the info panel
   * @param {string} userName - User name
   */
  addUserToInfoPanel(userName) {
    const listeTeckInfo = this.modalInfo.querySelector(".ulListeTeck");
    const newAssignedUserInfo = document.createElement("li");
    newAssignedUserInfo.classList.add("d-flex", "assignedUser");
    newAssignedUserInfo.innerHTML = userName;
    listeTeckInfo.appendChild(newAssignedUserInfo);
  }

  /**
   * Setup delete handlers for assigned users
   */
  setupDeleteUserHandlers() {
    this.modal.querySelectorAll(".deleteAssignedUser").forEach(deleteButton => {
      // Remove existing handlers to prevent duplicates
      const clone = deleteButton.cloneNode(true);
      deleteButton.parentNode.replaceChild(clone, deleteButton);
      
      clone.addEventListener("click", () => {
        const userId = clone.getAttribute("data-id-user");
        const userName = clone.getAttribute("data-name-user");
        clone.disabled = true;
        
        this.removeUserFromChantier(userId, userName, clone.closest(".assignedUser"));
      });
    });
  }

  /**
   * Remove a user from the current chantier
   * @param {string} userId - User ID to remove
   * @param {string} userName - User name to remove
   * @param {Element} assignedUserElement - DOM element to remove on success
   */
  removeUserFromChantier(userId, userName, assignedUserElement) {
    const url = `/supprimer-user-chantier/${this.chantierId}/${userId}`;
    
    fetch(url, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      // Remove user from assigned list
      assignedUserElement.remove();
      
      // Add back to selection options
      const option = document.createElement("option");
      option.value = userId;
      option.text = userName;
      option.classList.add("text-2xl");
      this.selectUsers.add(option);
      
      // Remove from info panel
      const listeTeckInfo = this.modalInfo.querySelector(".ulListeTeck");
      const assignedUserInfo = this.findLiByText(listeTeckInfo, userName);
      if (assignedUserInfo) {
        assignedUserInfo.remove();
      }
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize observation update buttons
   */
  initializeObservationButtons() {
    document.querySelectorAll(".btn-observation").forEach(btnObservation => {
      btnObservation.addEventListener("click", () => {
        const observation = this.modal.querySelector(".observations").value;
        this.updateChantierObservation(observation);
      });
    });
  }

  /**
   * Update chantier observation via API
   * @param {string} observation - New observation text
   */
  updateChantierObservation(observation) {
    const url = `/ajouter-observation-chantier/${this.chantierId}`;
    
    fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": this.getCsrfToken()
      },
      body: JSON.stringify({ observation })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      this.modalInfo.querySelector(".obs").innerHTML = 
        observation !== "" ? observation : "Aucune observation";
      
      flashAlert("success", "Observation modifiée !");
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize archive buttons
   */
  initializeArchiveButtons() {
    document.querySelectorAll(".btn-archive").forEach(btnArchive => {
      btnArchive.addEventListener("click", () => {
        if (confirm("Voulez-vous vraiment archiver ce chantier ?")) {
          this.archiveChantier();
        }
      });
    });
  }

  /**
   * Archive the current chantier
   */
  archiveChantier() {
    const url = `/archiver-chantier/${this.chantierId}`;
    
    fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      window.location.reload();
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize amount update buttons
   */
  initializeAmountButtons() {
    document.querySelectorAll(".btn-amount").forEach(btnAmount => {
      btnAmount.addEventListener("click", () => {
        const amountElements = this.modal.querySelector(".amounts").querySelectorAll(".amount");
        const amounts = Array.from(amountElements).map(element => element.value);
        
        this.updateChantierAmounts(amounts);
      });
    });
  }

  /**
   * Update chantier amounts via API
   * @param {Array} amounts - Array of amount values
   */
  updateChantierAmounts(amounts) {
    const url = `/modifier-montant/${this.chantierId}?amounts=${amounts}`;
    
    fetch(url, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      this.modalInfo.querySelector(".amountMaterial").innerHTML = `Montant : ${amounts[0]}€`;
      this.modalInfo.querySelector(".amountService").innerHTML = `Montant : ${amounts[1]}€`;
      flashAlert("success", "Montant modifié avec succès !");
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Initialize parameter checkboxes
   */
  initializeParameterCheckboxes() {
    document.querySelectorAll('#options input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener("change", () => {
        const checked = checkbox.checked;
        const idChantier = checkbox.closest('[id^="options"]').getAttribute("data-id");
        const idParameter = checkbox.getAttribute("data-id");
        
        this.updateChantierParameter(idChantier, idParameter, checked, checkbox);
      });
    });
  }

  /**
   * Update chantier parameter via API
   * @param {string} idChantier - Chantier ID
   * @param {string} idParameter - Parameter ID
   * @param {boolean} checked - Checkbox state
   * @param {Element} checkbox - DOM checkbox element
   */
  updateChantierParameter(idChantier, idParameter, checked, checkbox) {
    const url = `/modifier-parameter/${idChantier}/${idParameter}/${checked}`;
    
    fetch(url, {
      method: "GET",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      // Update label styling
      const labelEl = checkbox.parentNode;
      if (checked) {
        labelEl.classList.add('green');
      } else {
        labelEl.classList.remove('green');
      }
      
      flashAlert("success", "Option modifiée avec succès !");
      
      // Check if all boxes are checked
      this.checkAllParameters(idChantier);
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Check if all parameters are checked and update realization date if needed
   * @param {string} idChantier - Chantier ID
   */
  checkAllParameters(idChantier) {
    let allChecked = true;
    const currentModal = document.getElementById(`chantierModal_${idChantier}`);
    const currentCheckboxes = currentModal.querySelectorAll("input[type='checkbox']");
    
    currentCheckboxes.forEach(checkbox => {
      if (!checkbox.checked) {
        allChecked = false;
      }
    });
    
    if (allChecked) {
      fetch(`/modifier-date-realisation/${idChantier}`, {
        method: "PUT",
        headers: {
          "X-CSRF-TOKEN": this.getCsrfToken()
        }
      });
    }
  }

  /**
   * Initialize color selectors
   */
  initializeColorSelectors() {
    document.querySelectorAll(".colorSelect").forEach(colorSelect => {
      colorSelect.addEventListener("change", () => {
        const color = colorSelect.value;
        const idChantier = colorSelect.getAttribute("data-id");
        
        this.updateChantierColor(idChantier, color);
      });
    });
  }

  /**
   * Update chantier color via API
   * @param {string} idChantier - Chantier ID
   * @param {string} color - Color value
   */
  updateChantierColor(idChantier, color) {
    const url = `/modifier-couleur/${idChantier}/${color}`;
    
    fetch(url, {
      method: "PUT",
      headers: {
        "X-CSRF-TOKEN": this.getCsrfToken()
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      
      // Update color classes for all related elements
      const colorClasses = ["bg-blue-500", "bg-green-500", "bg-red-500", 
                          "bg-yellow-500", "bg-purple-500", "bg-gray-500"];
      
      const currentEvent = document.querySelector(`.dropEvent[data-id-chantier="${idChantier}"]`);
      const events = document.querySelectorAll(`.idChantierEvent${idChantier}`);
      
      // Update calendar events
      events.forEach(event => {
        colorClasses.forEach(cls => event.classList.remove(cls));
        event.classList.add(`bg-${color}-500`);
      });
      
      // Update drop event
      colorClasses.forEach(cls => currentEvent.classList.remove(cls));
      currentEvent.classList.add(`bg-${color}-500`);
    })
    .catch(error => {
      console.error(error);
      console.error(error.stack);
    });
  }

  /**
   * Find a list item by its text content
   * @param {Element} parentElement - Parent element to search within
   * @param {string} text - Text to search for
   * @returns {Element|null} - Found element or null
   */
  findLiByText(parentElement, text) {
    const lis = parentElement.querySelectorAll('li');
    for (let i = 0; i < lis.length; i++) {
      if (lis[i].textContent.includes(text)) {
        return lis[i];
      }
    }
    return null;
  }

  /**
   * Get CSRF token from meta tag
   * @returns {string} - CSRF token
   */
  getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute("content");
  }
}

// Initialize the chantier manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new ChantierManager();
});