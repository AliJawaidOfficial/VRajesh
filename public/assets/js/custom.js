const toggleNavBtn = document.querySelector("#toggleSidebar");
const sidebarContainer = document.querySelector(".sidebar");
const cancelNavBtn = document.querySelector(".cancel-icon");



toggleNavBtn.addEventListener("click", () => {
    sidebarContainer.classList.add("active");
})

cancelNavBtn.addEventListener("click", () => {
    sidebarContainer.classList.remove("active");
})