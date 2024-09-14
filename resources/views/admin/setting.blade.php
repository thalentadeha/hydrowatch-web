@extends('admin.layout')

@section('content')
    <main class="setting">
        <section>
            <div class="profile">
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
                <div class="text-area">
                    <span class="name">{{ $userData['fullname'] }}</span>
                    <span class="name">ADMIN</span>
                    <span class="email">{{ $email }}</span>
                </div>
                <div class="table-data">
                    <table id="setting-list">
                        <tbody>
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
        </section>
    </main>
@endsection

@section('script')
    <script>
        const SettingList = document.querySelector(".table-data #setting-list")
        if (SettingList !== null) {
            SettingList.addEventListener("click", () => {
                showDialogBox()
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

        function showDialogBox() {
            const dialogBox = document.querySelector("dialog")
            const innerDialog = getDialogBoxContent()
            dialogBox.innerHTML = innerDialog
            if (innerDialog !== ``) {
                dialogBox.show()
                dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
                    dialogBox.close()
                })

                const form = dialogBox.querySelector('form');
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    submitForm(new FormData(form));
                });

                passwordShowHidden();
            }
        }

        function getDialogBoxContent() {
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

        function submitForm(formData) {
            let button = document.querySelector('dialog form button[type="submit"]');
            button.disabled = true;

            fetch('{{ route('resetPassword_POST') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => {
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
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        button.disabled = false;
                        showErrors(error.errors);
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
    </script>
@endsection
