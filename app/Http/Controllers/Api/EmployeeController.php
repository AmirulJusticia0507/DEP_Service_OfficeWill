<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'DEP Service API', version: '1.0.0', description: 'API documentation for DEP Service OfficeWill')]
#[OA\Server(url: '/api')]
class EmployeeController extends Controller
{
    #[OA\Get(path: '/api/employees', summary: 'List all employees', tags: ['Employees'])]
    #[OA\Response(response: 200, description: 'List of employees')]
    public function index(): JsonResponse
    {
        return response()->json(Employee::all());
    }

    #[OA\Post(path: '/api/employees', summary: 'Create a new employee', tags: ['Employees'])]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'position_id', type: 'integer'),
                new OA\Property(property: 'affiliation_id', type: 'integer'),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Employee created')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position_id' => 'nullable|exists:positions,id',
            'affiliation_id' => 'nullable|exists:affiliations,id',
        ]);

        return response()->json(Employee::create($data), 201);
    }

    #[OA\Get(path: '/api/employees/{id}', summary: 'Get employee by ID', tags: ['Employees'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Employee data')]
    #[OA\Response(response: 404, description: 'Not found')]
    public function show(int $id): JsonResponse
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($employee);
    }
}
