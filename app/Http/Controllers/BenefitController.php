<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Benefit;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class BenefitController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $benefits = Benefit::whereHas('employee', function($query) use ($user) {
            $query->where('company_id', $user->company_id);
        })->with('employee')->orderBy('created_at', 'desc')->paginate(15);
        return view('benefits.index', compact('benefits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $employees = Employee::where('company_id', $user->company_id)->orderBy('name')->get();
        return view('benefits.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
