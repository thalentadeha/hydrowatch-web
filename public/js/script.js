const passField = document.querySelectorAll(".password-field")
passField.forEach(x => {
    const eye = x.querySelector(".eye")
    const pass = x.querySelector("input.password")
    eye.addEventListener("click", () => {
        eye.classList.toggle("active")
        if (eye.classList.contains("active")) {
            pass.type = "text"
        }
        else {
            pass.type = "password"
        }
    })
})

const menu = document.querySelector("menu")
const openMenu = document.querySelector("header nav img.logo")
const closeMenu = document.querySelector("menu .close")


openMenu.addEventListener("click", () => {
    menu.classList.toggle("show-menu")
})

closeMenu.addEventListener("click", () => {
    menu.classList.toggle("show-menu")
})

// function getDialogBoxContent(target) {
//     switch(target) {
//         case "AddContainer":
//             return `
//                 <div class="content">
//                     <div class="text-area">
//                         <span>New Container</span>
//                         <img class="close" src="./asset/img/close.png" alt="">
//                     </div>
//                     <form action="">
//                         <div class="nfc">
//                             <span>NFC ID</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                         </div>
//                         <textarea name="" class="desc" placeholder="Container Description"></textarea>
//                         <button type="button" class="addContainer blue">Save Container</button>
//                     </form>
//                 </div>
//             `
//             break
//         case "DeleteContainer":
//             return `
//                 <div class="content">
//                     <div class="text-area">
//                         <span>Delete Container</span>
//                         <img class="close" src="./asset/img/close.png" alt="">
//                     </div>
//                     <form action="">
//                         <div class="nfc">
//                             <span>NFC ID</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                             <span>:</span>
//                             <input type="text" placeholder="AA" maxlength="2">
//                         </div>
//                         <button type="button" class="deleteContainer red">Delete Container</button>
//                     </form>
//                 </div>
//             `
//             break
//         // case "AddUser":
//         //     return `
//         //         <div class="content">
//         //             <div class="text-area">
//         //                 <span>New User</span>
//         //                 <img class="close" src="./asset/img/close.png" alt="">
//         //             </div>
//         //             <form action="">
//         //                 <input type="text" placeholder="Name">
//         //                 <input type="text" placeholder="Nickname (Max 20 Character)" maxlength="20">
//         //                 <input type="email" placeholder="Email">
//         //                 <div class="password-field">
//         //                     <input class="password" type="Password" placeholder="Password">
//         //                     <div class="eye">
//         //                         <img class="show" src="./asset/img/view.png" alt="">
//         //                         <img class="hide" src="./asset/img/hide.png" alt="">
//         //                     </div>
//         //                 </div>
//         //                 <button type="button" class="addUser blue">Save New User</button>
//         //             </form>
//         //         </div>
//         //     `
//         //     break
//         // case "DeleteUser":
//         //     return `
//         //         <div class="content">
//         //             <div class="text-area">
//         //                 <span>Delete User</span>
//         //                 <img class="close" src="./asset/img/close.png" alt="">
//         //             </div>
//         //             <form action="">
//         //                 <input type="email" placeholder="Email">
//         //                 <button type="button" class="deleteUser red">Delete User</button>
//         //             </form>
//         //         </div>
//         //     `
//         //     break
//         case "Nickname":
//             return `
//                 <div class="content">
//                     <div class="text-area">
//                         <span>Change Nickname</span>
//                         <img class="close" src="./asset/img/close.png" alt="">
//                     </div>
//                     <form action="">
//                         <input type="text" placeholder="Nickname (Max 20 Character)" maxlength="20">
//                         <button type="button" class="changeNickname blue">Save Nickname</button>
//                     </form>
//                 </div>
//             `
//             break
//         case "Max Drink":
//             return `
//                 <div class="content">
//                     <div class="text-area">
//                         <span>Change Max Drink</span>
//                         <img class="close" src="./asset/img/close.png" alt="">
//                     </div>
//                     <form action="">
//                         <input type="number" placeholder="Max Drink (100 - 6000mL)" min="100" max="6000">
//                         <button type="button" class="changeMaxDrink blue">Save Max Drink</button>
//                     </form>
//                 </div>
//             `
//             break
// case "Change Password":
//     return `
//         <div class="content">
//             <div class="text-area">
//                 <span>Change Password</span>
//                 <img class="close" src="./asset/img/close.png" alt="">
//             </div>
//             <form action="">
//                 <div class="password-field">
//                     <input class="password oldPassword" type="Password" placeholder="Old Password">
//                     <div class="eye">
//                         <img class="show" src="./asset/img/view.png" alt="">
//                         <img class="hide" src="./asset/img/hide.png" alt="">
//                     </div>
//                 </div>
//                 <div class="password-field">
//                     <input class="password newPassword" type="Password" placeholder="New Password">
//                     <div class="eye">
//                         <img class="show" src="./asset/img/view.png" alt="">
//                         <img class="hide" src="./asset/img/hide.png" alt="">
//                     </div>
//                 </div>
//                 <div class="password-field">
//                     <input class="password rePassword" type="Password" placeholder="Re-enter new Password">
//                     <div class="eye">
//                         <img class="show" src="./asset/img/view.png" alt="">
//                         <img class="hide" src="./asset/img/hide.png" alt="">
//                     </div>
//                 </div>
//                 <button type="button" class="changePassword blue">Change Password</button>
//             </form>
//         </div>
//     `
//     break
//     }
//     return ``
// }

