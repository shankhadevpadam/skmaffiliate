<?php

namespace App\Http\Controllers\Subscriber;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\CreateSubscriberRequest;
use App\Http\Requests\Subscriber\UpdateSubscriberRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\SubscriberResource;

class SubscribersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search', '');
        $perPage = (int) $request->input('perPage', 10);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        $allowedSortFields = ['id', 'first_name', 'last_name', 'email', 'created_at'];

        if (! in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }

        if (! in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = Subscriber::query()->whereBelongsTo(auth()->user());

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny([
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ], 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $direction);

        $subscribers = $query->paginate($perPage)->withQueryString();

        $subscribers = SubscriberResource::collection($subscribers);

        return Inertia::render('subscribers/Index', [
            'subscribers' => $subscribers,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('subscribers/Create');
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
        return Inertia::modal('subscribers/Edit', [
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
}
