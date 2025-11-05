<?php

namespace App\Http\Controllers\Affiliate;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\TemplateResource;
use App\Models\Template;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userTemplates = Template::whereNotIn('id', function ($query) {
            $query->select('template_id')
                ->from('campaigns')
                ->where('user_id', auth()->id());
        });


        $query = QueryBuilder::for($userTemplates)
             ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->defaultSort('id')
            ->paginate()
            ->withQueryString();

        $templates = TemplateResource::collection($query);

        return Inertia::render('affiliate/templates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['filter', 'sort']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function createCampaign(int $id)
    {
        $template = Template::find($id);

        return Inertia::modal('affiliate/templates/CreateCampaign', [
            'template' => $template,
        ]);
    }

    public function storeCampaign(Request $request, int $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $campaign = auth()->user()->campaigns()->create([
            'template_id' => $id,
            'subject' => $validated['subject'],
            'content' => $validated['content'],
        ]);

        return back();
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