// function showDialogBox(target) {
//     const dialogBox = document.querySelector("dialog")
//     const innerDialog = getDialogBoxContent(target)
//     dialogBox.innerHTML = innerDialog
//     if(innerDialog !== ``) {
//         dialogBox.show()
//         dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
//             dialogBox.close()
//         })
//     }
// }

// //  Container
// const AddContainer = document.querySelector(".AddContainer")
// if(AddContainer !== null) {
//     AddContainer.addEventListener("click", () => {
//         showDialogBox("AddContainer")
//     })
// }

// const DeleteContainer = document.querySelector(".DeleteContainer")
// if(DeleteContainer !== null) {
//     DeleteContainer.addEventListener("click", () => {
//         showDialogBox("DeleteContainer")
//     })
// }

// const AddUser = document.querySelector(".AddUser")
// if(AddUser !== null) {
//     AddUser.addEventListener("click", () => {
//         showDialogBox("AddUser")
//     })
// }

// const DeleteUser = document.querySelector(".DeleteUser")
// if(DeleteUser !== null) {
//     DeleteUser.addEventListener("click", () => {
//         showDialogBox("DeleteUser")
//     })
// }

// // Setting
// const SettingList = document.querySelector(".table-data #setting-list")
// if(SettingList !== null) {
//     SettingList.querySelectorAll("tr").forEach(i => {
//         i.addEventListener("click", () => {
//             showDialogBox(i.querySelector("td").innerText)
//         })
//     })
// }

// const scheduleList = document.querySelectorAll("#schedule-list tr")
// if(scheduleList !== null) {
//     scheduleList.forEach(i => {
//         const j = i.querySelectorAll("td")
//         if(j.length > 0) {
//             i.addEventListener("click", () => {
//                 const dialogBox = document.querySelector("dialog")
//                 const innerDialog = `
//                     <div class="content">
//                         <div class="text-area">
//                             <span>Change Working Time - ${j[0].innerText}</span>
//                             <img class="close" src="./asset/img/close.png" alt="">
//                         </div>
//                         <form action="">
//                             <div class="time-in-out">
//                                 <span>Time In</span>
//                                 <input type="time" min="04:00" max="23:00" step="3600">
//                             </div>
//                             <div class="time-in-out">
//                                 <span>Time Out</span>
//                                 <input type="time" min="04:00" max="23:00" step="3600">
//                             </div>
//                             <div class="buttons">
//                                 <button type="button" class="deleteTime red">Delete Working Time</button>
//                                 <button type="button" class="changeTime blue">Save Working Time</button>
//                             </div>
//                         </form>
//                     </div>
//                 `
//                 dialogBox.innerHTML = innerDialog
//                 if(innerDialog !== ``) {
//                     dialogBox.show()
//                     dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
//                         dialogBox.close()
//                     })

