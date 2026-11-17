<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Models\SiteSetting;

class KontenProfilController extends Controller
{
    /** UC-15: konten company profile saat ini. */
    public function show()
    {
        return response()->json(['data' => SiteSetting::current()]);
    }

    public function update(ProfilRequest $request)
    {
        $setting = SiteSetting::current();
        $setting->update($request->dataTersimpan());

        return response()->json([
            'message' => 'Konten profil berhasil disimpan.',
            'data' => $setting->refresh(),
        ]);
    }
}
