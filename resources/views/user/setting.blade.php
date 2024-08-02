@extends('user.layout')

@section('content')
    <main class="setting">
        <section>
            <div class="profile">
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
                <div class="text-area">
                    <span class="name">{{ $userDoc['fullname'] }}</span>
                    <span class="email">{{ $email }}</span>
                </div>
                <div class="table-data">
                    <table id="setting-list">
                        <tbody>
                            <tr class="Nickname">
                                <td>Nickname</td>
                                <td>
                                    <span>{{ $userDoc['nicknames'] }}</span>
                                    <img class="arrow" src="{{ asset('img/next.png') }}" alt="">
                                </td>
                            </tr>
                            <tr class="MaxDrink">
                                <td>Target Drink</td>
                                <td>
                                    <span>3000mL</span>
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
                <button type="button" class="signout red">Sign Out</button>
            </div>
            <div class="schedule">
                <div class="text-area">
                    <span>Working Time</span>
                    <div class="notification">
                        <span>Notification</span>
                        <label class="switch">
                            <input type="checkbox">
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
                            <tr>
                                <td>Monday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>08:00</td>
                                <td>17:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="SaveWorkingTime blue">Save Working Time</button>
            </div>
        </section>
    </main>
@endsection
