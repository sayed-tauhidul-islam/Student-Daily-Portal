<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Lists\SchoolList;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        return view('frontend.schools', [
            'schools' => SchoolList::search($query),
            'query' => $query,
        ]);
    }
}
