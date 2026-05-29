<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Aksesmenu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AksesMenuController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        // Hierarchical menus
        $menus = Menu::where('idmenu', 0)->orWhere('idmenu', null)->with('subMenus')->get();
        
        // Get existing access Mapping: [idrole => [idmenu, idmenu, ...]]
        $access = [];
        $allAkses = Aksesmenu::all();
        foreach ($allAkses as $a) {
            $access[$a->idrole][] = $a->idmenu;
        }

        return view('admin.role_menu.index', compact('roles', 'menus', 'access'));
    }

    public function store(Request $request)
    {
        $idrole = $request->idrole;
        $idmenus = $request->idmenus ?? [];

        // Delete existing access for this role
        Aksesmenu::where('idrole', $idrole)->delete();

        // Inserts new ones
        foreach ($idmenus as $idmenu) {
            Aksesmenu::create([
                'idrole' => $idrole,
                'idmenu' => $idmenu
            ]);
        }

        return back()->with('success', 'Akses menu berhasil diperbarui.');
    }
}
