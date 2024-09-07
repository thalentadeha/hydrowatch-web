const menu = document.querySelector("menu")
const openMenu = document.querySelector("header nav img.logo")
const closeMenu = document.querySelector("menu .close")

if(openMenu != null && openMenu != undefined) {
    openMenu.addEventListener("click", () => {
        menu.classList.toggle("show-menu")
    })
}

if(closeMenu != null && closeMenu != undefined) {
    closeMenu.addEventListener("click", () => {
        menu.classList.toggle("show-menu")
    })
}
