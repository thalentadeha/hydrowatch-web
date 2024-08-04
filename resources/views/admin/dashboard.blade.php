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
                                    <td>0</td>
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
        if(DeleteUser !== null) {
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

                                    <input type="text" placeholder="Name" name="fullname" required>
                                    <input type="text" placeholder="Nickname (Max 20 Character)" maxlength="20" name="nickname" required>
                                    <input type="email" placeholder="Email" name="email" required>
                                    <div class="password-field">
                                        <input class="password" type="password" name="password" placeholder="Password" required>
                                        <div class="eye">
                                            <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                           <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="radio-group">
                                        <label>Role:</label>
                                        <div class="options">
                                            <label><input type="radio" name="role" value="user" required> User</label>
                                            <label><input type="radio" name="role" value="admin" required> Admin</label>
                                            <label><input type="radio" name="role" value="dispenser" required> Dispenser</label>
                                        </div>
                                    </div>
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

                                    <input type="email" placeholder="Email" name="email" required>
                                    <button type="submit" class="deleteUser red">Delete User</button>
                                </form>
                            </div>
                            `
            }
        }
    </script>
@endsection
