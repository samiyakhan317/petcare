@extends('layouts.app')

@section('title', 'Grooming Services')

@section('content')

<style>

    .services-page {
        min-height: calc(100vh - 70px);

        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );

        padding: 50px 40px;
    }

    .services-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .services-header {
        margin-bottom: 30px;
    }

    .services-header h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .services-header p {
        color: #666;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .service-card {
        background: white;
        padding: 25px;
        border-radius: 15px;

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);

        transition:
            transform 0.2s,
            box-shadow 0.2s;
    }

    .service-card:hover {
        transform: translateY(-4px);

        box-shadow:
            0 15px 35px rgba(0, 0, 0, 0.13);
    }

    .service-card h2 {
        color: #285b94;
        font-size: 22px;
        margin-bottom: 15px;
    }

    .service-info {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
    }

    .info-box {
        background: #f0f6fc;
        padding: 10px 14px;
        border-radius: 8px;
        flex: 1;
    }

    .info-label {
        display: block;
        color: #777;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .info-value {
        color: #285b94;
        font-weight: bold;
        font-size: 16px;
    }

    .description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .book-button {
        display: inline-block;

        padding: 11px 20px;

        background:
            linear-gradient(
                135deg,
                #3478c9,
                #54a4df
            );

        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    }

    .book-button:hover {
        background: #285b94;
    }

    .empty-message {
        background: white;
        padding: 40px 20px;
        border-radius: 15px;
        text-align: center;
        color: #777;

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);
    }

    .empty-message h2 {
        color: #285b94;
        margin-bottom: 10px;
    }

    @media (max-width: 700px) {

        .services-page {
            padding: 35px 20px;
        }

        .services-grid {
            grid-template-columns: 1fr;
        }

        .service-info {
            flex-direction: column;
        }

    }

</style>

<div class="services-page">

```
<div class="services-container">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="services-header">

        <h1>
            Grooming Services
        </h1>

        <p>
            Choose from our available pet grooming services.
        </p>

    </div>


    {{-- =========================
         SERVICES
    ========================== --}}

    @if($services->count() > 0)

        <div class="services-grid">

            @foreach($services as $service)

                <div class="service-card">

                    <h2>
                        {{ $service->Service_Name }}
                    </h2>


                    {{-- =========================
                         DURATION + PRICE
                    ========================== --}}

                    <div class="service-info">

                        <div class="info-box">

                            <span class="info-label">
                                Duration
                            </span>

                            <span class="info-value">

                                {{ $service->Duration }}

                                minutes

                            </span>

                        </div>


                        <div class="info-box">

                            <span class="info-label">
                                Price
                            </span>

                            <span class="info-value">

                                ৳{{ number_format(
                                    $service->Price,
                                    2
                                ) }}

                            </span>

                        </div>

                    </div>


                    {{-- =========================
                         DESCRIPTION
                    ========================== --}}

                    <div class="description">

                        {{ $service->Description ?: 'No description available.' }}

                    </div>


                    {{-- =========================
                         BOOK THIS SERVICE
                    ========================== --}}

                    <a
                        href="{{ route(
                            'appointments.create',
                            ['service_id' => $service->Service_ID]
                        ) }}"
                        class="book-button"
                    >

                        Book This Service

                    </a>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-message">

            <h2>
                No Services Available
            </h2>

            <p>
                There are currently no active grooming services.
            </p>

        </div>

    @endif

</div>
```

</div>

@endsection
