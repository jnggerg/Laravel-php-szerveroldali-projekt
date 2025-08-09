<?php
namespace App\Http\Controllers;

use App\Models\Enclosure;
use App\Models\Animal;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function showDashboardStats()
    {
        $current_user = auth()->user();
        $enclosure_count = Enclosure::count();
        $animal_count = Animal::count();

        $archived_animals = Animal::onlyTrashed()->orderByDesc("deleted_at")->get();   #archivált állatok listája

        $users_enclosures = $current_user->enclosures->sortBy(function($enclosure){  #időrendi sorrendbe sort
            return Carbon::parse($enclosure->feeding_at)->setDate(2025,1,1);  #setDatelni kell ugyanarra az összeset,
        });                                            #mivel az Datetime formatban szerepelnek az adatok,de itt csak az ora / perc kell

        $enclosures_paginate = $current_user->enclosures()->orderBy('name')->paginate(5);  #paginate hasnzálása az oldalakra bontáshoz
        if ($current_user->isAdmin()){
            $enclosures_paginate = Enclosure::orderBy('name')->paginate(5);         #ha admin, az összes enclosuret mutatja
        }
        return view('dashboard', compact('current_user','enclosure_count', 'animal_count','users_enclosures','enclosures_paginate','archived_animals'));
    }
}

