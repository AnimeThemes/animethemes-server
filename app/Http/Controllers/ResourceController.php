<?php

namespace App\Http\Controllers;

use App\Enums\ResourceType;
use App\Models\Resource;
use App\Rules\ResourceTypeDomain;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = Resource::all();
        return view('resource.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('resource.create')->withResourceTypes(ResourceType::toSelectArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', new EnumValue(ResourceType::class, false)],
            'link' => ['required', 'unique:resource', 'max:192', 'url', new ResourceTypeDomain($request->input('type'))],
            'label' => ['nullable', 'max:192', 'alpha_dash'],
        ]);

        Resource::create($request->all());

        return redirect()->route('resource.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $resource)
    {
        return view('resource.show')->withResource($resource);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource)
    {
        return view('resource.edit')->withResource($resource)->withResourceTypes(ResourceType::toSelectArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'type' => ['required', new EnumValue(ResourceType::class, false)],
            'link' => ['required', Rule::unique('resource')->ignore($resource), 'max:192', 'url', new ResourceTypeDomain($request->input('type'))],
            'label' => ['nullable', 'max:192', 'alpha_dash'],
        ]);

        $resource->update($request->all());

        return redirect()->route('resource.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('resource.index');
    }
}
