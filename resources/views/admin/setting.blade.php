@extends('admin.layout')

@section('content')
    <main class="setting">
        <section>
            <div class="profile">
                <img class="avatar" src="./asset/img/no-avatar.png" alt="">
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
                                <td><img class="arrow" src="./asset/img/next.png" alt=""></td>
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
