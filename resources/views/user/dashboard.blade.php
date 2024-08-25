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
                    <span class="month-year">{{ $month . " " . $year }}</span>
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
                                        <td>{{ $containerData['volume'] !== -1 ? $containerData['volume'] : "set volume at dispenser!"}}</td>
                                        <td>{{ $containerData['weight'] !== -1 ? $containerData['weight']."g" : "set weight at dispenser!"}}</td>
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
    <script>
        var percentage = @json($percentage);
        var allDrankData = @json($allDrankData);
        var allMaxDrinkData = @json($allMaxDrinkData);
    </script>
@endsection
