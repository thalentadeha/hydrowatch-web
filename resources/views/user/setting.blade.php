@extends('user.layout')

@section('content')
    <main class="setting">
        <section>
            <div class="profile">
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
                <div class="text-area">
                    <span class="name">{{ $userData['fullname'] }}</span>
                    <span class="email">{{ $email }}</span>
                </div>
                <div class="table-data">
                    <table id="setting-list">
                        <tbody>
                            <tr class="Nickname">
                                <td>Nickname</td>
                                <td>
                                    <span>{{ $userData['nickname'] }}</span>
                                    <img class="arrow" src="{{ asset('img/next.png') }}" alt="">
                                </td>
                            </tr>
                            <tr class="TargetDrink">
                                <td>Target Drink</td>
                                <td>
                                    <span>{{ $targetDrink }}mL</span>
                                    <img class="arrow" src="{{ asset('img/next.png') }}" alt="">
                                </td>
                            </tr>
                            <tr class="MaxDrink">
                                <td>Max Drink</td>
                                <td>
                                    <span>{{ $maxDrink }}mL</span>
                                    <img class="arrow" src="{{ asset('img/next.png') }}" alt="">
                                </td>
                            </tr>
                            <tr class="ChangePassword">
                                <td>Change Password</td>
                                <td><img class="arrow" src="{{ asset('img/next.png') }}" alt=""></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <form action="{{ route('logout_POST') }}", method="POST">
                    @csrf
                    <button type="submit" class="signout red">Sign Out</button>
                </form>
            </div>
            <div class="schedule">
                <div class="text-area">
                    <span>Working Time</span>
                    <div class="notification">
                        <span>Notification</span>
                        <label class="switch">
                            <input type="checkbox" id="notificationToggle" idToken="{{ session('idToken') }}"
                                {{ $isNotificationEnabled ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="table-data">
                    <table id="schedule-list">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notificationTimeIn as $day => $startTime)
                                <tr>
                                    <td>{{ $day }}</td>
                                    <td>{{ $startTime }}</td>
                                    <td>{{ $notificationTimeOut[$day] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="SaveWorkingTime blue">Save Working Time</button>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script>
        const SettingList = document.querySelector(".table-data #setting-list")
        if (SettingList !== null) {
            SettingList.querySelectorAll("tr").forEach(i => {
                i.addEventListener("click", () => {
                    showDialogBox(i.querySelector("td").innerText)
                })
            })
        }

        function passwordShowHidden() {
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
        }

        function showDialogBox(target) {
            const dialogBox = document.querySelector("dialog")
            const innerDialog = getDialogBoxContent(target)
            dialogBox.innerHTML = innerDialog
            if (innerDialog !== ``) {
                dialogBox.show()
                dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
                    dialogBox.close()
                })

                const form = dialogBox.querySelector('form');
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    submitForm(new FormData(form), target);
                });

                if(target === "Change Password") {
                    passwordShowHidden();
                }
            }
        }

        function getDialogBoxContent(target) {
            switch (target) {
                case "Nickname":
                    return `
                        <div class="content">
                            <div class="text-area">
                                <span>Change Nickname</span>
                                <img class="close" src="{{ asset('img/close.png') }}" alt="">
                            </div>
                            <form action="{{ route('changeNickname_POST') }}" method="POST">
                                @csrf
                                <input type="text" placeholder="Nickname (Max 20 Character)" maxlength="20" name="nickname">
                                <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                <button type="submit" class="changeNickname blue">Save Nickname</button>
                            </form>
                        </div>
                    `
                case "Target Drink":
                    return `
                        <div class="content">
                            <div class="text-area">
                                <span>Change Target Drink</span>
                                <img class="close" src="{{ asset('img/close.png') }}" alt="">
                            </div>
                            <form action="{{ route('setTargetDrink_POST') }}" method="POST">
                                @csrf
                                <input type="number" placeholder="Target Drink (1000 - 6000mL)" name="targetDrink">
                                <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                <button type="submit" class="changeTargetDrink blue">Save Target Drink</button>
                            </form>
                        </div>
                    `
                case "Max Drink":
                    return `
                        <div class="content">
                            <div class="text-area">
                                <span>Change Max Drink</span>
                                <img class="close" src="{{ asset('img/close.png') }}" alt="">
                            </div>
                            <form action="{{ route('setMaxDrink_POST') }}" method="POST">
                                @csrf
                                <input type="number" placeholder="Max Drink (0 or 100 - 6000mL)" name="maxDrink">
                                <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                <button type="submit" class="changeMaxDrink blue">Save Max Drink</button>
                            </form>
                        </div>
                    `
                case "Change Password":
                    return `
                        <div class="content">
                            <div class="text-area">
                                <span>Change Password</span>
                                <img class="close" src="{{ asset('img/close.png') }}" alt="">
                            </div>
                            <form action="{{ route('resetPassword_POST') }}" method="POST">
                                @csrf

                                <div class="password-field">
                                    <input class="password oldPassword" type="Password" placeholder="Old Password" name="oldPassword">
                                    <div class="eye">
                                        <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                        <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                                    </div>
                                </div>
                                <div class="password-field">
                                    <input class="password newPassword" type="Password" placeholder="New Password" name="newPassword">
                                    <div class="eye">
                                        <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                        <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                                   </div>
                               </div>
                               <div class="password-field">
                                    <input class="password rePassword" type="Password" placeholder="Re-enter new Password" name="rePassword">
                                    <div class="eye">
                                        <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                        <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                                    </div>
                                </div>
                                <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                <button type="submit" class="changePassword blue">Change Password</button>
                           </form>
                        </div>
                    `
            }
        }

        let notifTimeIn = @json($notificationTimeOut);
        let notifTimeOut = @json($notificationTimeOut);
        let scheduleList = document.querySelectorAll("#schedule-list tbody tr");

        if (scheduleList !== null) {
            scheduleList.forEach(i => {
                const j = i.querySelectorAll("td")
                if (j.length > 0) {
                    i.addEventListener("click", () => {
                        const dialogBox = document.querySelector("dialog")
                        const innerDialog = `
                    <div class="content">
                        <div class="text-area">
                            <span>Change Working Time - ${j[0].innerText}</span>
                            <img class="close" src="{{ asset('img/close.png') }}" alt="">
                        </div>
                        <form action="">
                            <div class="time-in-out">
                                <span>Time In</span>
                                <input type="time" min="04:00" max="23:00" step="3600">
                            </div>
                            <div class="time-in-out">
                                <span>Time Out</span>
                                <input type="time" min="04:00" max="23:00" step="3600">
                            </div>
                            <div class="buttons">
                                <button type="button" class="deleteTime red">Delete Working Time</button>
                                <button type="button" class="changeTime blue">Save Working Time</button>
                            </div>
                        </form>
                    </div>
                `
                        dialogBox.innerHTML = innerDialog
                        if (innerDialog !== ``) {
                            dialogBox.show()
                            dialogBox.querySelector(".content .text-area .close").addEventListener("click",
                                () => {
                                    dialogBox.close()
                                })

                            const timeForm = document.querySelectorAll(".time-in-out input[type=time]")
                            if (timeForm !== null) {
                                timeForm.forEach(i => {
                                    i.addEventListener("change", () => {
                                        i.value = i.value.split(":")[0] + ":00"
                                    })
                                })
                            }

                            const deleteTime = document.querySelector("button.deleteTime")
                            const changeTime = document.querySelector("button.changeTime")

                            deleteTime.addEventListener('click', () => {
                                j[1].innerHTML = "OFF"
                                j[2].innerHTML = "OFF"
                                dialogBox.close()
                            })
                            changeTime.addEventListener('click', () => {
                                if(String(timeForm[0].value) === "") {
                                    showErrors("'Time in' cannot be empty")
                                }
                                else if(String(timeForm[1].value) === "") {
                                    showErrors("'Time out' cannot be empty")
                                }
                                else if(parseInt(timeForm[1].value) <= parseInt(timeForm[0].value)) {
                                    showErrors("'Time in' must be earlier than the 'Time out'")
                                }
                                else {
                                    j[1].innerHTML = timeForm[0].value
                                    j[2].innerHTML = timeForm[1].value
                                    dialogBox.close()
                                }
                            })
                        }
                    })
                }
            })
        }

        function checkScheduleChange() {
            let changeList = {};
            changeList["list"] = []
            let list = document.querySelectorAll("#schedule-list tbody tr");
            list.forEach(function (i, index) {
                const j = i.querySelectorAll("td")
                let day = j[0].innerHTML
                if(j[1].innerHTML !== notifTimeIn[day] || j[2].innerHTML !== notifTimeOut[day]) {
                    changeList["list"].push(index)
                    changeList[index] = {}
                    changeList[index]["timeIn"] = j[1].innerHTML;
                    changeList[index]["timeOut"] = j[2].innerHTML;
                }
            })
            return changeList;
        }

        function submitForm(formData, target) {
            let ACTION_URL;

            switch (target) {
                case "Nickname":
                    ACTION_URL = '{{ route('changeNickname_POST') }}';
                    break;
                case "Max Drink":
                    ACTION_URL = '{{ route('setMaxDrink_POST') }}';
                    break;
                case "Target Drink":
                    ACTION_URL = '{{ route('setTargetDrink_POST') }}';
                    break;
                case "Change Password":
                    ACTION_URL = '{{ route('resetPassword_POST') }}';
                    break;
                case "Save Working Time":
                    ACTION_URL = '{{ route('saveSchedule_POST') }}';
                    break;
            }

            let button = document.querySelector('dialog form button[type="submit"]');
            if(button != null && button != undefined) {
                button.disabled = true;
            }

            fetch(ACTION_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    console.log(response)
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if(target !== "Save Working Time") {
                            document.querySelector('dialog').close();
                        }
                        alert(data.success);

                        if (formData.has('nickname')) {
                            const nicknameElement = document.querySelector('.Nickname span');
                            if (nicknameElement) {
                                nicknameElement.textContent = formData.get('nickname');
                            }

                            // const dialogNicknameInput = document.querySelector('input[name="nickname"]');
                            // if (dialogNicknameInput) {
                            //     dialogNicknameInput.value = formData.get('nickname');
                            // }

                            const profileNicknameElement = document.querySelector('.profile .text-area .name');
                            if (profileNicknameElement) {
                                profileNicknameElement.textContent = formData.get('nickname');
                            }

                            const layoutNicknameElement = document.querySelector('#profile .names .nickname');
                            if (layoutNicknameElement) {
                                layoutNicknameElement.textContent = formData.get('nickname');
                            }
                        }

                        if (formData.has('maxDrink')) {
                            const maxDrinkElement = document.querySelector('.MaxDrink span');
                            if (maxDrinkElement) {
                                maxDrinkElement.textContent = `${formData.get('maxDrink')}mL`;
                            }
                        }

                        if (formData.has('targetDrink')) {
                            const maxDrinkElement = document.querySelector('.TargetDrink span');
                            if (maxDrinkElement) {
                                maxDrinkElement.textContent = `${formData.get('targetDrink')}mL`;
                            }
                        }
                    }
                    console.log(data)
                })
                .catch(error => {
                    if (error.errors) {
                        if("Save Working Time" === target) {
                            alert(error.errors);
                        }
                        else {
                            showErrors(error.errors);
                        }
                        
                        if(button != null && button != undefined) {
                            button.disabled = true;
                        }
                        console.log(response)
                    }
                });
        }

        function showErrors(error) {
            const existingWarning = document.querySelector('.warning-form');
            if (existingWarning) {
                existingWarning.remove();
            }
            if (error) {
                const form = document.querySelector('form');
                const errorSpan = document.createElement('span');
                errorSpan.className = 'warning-form';
                errorSpan.textContent = error;
                form.appendChild(errorSpan);
            }
        }

        let buttonSave = document.querySelector("button.SaveWorkingTime");
        buttonSave.addEventListener('click', () => {
            let schedules = checkScheduleChange();
            if(schedules["list"].length > 0) {
                buttonSave.disabled = true;

                schedules["list"].forEach(i => {
                    const form = document.createElement('form');
                    form.action = '{{ route('saveSchedule_POST') }}';
                    form.method = "POST";

                    const day = document.createElement('input');
                    day.type = 'text';
                    day.name = 'day';
                    day.defaultValue = i;

                    const timeIn = document.createElement('input');
                    timeIn.type = 'text';
                    timeIn.name = 'in';
                    timeIn.defaultValue = schedules[i]["timeIn"];

                    const timeOut = document.createElement('input');
                    timeOut.type = 'text';
                    timeOut.name = 'out';
                    timeOut.defaultValue = schedules[i]["timeOut"];

                    const btn = document.createElement('button');
                    btn.type = 'submit';
                    btn.name = 'submit';

                    form.appendChild(day);
                    form.appendChild(timeIn);
                    form.appendChild(timeOut);
                    form.appendChild(btn);

                    submitForm(new FormData(form), "Save Working Time")
                })
            }
        })
        

        //NOTIFICATION
        document.getElementById('notificationToggle').addEventListener('change', function() {
            const isEnabled = this.checked;
            const sessionToken = this.getAttribute('idToken');
            console.log('isEnabled:', isEnabled);
            // console.log('sessionToken:', sessionToken);

            updateNotificationStatus(isEnabled, sessionToken);
        });

        function updateNotificationStatus(isEnabled, token) {
            fetch('{{ route('updateNotificationStatus_POST') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        isNotificationEnabled: isEnabled,
                        idToken: token,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success);
                        console.log("success");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
