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
                            {{-- <tr class="MaxDrink">
                                <td>Max Drink</td>
                                <td>
                                    <span>{{ $maxDrink }}mL</span>
                                    <img class="arrow" src="{{ asset('img/next.png') }}" alt="">
                                </td>
                            </tr> --}}
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
            scheduleList.forEach((i, index) => {
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
                        <form action="{{ route('saveSchedule_POST') }}" method="POST">
                        @csrf
                        <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                        <input type="hidden" name="day" value=${index}>
                        <input type="hidden" class="timeIn" name="in" value="">
                        <input type="hidden" class="timeOut" name="out" value="">
                        <div class="time-in-out">
                                <span>Time In</span>
                                <input type="time" name="timeIn" min="06:00" max="22:00" step="3600">
                            </div>
                            <div class="time-in-out">
                                <span>Time Out</span>
                                <input type="time" name="timeOut" min="06:00" max="22:00" step="3600">
                            </div>
                            <div class="buttons">
                                <button type="reset" class="deleteTime red">Delete Working Time</button>
                                <button type="submit" class="changeTime blue">Save Working Time</button>
                            </div>
                        </form>
                    </div>
                `
                        dialogBox.innerHTML = innerDialog
                        if (innerDialog !== ``) {
                            dialogBox.show()
                            dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
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

                            const form = dialogBox.querySelector('form');
                            form.addEventListener('submit', function(event) {
                                event.preventDefault();
                                const inputTimeIn = document.querySelector("input.timeIn");
                                inputTimeIn.value = String(timeForm[0].value);
                                const inputTimeOut = document.querySelector("input.timeOut");
                                inputTimeOut.value = String(timeForm[1].value);
                                submitForm(new FormData(form), "Save Working Time");
                            });
                            form.addEventListener('reset', function(event) {
                                event.preventDefault();
                                const inputTimeIn = document.querySelector("input.timeIn");
                                inputTimeIn.value = "OFF";
                                const inputTimeOut = document.querySelector("input.timeOut");
                                inputTimeOut.value = "OFF";
                                submitForm(new FormData(form), "Save Working Time");
                            });
                        }
                    })
                }
            })
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
            let buttonRst = document.querySelector('dialog form button[type="reset"]');
            if(button != null && button != undefined) {
                button.disabled = true;
            }
            if(buttonRst != null && buttonRst != undefined) {
                buttonRst.disabled = true;
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
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.querySelector('dialog').close();
                        alert(data.success);

                        if (formData.has('nickname')) {
                            const nicknameElement = document.querySelector('.Nickname span');
                            if (nicknameElement) {
                                nicknameElement.textContent = formData.get('nickname');
                            }

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

                        if (target === "Save Working Time") {
                            const list = document.querySelectorAll("#schedule-list tbody tr");
                            let schedule = list[formData.get('day')].querySelectorAll("td");
                            schedule[1].innerHTML = formData.get('in');
                            schedule[2].innerHTML = formData.get('out');
                        }
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);

                        if(button != null && button != undefined) {
                            button.disabled = false;
                        }
                        if(buttonRst != null && buttonRst != undefined) {
                            buttonRst.disabled = false;
                        }
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

        const notificationToggle = document.getElementById('notificationToggle');
        notificationToggle.addEventListener('change', function() {
            const isEnabled = this.checked;
            notificationToggle.disabled = true;
            const sessionToken = this.getAttribute('idToken');
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
