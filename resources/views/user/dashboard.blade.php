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

@section('content')
    <main class="home">
        <section>
            <div class="grid-item g1">
                <div class="g11">
                    <span>Drank Water</span>
                    <div class="text-container">
                        <span>{{ $drankWater }}</span>
                        <span>/ 2500mL</span>
                    </div>
                </div>
                <div class="g12">
                    <span>Last Drink</span>
                    <div class="text-container">
                        <span>18:00</span>
                    </div>
                </div>
            </div>
            <div class="grid-item g3">
                <div class="text-container">
                    <img class="prev" src="{{ asset('img/prev.png') }}" alt="">
                    <span class="month-year">January 2024</span>
                    <img class="next" src="{{ asset('img/next_b.png') }}" alt="">
                </div>
                <div class="chart">

                </div>
            </div>
            <div class="grid-item g2">
                <div class="text-container">
                    <span>Container List</span>
                    <a href="./container.html"><img class="goto" src="{{ asset('img/arrow-2.png') }}" alt=""></a>
                </div>
                <div class="table-data">
                    <table id="container-list">
                        <tbody>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
                            <tr>
                                <td>15:AD:30:E3</td>
                                <td>500</td>
                            </tr>
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
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            let percentage = {{ number_format($percentage) }};
            createDonut(100);
        });
    </script>
@endsection
