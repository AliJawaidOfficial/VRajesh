const toggleNavBtn = document.querySelector("#toggleSidebar");
const cancelNavBtn = document.querySelector(".cancel-icon");
const sidebarContainer = document.querySelector(".sidebar");

if (cancelNavBtn && toggleNavBtn) {
    toggleNavBtn.addEventListener("click", () => {
        sidebarContainer.classList.add("active");
    })

    cancelNavBtn.addEventListener("click", () => {
        sidebarContainer.classList.remove("active");
    })
}

$("form").each(function () {
    $(this).validate();
});

function standardDateTimeFormat(date) {
    const formattedDate = new Date(date);
    if (isNaN(formattedDate.getTime())) return 'Invalid Date';
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    const formattedString = formattedDate.toLocaleString('en-US', options);
    return formattedString;
}

function convertUTCToLocalTime(dateTimeString) {
    var date = new Date(dateTimeString);
    return date.toString()
}