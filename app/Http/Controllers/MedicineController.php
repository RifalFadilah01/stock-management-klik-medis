<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $medicines = Medicine::query()
                ->filter($request->only(['search', 'category', 'status']))
                ->latest()
                ->paginate(10);

            return MedicineResource::collection($medicines)->response();
        }

        return view('medicines.index');
    }

    public function store(StoreMedicineRequest $request)
    {
        $medicine = Medicine::create($request->validated());

        return response()->json([
            'message' => 'Data saved successfully',
            'data' => new MedicineResource($medicine)
        ], 201);
    }

    public function show(Medicine $medicine)
    {
        return new MedicineResource($medicine);
    }

    public function edit(Medicine $medicine)
    {
        return new MedicineResource($medicine);
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        $medicine->update($request->validated());

        return response()->json([
            'message' => 'Data updated successfully',
            'data' => new MedicineResource($medicine)
        ]);
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return response()->json([
            'message' => 'Data deleted successfully'
        ]);
    }
}