//                     const timeForm = document.querySelectorAll(".time-in-out input[type=time]")
//                     if(timeForm !== null) {
//                         timeForm.forEach(i => {
//                             i.addEventListener("change", () => {
//                                 i.value = i.value.split(":")[0] + ":00"
//                             })
//                         })
//                     }
//                 }
//             })
//         }
//     })
// }


// Home

const red = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-invalid")
    .trim();

const blue = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-accent")
    .trim();

const grey = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-foreground")
    .trim();

const white = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-secondary")
    .trim();

const fontFamily = getComputedStyle(document.documentElement)
    .getPropertyValue("--font-regular")
    .trim();

const fontSize = getComputedStyle(document.documentElement)
    .getPropertyValue("--s9")
    .trim();


function getLabel() {
    const month_year = document.querySelector(".g3 .text-container span.month-year").innerText
    const tempDate = new Date(month_year)
    const daytime = new Date(tempDate.getFullYear(), tempDate.getMonth() + 1, 0)
    let label = []
    for (let i = 1; i <= daytime.getDate(); i++) {
        label.push(i)
    }
    return label
}

function createChart(drink, maxDrink) {
    const label = getLabel()

    for (let i = maxDrink.length; i < label.length; i++) {
        maxDrink.push(maxDrink[i - 1])
        drink.push(0)
    }

    let options = {
        dataLabels: {
            enabled: false,
        },

        tooltip: {
            enabled: true,
            style: {
                fontFamily: fontFamily,
            },
            x: {
                formatter: (value) => `${value} ${document.querySelector(".g3 .text-container span.month-year").innerText}`,
            },
            y: {
                formatter: (value) => `${value}mL`,
            },
        },

        series: [
            {
                name: "Drank",
                type: 'bar',
                data: drink,
            },
            {
                name: "Target Drink",
                type: 'line',
                data: maxDrink,
            },
        ],

        chart: {
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
            width: "100%",
            height: "100%",
            offsetY: 10,
            type: "line",
            stacked: false,
        },

        stroke: {
            curve: 'smooth',
            width: [0, 3],
            colors: [grey, red],
            lineCap: "round",
        },

        grid: {
            borderColor: "rgba(0, 0, 0, 0)",
            padding: {
                top: -10,
                right: 0,
                bottom: 0,
                left: 12,
            },
        },

        colors: [blue, red],

        markers: {
            colors: [grey, red],
            strokeColors: [grey, red],
        },

        yaxis: {
            show: false,
        },

        xaxis: {
            labels: {
                show: true,
                floating: true,
                style: {
                    colors: grey,
                    fontFamily: fontFamily,
                    fontSize: fontSize,
                },
            },

            axisBorder: {
                show: false,
            },

            axisTicks: {
                show: false,
            },

            crosshairs: {
                show: false,
            },

            categories: label,
        },

        legend: {
            show: false,
        },

        states: {
            normal: {
                filter: { type: 'lighten', value: 0.03 },
            },
            hover: {
                filter: { type: 'lighten', value: 0.01 },
            },
            active: {
                filter: { type: 'none', value: 0 },
                allowMultipleDataPointsSelection: false,
            },
        },
    }

    let chart = new ApexCharts(document.querySelector(".g3 .chart"), options)
    chart.render()
}

function createDonut(x) {
    let data = []
    data.push(x)
    var options = {
        series: data,
        chart: {
            height: 'auto',
            width: '125%',
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                },
                track: {
                    background: white,
                },
                dataLabels: {
                    show: false
                }
            },
        },
        colors: [blue],
        labels: [''],
    };


    let chart = new ApexCharts(document.querySelector(".g5 .chart"), options);
    chart.render();
}


// Example for Home
createDonut(percentage)
createChart(allDrankData, allMaxDrinkData)
