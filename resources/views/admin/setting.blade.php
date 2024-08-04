@extends('admin.layout')

@section('content')
    <main class="setting">
        <section>
            <div class="profile">
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
                <div class="text-area">
                    <span class="name">{{ $userDoc['fullname'] }}</span>
                    <span class="name">(ADMIN)</span>
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

        function showDialogBox() {
            const dialogBox = document.querySelector("dialog")
            const innerDialog = getDialogBoxContent()
            dialogBox.innerHTML = innerDialog
            if (innerDialog !== ``) {
                dialogBox.show()
                dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
                    dialogBox.close()
                })
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
                            <input class="password oldPassword" type="Password" placeholder="Old Password" name="oldPassword" required>
                            <div class="eye">
                                <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                            </div>
                        </div>
                        <div class="password-field">
                            <input class="password newPassword" type="Password" placeholder="New Password" name="newPassword" required>
                            <div class="eye">
                                <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                            </div>
                        </div>
                        <div class="password-field">
                            <input class="password rePassword" type="Password" placeholder="Re-enter new Password" name="rePassword" required>
                            <div class="eye">
                                <img class="show" src="{{ asset('img/view.png') }}" alt="">
                                <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                            </div>
                        </div>
                        <button type="submit" class="changePassword blue">Change Password</button>
                    </form>
                </div>
            `
        }
    </script>
@endsection
