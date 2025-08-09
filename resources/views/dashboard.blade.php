<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Köszöntelek az Állatkert Gondozói oldalon!</p>
                    <p align="right">
                        Válaszüzenetek: <br>
                        @if (session('success'))
                            {{ session('success') }}
                        @endif
                        @if (session('error'))
                            {{ session('error') }}
                        @endif
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li align="right">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </p>
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Kifutók száma:
                    {{ $enclosure_count }}
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Állatok száma:
                    {{ $animal_count }}
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Gondozói teendők:') }}
                    <ul>
                        @foreach ($users_enclosures as $enclosure)
                            @php
                                $feeding = \Carbon\Carbon::parse($enclosure->feeding_at)->setDate(2025, 1, 1);
                                $now = \Carbon\Carbon::now('Europe/Budapest')->setDate(2025, 1, 1);
                            @endphp
                            @if ($feeding->greaterThan($now))
                                <li>Kifutó neve: "{{ $enclosure->name }}" | Etetési idő:
                                    {{ \Carbon\Carbon::parse($enclosure->feeding_at)->format('H:i') }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Összes kifutód:
                    <br>
                    <table cellpadding="8">
                        <thead>
                            <tr>
                                <th>Kifutó neve</th>
                                <th>Limit</th>
                                <th>Elhelyezett állatok száma</th>
                                @if ($current_user->isAdmin())
                                    <th>Kifutó Szerkeztése</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enclosures_paginate as $enclosure)
                                <tr>
                                    <th>{{ $enclosure->name }}</th>
                                    <th>{{ $enclosure->limit }}</th>
                                    <th>{{ $enclosure->animals()->count() }}</th>
                                    @if ($current_user->isAdmin())
                                        <form action="{{ route('dashboard.edit_enclosure') }}" method="POST"
                                            style="width: 100px; height: 5;">
                                            @csrf
                                            <th><input type="text" name="name" placeholder="Kifutó neve"
                                                    value="{{ $enclosure->name }}" required
                                                    style="color: black; background: white; width: 200px; height: 5px;">
                                            </th>
                                            <th><input type="number" name="limit" placeholder="Limit"
                                                    value="{{ $enclosure->limit }}" required
                                                    style="color: black; background: white; width: 100px; height: 5px;">
                                            </th>
                                            <th><input type="time" name="feeding_at" placeholder="Etetés ideje"
                                                    value="{{ \Carbon\Carbon::parse($enclosure->feeding_at)->format('H:i') }}"
                                                    required
                                                    style="color: black; background: white; width: 100px; height: 5px;">
                                            </th>
                                            <th><input type="text" name="user_ids"
                                                    placeholder="Gondozók (id1,id2,..)"
                                                    value="{{ $enclosure->users()->pluck('users.id')->implode(',') }}"
                                                    required
                                                    style="color: black; background: white; width: 100px; height: 5px;">
                                            </th>
                                            <th><button type="submit">Submit</button></th>
                                        </form>
                                        <form action="{{ route('dashboard.del_enclosure') }}" method="POST"
                                            style="width: 100px; height: 5;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $enclosure->id }}">
                                            <th><button type="submit">Törlés</button></th>
                                        </form>
                                    @endif
                                    <form action="{{ route('dashboard.show_enclosure') }}" method="GET"
                                        style="width: 100px; height: 5;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $enclosure->id }}">
                                        <th><button type="submit">Megtekintés</button></th>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $enclosures_paginate->links() }}
                </div>
                @if ($current_user->isAdmin())
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Új kifutó hozzáadása
                        <form action="{{ route('dashboard.new_enclosure') }}" method="POST" style=>
                            @csrf
                            <input type="text" name="name" placeholder="Kifutó neve" value="{{ old('name') }}"
                                required style="color: black; background: white;">
                            <input type="number" name="limit" placeholder="Limit" value="{{ old('limit') }}"
                                required style="color: black; background: white;">
                            <input type="time" name="feeding_at" placeholder="Etetés ideje"
                                value="{{ old('feeding_at') }}" required style="color: black; background: white;">
                            <input type="text" name="user_ids" placeholder="Gondozók (id1,id2,..)"
                                value="{{ old('user_ids') }}" required style="color: black; background: white;">
                            <button type="submit">Submit</button>
                        </form>
                    </div>
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Archivált állatok száma:
                        {{ $animal_count }}
                    </div>
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        @if ($archived_animals->count() > 0)
                            Archivált állatok Listája:
                            <table cellpadding="8">
                                <thead>
                                    <tr>
                                        <th>Állat neve</th>
                                        <th>Állatfaj</th>
                                        <th>Archiválás időpontja</th>
                                        <th>Kifutó id</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archived_animals as $animal)
                                        <tr>
                                            <th>{{ $animal->name }}</th>
                                            <th>{{ $animal->species }}</th>
                                            <th>{{ \Carbon\Carbon::parse($animal->deleted_at)->format('Y-m-d H:i:s') }}
                                            </th>
                                            <th>
                                                <form
                                                    action="{{ route('dashboard.restore_animal', ['animal' => $animal->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="number" name="id"
                                                        value="{{ $animal->enclosure_id }}" required
                                                        style="color: black; background: white;">
                                                    <button type="submit">Visszaállítás</button>
                                                </form>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            Nincs archivált állat
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
