@extends('admin.layout')

@section('content')
    <main class="admin">
        <section>
            <div class="title">
                <h1>User List</h1>
                <div class="buttons">
                    <form action="">
                        <button type="button" class="AddUser blue">New User</button>
                        <button type="button" class="DeleteUser red">Delete User</button>
                    </form>
                </div>
            </div>
            <div class="table-data">
                <table id="user-list">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Nickname</th>
                            <th>Drink Water (mL)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $uid => $user)
                            @if ($user['role'] === 'user')
                                <tr>
                                    <td>{{ $user['fullname'] }}</td>
                                    <td>{{ $email[$uid]['email'] }}</td>
                                    <td>{{ $user['nickname'] }}</td>
                                    <td>{{ $drinkHistories[$uid]}}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script>
        const AddUser = document.querySelector(".AddUser")
        if (AddUser !== null) {
            AddUser.addEventListener("click", () => {
                showDialogBox("AddUser")
            })
        }

        const DeleteUser = document.querySelector(".DeleteUser")
        if (DeleteUser !== null) {
            DeleteUser.addEventListener("click", () => {
                showDialogBox("DeleteUser")
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
            }
        }

        function getDialogBoxContent(target) {
            switch (target) {
                case "AddUser":
                    return `
                            <div class="content">
                                <div class="text-area">
                                    <span>New User</span>
                                    <img class="close" src="{{ asset('img/close.png') }}" alt="">
                                </div>
                                <form action="{{ route('register_POST') }}" method="POST">
                                    @csrf

                                    <input type="text" placeholder="Name" name="fullname">
                                    <input type="text" placeholder="Nickname (Max 20 Character)" maxlength="20" name="nickname">
                                    <input type="email" placeholder="Email" name="email">
                                    <div class="password-field">
                                        <input class="password" type="password" name="password" placeholder="Password">
                                        <div class="eye">
                                            <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                           <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                                        </div>
                                    </div>
                                    <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                    <button type="submit" class="addUser blue">Save New User</button>
                                </form>
                            </div>
                            `
                case "DeleteUser":
                    return `
                            <div class="content">
                                <div class="text-area">
                                    <span>Delete User</span>
                                    <img class="close" src="{{ asset('img/close.png') }}" alt="">
                                </div>
                                <form action="{{ route('deleteUser_POST') }}" method="POST">
                                    @csrf

                                    <input type="email" placeholder="Email" name="email">
                                    <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                                    <button type="submit" class="deleteUser red">Delete User</button>
                                </form>
                            </div>
                            `
            }
        }

        function submitForm(formData, target) {
            let ACTION_URL;

            switch (target) {
                case "AddUser":
                    ACTION_URL = '{{ route('register_POST') }}';
                    break;
                case "DeleteUser":
                    ACTION_URL = '{{ route('deleteUser_POST') }}';
                    break;
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

                        updateData(formData, target);
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                    }
                });
        }

        function updateData(formData, target) {
            const email = formData.get('email');

            if (target === "AddUser") {
                const fullname = formData.get('fullname');
                const nickname = formData.get('nickname');
                const drinkWater = '0';

                const userList = document.querySelector('.table-data #user-list tbody');
                // console.log('userList:', userList);
                if (userList) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = '<td>' + fullname + '</td>' +
                        '<td>' + email + '</td>' +
                        '<td>' + nickname + '</td>' +
                        '<td>' + drinkWater + '</td>';

                    // Get existing rows and convert to array
                    const rowsArray = Array.from(userList.querySelectorAll('tr'));

                    // Insert the new row in the correct position
                    let inserted = false;
                    for (let i = 0; i < rowsArray.length; i++) {
                        const currentRow = rowsArray[i];
                        const currentFullname = currentRow.cells[0].textContent.trim().toUpperCase();

                        if (fullname.trim().toUpperCase() < currentFullname) {
                            userList.insertBefore(newRow, currentRow);
                            inserted = true;
                            break;
                        }
                    }

                    // If the new row is not inserted (meaning it is the last row), append it
                    if (!inserted) {
                        userList.appendChild(newRow);
                    }
                }
            } else {
                const userList = document.querySelector('.table-data #user-list tbody');
                if (userList) {
                    const rowsArray = Array.from(userList.querySelectorAll('tr'));

                    // Find and delete the row with the matching email
                    for (let i = 0; i < rowsArray.length; i++) {
                        const currentRow = rowsArray[i];
                        const currentEmail = currentRow.cells[1].textContent.trim();

                        if (email === currentEmail) {
                            userList.removeChild(currentRow);
                            break;
                        }
                    }
                }
            }
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
