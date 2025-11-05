<?php

namespace App\Http\Controllers\Affiliate;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\CampaignResource;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(auth()->user()->campaigns())
             ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->defaultSort('id')
            ->paginate()
            ->withQueryString();

        $campaigns = CampaignResource::collection($query);

        return Inertia::render('affiliate/campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['filter', 'sort']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $id)
    {
        return Inertia::modal('affiliate/campaigns/Create');
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
