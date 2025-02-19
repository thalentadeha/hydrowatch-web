@extends('user.layout')

<style>
    main.home section .g3 .text-container img {
        cursor: pointer;
        height: var(--s7);
    }

    main.home section .g3 .text-container {
        width: 100%;
        flex-grow: 1;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        font-size: var(--s6);
    }
</style>

@section('content')
    <main class="home">
        <section>
            <div class="grid-item g1">
                <div class="g11">
                    <span>Drank Water</span>
                    <div class="text-container">
                        <span>{{ $drankWater }}</span>
                        <span>/ {{ $maxDrink < $targetDrink && $maxDrink > 0 ? $maxDrink : $targetDrink }}mL</span>
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
                    <img class="prev" src="{{ asset('img/prev.png') }}" alt="" data-action="prev">
                    <span class="month-year">{{ $monthName . ' ' . $year }}</span>
                    <img class="next" src="{{ asset('img/next_b.png') }}" alt="" data-action="next">
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
                                        <td>{{ isset($containerData['volume']) ? ((string) $containerData['volume']) . 'mL' : 'Not set' }}
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
            const month_year = document.querySelector(".g3 .text-container span.month-year").innerText;
            const tempDate = new Date(month_year);
            const date = new Date(tempDate.getFullYear(), tempDate.getMonth() + 1, 0).getDate();
            let drink = [];
            let maxDrink = [];

            let label = [];
            for (let i = 1; i <= date; i++) {
                label.push(i)
                drink.push(userDrink[String(i)] !== undefined ? userDrink[String(i)] : 0);
                maxDrink.push(userMaxDrink[String(i)] !== undefined ? userMaxDrink[String(i)] : 0);
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
                        formatter: (value) =>
                            `${value} ${document.querySelector(".g3 .text-container span.month-year").innerText}`,
                    },
                    y: {
                        formatter: (value) => `${value}mL`,
                    },
                },

                series: [{
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
                        filter: {
                            type: 'lighten',
                            value: 0.03
                        },
                    },
                    hover: {
                        filter: {
                            type: 'lighten',
                            value: 0.01
                        },
                    },
                    active: {
                        filter: {
                            type: 'none',
                            value: 0
                        },
                        allowMultipleDataPointsSelection: false,
                    },
                },
            }

            let chart = new ApexCharts(document.querySelector(".g3 .chart"), options)
            chart.render()
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prevNextButtons = document.querySelectorAll('.prev, .next');
            const monthYearElement = document.querySelector('.month-year');

            // console.log({{ $year }});
            // console.log({{ $month }});

            prevNextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action; // "prev" or "next"

                    fetch('/update-month', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                action: action,
                                idToken: '{{ session('idToken') }}',
                                year: year,
                                month: month
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // update the month and year
                                year = data.year;
                                month = data.month;
                                monthYearElement.textContent = data.monthName + ' ' + year;

                                // update the charts and other elements with new data
                                // clear existing charts
                                document.querySelector(".g3 .chart").innerHTML = '';

                                console.log(data.userDrankHistory);
                                console.log(data.userMaxDrinkHistory);

                                createChart(data.userDrankHistory, data.userMaxDrinkHistory);
                            } else {
                                alert('Failed to load data. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        });

        var year = {{ $year }};
        var month = {{ $month }}
        createDonut({{ $percentage }});
        let drink = @json($userDrankHistory);
        let maxDrink = @json($userMaxDrinkHistory);

        // console.log($userDrankHistory);
        console.log(drink);

        createChart(drink, maxDrink);
    </script>
@endsection
