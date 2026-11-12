<?php

namespace App\Features\Services\Http\Controllers;

use App\Features\Services\Http\Requests\StoreServiceRequest;
use App\Features\Services\Http\Requests\UpdateServiceRequest;
use App\Features\Services\Http\Resources\ServiceResource;
use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return ServiceResource::collection(
            Service::query()->orderBy('nom')->paginate($validated['per_page'] ?? 15),
        );
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return (new ServiceResource($service))->response()->setStatusCode(201);
    }

    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $service->update($request->validated());

        return new ServiceResource($service);
    }
}
