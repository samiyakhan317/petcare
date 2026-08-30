@extends('layouts.app')

@section('title', 'Add Grooming Service')

@section('content')

<style>

    .service-page {
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

    .service-container {
        max-width: 650px;
        margin: 0 auto;
    }

    .service-card {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);
    }

    .service-card h1 {
        color: #285b94;
        margin-bottom: 10px;
    }

    .service-card .subtitle {
        color: #777;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: #444;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 15px;
        box-sizing: border-box;
    }

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    .error {
        color: #c62828;
        font-size: 14px;
        margin-top: 5px;
    }

    .buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .button {
        display: inline-block;
        padding: 12px 22px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        font-size: 15px;
    }

    .save-button {
        background:
            linear-gradient(
                135deg,
                #3478c9,
                #54a4df
            );
        color: white;
    }

    .save-button:hover {
        background: #285b94;
    }

    .back-button {
        background: #eee;
        color: #555;
    }

    .back-button:hover {
        background: #ddd;
    }

</style>


<div class="service-page">

    <div class="service-container">

        <div class="service-card">

            <h1>
                Add Grooming Service
            </h1>

            <p class="subtitle">
                Create a new pet grooming service.
            </p>


            @if($errors->any())

                <div style="
                    background: #fdecec;
                    color: #c62828;
                    border: 1px solid #f5b5b5;
                    padding: 12px 15px;
                    border-radius: 7px;
                    margin-bottom: 20px;
                ">

                    @foreach($errors->all() as $error)

                        <div>
                            {{ $error }}
                        </div>

                    @endforeach

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('admin.services.store') }}"
            >

                @csrf


                <!-- SERVICE NAME -->

                <div class="form-group">

                    <label for="Service_Name">
                        Service Name
                    </label>

                    <input
                        type="text"
                        id="Service_Name"
                        name="Service_Name"
                        value="{{ old('Service_Name') }}"
                        placeholder="Example: Full Grooming"
                        required
                    >

                    @error('Service_Name')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <!-- DURATION -->

                <div class="form-group">

                    <label for="Duration">
                        Duration (minutes)
                    </label>

                    <input
                        type="number"
                        id="Duration"
                        name="Duration"
                        value="{{ old('Duration') }}"
                        min="1"
                        placeholder="Example: 60"
                        required
                    >

                    @error('Duration')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <!-- PRICE -->

                <div class="form-group">

                    <label for="Price">
                        Price (৳)
                    </label>

                    <input
                        type="number"
                        id="Price"
                        name="Price"
                        value="{{ old('Price') }}"
                        min="0"
                        step="0.01"
                        placeholder="Example: 800"
                        required
                    >

                    @error('Price')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <!-- DESCRIPTION -->

                <div class="form-group">

                    <label for="Description">
                        Description
                    </label>

                    <textarea
                        id="Description"
                        name="Description"
                        placeholder="Describe the grooming service..."
                    >{{ old('Description') }}</textarea>

                    @error('Description')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <!-- BUTTONS -->

                <div class="buttons">

                    <button
                        type="submit"
                        class="button save-button"
                    >
                        Add Service
                    </button>

                    <a
                        href="{{ route('admin.services.index') }}"
                        class="button back-button"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection