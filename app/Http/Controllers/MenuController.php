<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Aksesmenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        $parentMenus = Menu::where('submenu', 1)->get();
        return view('admin.menu.create', compact('parentMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namamenu' => 'required|string|max:255',
            'alamat_url' => 'required|string',
            'namaroute' => 'nullable|string',
            'icon' => 'nullable|string',
            'submenu' => 'required|boolean',
            'idmenu' => 'nullable|integer',
        ]);

        Menu::create($request->all());

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $parentMenus = Menu::where('submenu', 1)->where('id', '!=', $id)->get();
        return view('admin.menu.edit', compact('menu', 'parentMenus'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $request->validate([
            'namamenu' => 'required|string|max:255',
            'alamat_url' => 'required|string',
            'submenu' => 'required|boolean',
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Cek apakah ada di tabel aksesmenu (rolemenu)
        $hasAccess = Aksesmenu::where('idmenu', $id)->exists();
        if ($hasAccess) {
            return back()->with('error', 'Menu tidak dapat dihapus karena sudah memiliki akses role.');
        }

        // Cek apakah memiliki submenu
        if ($menu->subMenus()->exists()) {
            return back()->with('error', 'Menu tidak dapat dihapus karena memiliki submenu.');
        }

        $menu->delete();
        return redirect()->route('admin.menu')->with('success', 'Menu berhasil dihapus.');
    }
}
