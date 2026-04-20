<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Equipment;
use App\Repository\EquipmentRepositoryInterface;
use App\Http\Requests\EquipmentRequest;
use App\Http\Resources\EquipmentResource;

class EquipmentController extends Controller
{

    private EquipmentRepositoryInterface $equipmentRepository;

    public function __construct(EquipmentRepositoryInterface $repository)
    {
        $this->equipmentRepository = $repository;
    }

    public function store(EquipmentRequest $request)
    {
        try {
            $user = Auth::user();
            if (($user->role)->name != 'admin')
            {
                abort(FORBIDDEN, "Forbidden");
            }
                
            $request->validated();
            $equipment = $this->equipmentRepository->create($request->toArray());
            return (new EquipmentResource($equipment))->response()->setStatusCode(CREATED);

        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Server error');
        }
    }

    public function update(EquipmentRequest $request, int $id)
    {
        try {
            $user = Auth::user();
            if (($user->role)->name != 'admin')
            {
                abort(FORBIDDEN, "Forbidden");
            }
                
            $request->validated();
            $equipment = $this->equipmentRepository->update($id, $request->toArray());
            return (new EquipmentResource($equipment))->response()->setStatusCode(OK);
            
        } catch (QueryException $e) {
            abort(NOT_FOUND, 'Invalid Id');
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Server error');
        }
    }

    public function destroy(int $id)
    {
        try {
            $user = Auth::user();
            if (($user->role)->name != 'admin')
            {
                abort(FORBIDDEN, "Forbidden");
            }

            $this->equipmentRepository->delete($id);
            return response()->noContent();
        
        } catch (QueryException $e) {
            abort(NOT_FOUND, 'Invalid Id');
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Server error');
        }
    }
}
