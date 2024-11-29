document.addEventListener("DOMContentLoaded", () => {
    const actionsModalEl = document.getElementById('actionsModal');
    const actionsModalBg = document.getElementById('actionsModalBg');
    const notesModalEl = document.getElementById('notesModal');
    const notesModalBg = document.querySelector('.notesModalBg');
    const closeButtons = document.querySelectorAll('.closeButton');

    // Handle the echap key to close the modal
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            actionsModalEl.classList.contains("hidden") ? notesModalEl.classList.add("hidden") : null;
            notesModalEl.classList.contains("hidden") ? actionsModalEl.classList.add("hidden") : null;
        }
    });

    // Handle the click on the close button
    closeButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            actionsModalEl.classList.add("hidden");
            notesModalEl.classList.add("hidden");
        });
    });

    // Handle the click on the background
    actionsModalBg.addEventListener("click", (e) => {
        e.preventDefault();
        actionsModalEl.classList.add("hidden");
    });
    
    if (notesModalBg !== null){
        notesModalBg.addEventListener("click", (e) => {
            e.preventDefault();
            notesModalEl.classList.add("hidden");
        });
    }
});



