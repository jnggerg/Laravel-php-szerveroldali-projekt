<?php
namespace App\Http\Controllers;

use App\Models\Enclosure;
use App\Models\Animal;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    public function currentUserisAdmin(){
        $isadmin = auth()->user()->admin;
        if (!$isadmin) {
            return redirect()->back()->with('error', 'Nincs ehhez jogosultságod!');
        }
        return true;
    }

    public function newEnclosure(Request $req)
    {
        #validácio
        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $valid_data = $req->validate([
            'name' => 'required|string|min:1|max:255',
            'limit' => 'required|integer|min:1',
            'feeding_at' => 'required|date_format:H:i',
            'user_ids' => 'required|string|min:1|max:255',
        ]);

        $users = array_map('intval', explode(',', $req->user_ids));
        $bad_ids = [];

        foreach ($users as $id){
            if (!User::find(intval($id))) {
                $bad_ids[] = $id;
            }
        }
        if (count($bad_ids) > 0) {
            return redirect()->back()->withErrors(['user_ids' => 'Hibás id(k) megadva! '.implode(',', $bad_ids)])->withInput();
        }

        #dbhez hozzáadás
        $enclosure = Enclosure::create([
            'name' => $valid_data['name'],
            'limit' => $valid_data['limit'],
            'feeding_at' => $valid_data['feeding_at'],
        ]);

        #userek hozzácsatolása
        $enclosure->users()->attach($users);

        return redirect()->back()->with('success','Új kifutó sikeresen hozzáadva');
    }

    public function editEnclosure(Request $req){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        #validácio
        $valid_data = $req->validate([
        'name' => 'required|string|min:1|max:255',
        'limit' => 'required|integer|min:1',
        'feeding_at' => 'required|date_format:H:i',
        'user_ids' => 'required|string|min:1|max:255',
        ]);

        $enclosure = Enclosure::where('name', $req->name)->firstOrFail();
        $users = array_map('intval', explode(',', $req->user_ids));
        $bad_ids = [];

        foreach ($users as $id){
            if (!User::find(intval($id))) {
                $bad_ids[] = $id;
            }
        }
        if (count($bad_ids) > 0) {
            return redirect()->back()->withErrors(['user_ids' => 'Hibás id(k) megadva! '.implode(',', $bad_ids)])->withInput();
        }
        $enclosure->update([
            'name' => $valid_data['name'],
            'limit' => $valid_data['limit'],
            'feeding_at' => $valid_data['feeding_at'],
        ]);
        $enclosure->users()->sync($users);

        return redirect()->back()->with('success', 'Kifutó adatai sikeresen frissítve');
    }

    public function delEnclosure(Request $req){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $enclosure = Enclosure::findOrFail($req->id);
        $enclosure->users()->detach();
        $enclosure->animals()->ForceDelete();
        $enclosure->delete();
        return redirect()->back()->with('success', 'Kifutó sikeresen törlve');
    }

    public function showEnclosure(Request $req){
        $current_user = auth()->user();
        $enclosure = Enclosure::findOrFail($req->id);
        return view('enclosuredetails', compact('enclosure','current_user'));
    }

    public function newAnimal(Request $req, $enclosure_id){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $current_user = auth()->user();
        $valid_data = $req->validate([
            'name' => 'required|string|min:1|max:255',
            'species' => 'required|string|min:1|max:255',
            'born_at' => 'required|date',
            'is_predator' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' #max 2mb
        ]);

        $enclosure = Enclosure::findOrFail($enclosure_id);
        $enclosure->animals()->create([
            'name' => $valid_data['name'],
            'species' => $valid_data['species'],
            'born_at' => $valid_data['born_at'],
            'is_predator' => $valid_data['is_predator'],
            'kep' => $req->file('image')->store('animals', 'public'),
        ]);

        return redirect()->back()->with('success', 'Állat sikeresen létrehozva');
    }

    public function editAnimal(Request $req, $enclosure_id, $animal_id){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $valid_data = $req->validate([
            'name' => 'required|string|min:1|max:255',
            'species' => 'required|string|min:1|max:255',
            'born_at' => 'required|date|before_or_equal:today',
            'is_predator' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' #max 2mb
        ]);

        $enclosure = Enclosure::findOrFail($enclosure_id);
        $animal = $enclosure->animals()->findOrFail($animal_id);

        if ($req->hasFile('image')) {

            if ($animal->kep) {
                Storage::disk('public')->delete($animal->kep);
            }

            $valid_data['kep'] = $req->file('image')->store('animals', 'public');
        }

        $animal->update($valid_data);

        return redirect()->back()->with('success', 'Állat frissítve');
    }

    public function delAnimal($enclosure_id, $animal_id){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $enclosure = Enclosure::findOrFail($enclosure_id);
        $animal = $enclosure->animals()->findOrFail($animal_id);

        $animal->delete();
        return redirect()->back()->with('success', 'Állat archiválva');
    }


    public function restoreAnimal(Request $req, $animal){

        $admin_check = $this->currentUserisAdmin();
        if ($admin_check !== true) {
            return $admin_check;
        }
        $req->validate([
            'id' => 'required|integer'
        ]);
        $enclosure_exists = Enclosure::find($req->id);

        if (!$enclosure_exists) {
            return back()->withErrors(['enclosure_id' => 'A kiválasztott kifutó nem létezik.']);
        }

        $animal = Animal::withTrashed()->findOrFail($animal);
        $animal->restore();

        $animal->enclosure_id = $req->id;
        $animal->save();
        return redirect()->back()->with('success', 'Állat visszaállitva');
    }
}

