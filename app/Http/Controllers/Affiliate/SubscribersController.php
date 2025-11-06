<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\CreateSubscriberRequest;
use App\Http\Requests\Subscriber\UpdateSubscriberRequest;
use App\Http\Resources\SubscriberResource;
use App\Imports\SubscribersImport;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SubscribersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('perPage', 10);

        $query = QueryBuilder::for(Subscriber::class)
            ->whereBelongsTo(auth()->user())
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('id'),
                AllowedSort::field('first_name'),
                AllowedSort::field('last_name'),
                AllowedSort::field('email'),
                AllowedSort::field('created_at'),
            ])
            ->defaultSort('id')
            ->paginate($perPage)
            ->withQueryString();

        $subscribers = SubscriberResource::collection($query);

        return Inertia::render('affiliate/subscribers/Index', [
            'subscribers' => $subscribers,
            'filters' => $request->only(['filter', 'sort']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('affiliate/subscribers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSubscriberRequest $request): RedirectResponse
    {
        Subscriber::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return to_route('subscribers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriber $subscriber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriber $subscriber)
    {
        return Inertia::modal('affiliate/subscribers/Edit', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber): RedirectResponse
    {
        $subscriber->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        Gate::allowIf($subscriber->user()->is(auth()->user()));

        $subscriber->delete();

        return back();
    }

    public function importSubscriber(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new SubscribersImport(auth()->user()), $request->file('file'));

        return back();
    }
}
