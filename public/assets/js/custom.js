const toggleNavBtn = document.querySelector("#toggleSidebar");
const cancelNavBtn = document.querySelector(".cancel-icon");
const sidebarContainer = document.querySelector(".sidebar");

if(cancelNavBtn && toggleNavBtn) {
    toggleNavBtn.addEventListener("click", () => {
        sidebarContainer.classList.add("active");
    })
    
    cancelNavBtn.addEventListener("click", () => {
        sidebarContainer.classList.remove("active");
    })
}
