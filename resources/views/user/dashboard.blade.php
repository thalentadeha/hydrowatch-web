@extends('user.layout')

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
                        <span>/ {{ ($maxDrink < $targetDrink && $maxDrink > 0 ? $maxDrink : $targetDrink) }}mL</span>
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
                    <span class="month-year">{{ $month . ' ' . $year }}</span>
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
                                        <td>{{ isset($containerData['volume']) ? (((String) $containerData['volume']) . "mL"): 'Not set' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" style="font-family: var(--font-regular);">No container found.</td>
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
                                <td>Lantai 2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script type="module" src=" {{ asset('js/firebase.js') }}"></script>
    <script>
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
        
        function createChart(userDrink, userMaxDrink) {
            const today = new Date(new Date().getFullYear(), (new Date().getMonth() + 1), 0).getDate();
            let drink = [];
            let maxDrink = [];
            
            let label = [];
            for(let i = 1; i <=today; i++) {
                label.push(i)
                drink.push(userDrink[String(i)])
                maxDrink.push(userMaxDrink[String(i)])
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
    </script>
    <script>
        createDonut({{$percentage}});
        let drink = @json($userDrankHistory);
        let maxDrink = @json($userMaxDrinkHistory);
        createChart(drink, maxDrink);
    </script>
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
    </script>
@endsection
