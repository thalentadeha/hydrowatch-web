@extends('user.layout')

{{-- @section('profile')
    <div id="profile">
        <div class="names">
            <span class="nickname">{{ $userDoc['nickname'] }}</span>
            <span class="full-name">{{ $userDoc['fullname'] }}</span>
        </div>
        <img class="avatar" src="asset{{ asset('img/no-avatar.png') }}" alt="">
    </div>
@endsection --}}
{{-- @section('head-script')
    <script type="module" src=" {{ asset('js/firebase.js') }}"></script>
@endsection --}}

@section('content')
    <main class="home">
        <section>
            <div class="grid-item g1">
                <div class="g11">
                    <span>Drank Water</span>
                    <div class="text-container">
                        <span>{{ $drankWater }}</span>
                        <span>/ {{ $maxDrink }}mL</span>
                    </div>
                </div>
                <div class="g12">
                    <span>Last Drink</span>
                    <div class="text-container">
                        <span>{{ $lastDrinkTime }}</span>
                    </div>
                </div>
            </div>
            <div class="grid-item g3">
                <div class="text-container">
                    <img class="prev" src="{{ asset('img/prev.png') }}" alt="">
                    <span class="month-year">{{ $month . ' ' . $year }}</span>
                    <img class="next" src="{{ asset('img/next_b.png') }}" alt="">
                </div>
                <div class="chart">

                </div>
            </div>
            <div class="grid-item g2">
                <div class="text-container">
                    <span>Container List</span>
                    <a href="{{ route('user-container', ['idToken' => session('idToken')]) }}"><img class="goto"
                            src="{{ asset('img/arrow-2.png') }}" alt=""></a>
                </div>
                <div class="table-data">
                    <table id="container-list">
                        <tbody>
                            @if (!empty($containerList))
                                @foreach ($containerList as $nfcid => $containerData)
                                    <tr>
                                        <td>{{ $nfcid }}</td>
                                        <td>{{ $containerData['volume'] !== -1 ? $containerData['volume'] : 'set volume at dispenser!' }}
                                        </td>
                                        <td>{{ $containerData['weight'] !== -1 ? $containerData['weight'] . 'g' : 'set weight at dispenser!' }}
                                        </td>
                                        <td>{{ $containerData['description'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="font-family: var(--font-regular);">No container found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid-item g5">
                <div class="text-container">
                    <span class="percentage">{{ number_format($percentage) }}%</span>
                    <span>Hydration</span><span>Target</span>
                </div>
                <div class="chart"></div>
            </div>
            <div class="grid-item g6">
                <div class="text-container">
                    <span>Dispenser Location</span>
                </div>
                <div class="table-data">
                    <table id="location-list">
                        <tbody>
                            <tr>
                                <td>Kantor WillFitness</td>
                                <td>Lantai 1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('script')
    {{-- <script>

    </script> --}}

    <script src="https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js"></script>
    {{-- <script src="https://www.gstatic.com/firebasejs/10.13.1/firebase-messaging.js"></script> --}}
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
        // import {
        //     initializeApp
        // } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js";

        // import {
        //     getToken,
        //     getMessaging,
        //     onMessage
        // } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-messaging.js";

        var firebaseConfig = {
            apiKey: "AIzaSyAPkDZg_G9LZG3BgS906t0frdDOlKAcfk8",
            authDomain: "hydrowatch-testing.firebaseapp.com",
            databaseURL: "https://hydrowatch-testing-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "hydrowatch-testing",
            storageBucket: "hydrowatch-testing.appspot.com",
            messagingSenderId: "193728248140",
            appId: "1:193728248140:web:dcf3f61a35ea7c59deb7e9",
            measurementId: "G-CME1973XZF"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then(function(registration) {
                    console.log('Registration successful, scope is:', registration.scope);
                }).catch(function(err) {
                    console.log('Service worker registration failed, error:', err);
                });
        }

        messaging.getToken(messaging, {
                vapidKey: 'BHcCqa0Uyr4JjZ9kCNSoZiU1fRnxbE2OE9XghEtDDEKsPx5XjOZ3gl7-087DfsDgpz1-3sUW8HzFDTpex5g08hI'
            })
            .then((currentToken) => {
                if (currentToken) {
                    console.log('FCM token:', currentToken);
                    // saveToken(currentToken)
                } else {
                    console.log('No registration token available.');
                }
            })
            .catch((err) => {
                console.error('An error occurred while retrieving token. ', err);
            });

        function saveToken(currentToken) {
            fetch('saveDeviceToken_POST', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        deviceToken: currentToken
                    })
                })
                .then(response => response.json())
                .then(data => console.log('Token sent to server:', data))
                .catch(error => console.error('Error sending token to server:', error));
        }

        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return getToken()
                })
                .then(function(token) {
                    console.log(token);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '{{ route('saveDeviceToken_POST') }}',
                        type: 'POST',
                        data: {
                            deviceToken: token
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            alert('Token saved successfully.');
                        },
                        error: function(err) {
                            console.log('User Chat Token Error' + err);
                        },
                    });

                }).catch(function(err) {
                    console.log('User Chat Token Error' + err);
                });
        }

        onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });

        var percentage = @json($percentage);
        var allDrankData = @json($allDrankData);
        var allMaxDrinkData = @json($allMaxDrinkData);
    </script>
@endsection
