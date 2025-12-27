document.addEventListener("DOMContentLoaded", function () {

    const cards = document.querySelectorAll(".kanban-card");
    const columns = document.querySelectorAll(".kanban-column");

    cards.forEach(card => {
        card.addEventListener("dragstart", () => {
            card.classList.add("dragging");
        });

        card.addEventListener("dragend", () => {
            card.classList.remove("dragging");
        });
    });

    columns.forEach(column => {
        column.addEventListener("dragover", e => {
            e.preventDefault();
            const dragging = document.querySelector(".dragging");
            column.appendChild(dragging);
        });
    });

});
