<?php

use App\Models\Subscriber;
use App\Models\User;

test('guests cannot access subscribers index', function () {
    $response = $this->get(route('subscribers.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view their subscribers', function () {
    $user = User::factory()->create();
    Subscriber::factory()->count(5)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index'));
    $response->assertSuccessful();
});

test('users can only see their own subscribers', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1Subscribers = Subscriber::factory()->count(3)->create(['user_id' => $user1->id]);
    $user2Subscribers = Subscriber::factory()->count(2)->create(['user_id' => $user2->id]);

    $this->actingAs($user1);

    $response = $this->get(route('subscribers.index'));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect(count($responseData))->toBe(3);
    expect(collect($responseData)->pluck('id')->sort()->values()->all())
        ->toBe($user1Subscribers->pluck('id')->sort()->values()->all());
});

test('subscribers can be searched by first name', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['filter' => ['search' => 'John']]));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect(count($responseData))->toBe(1);
    expect($responseData[0]['id'])->toBe($subscriber1->id);
});

test('subscribers can be searched by last name', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['filter' => ['search' => 'Smith']]));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect(count($responseData))->toBe(1);
    expect($responseData[0]['id'])->toBe($subscriber2->id);
});

test('subscribers can be searched by email', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['filter' => ['search' => 'jane@example']]));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect(count($responseData))->toBe(1);
    expect($responseData[0]['id'])->toBe($subscriber2->id);
});

test('subscribers can be sorted by first name ascending', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Charlie',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Alice',
    ]);

    $subscriber3 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Bob',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['sort' => 'first_name']));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect($responseData[0]['first_name'])->toBe('Alice');
    expect($responseData[1]['first_name'])->toBe('Bob');
    expect($responseData[2]['first_name'])->toBe('Charlie');
});

test('subscribers can be sorted by first name descending', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Charlie',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Alice',
    ]);

    $subscriber3 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'first_name' => 'Bob',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['sort' => '-first_name']));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect($responseData[0]['first_name'])->toBe('Charlie');
    expect($responseData[1]['first_name'])->toBe('Bob');
    expect($responseData[2]['first_name'])->toBe('Alice');
});

test('subscribers can be sorted by email ascending', function () {
    $user = User::factory()->create();

    $subscriber1 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'email' => 'charlie@example.com',
    ]);

    $subscriber2 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'email' => 'alice@example.com',
    ]);

    $subscriber3 = Subscriber::factory()->create([
        'user_id' => $user->id,
        'email' => 'bob@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['sort' => 'email']));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers']['data'];

    expect($responseData[0]['email'])->toBe('alice@example.com');
    expect($responseData[1]['email'])->toBe('bob@example.com');
    expect($responseData[2]['email'])->toBe('charlie@example.com');
});

test('subscribers pagination works correctly', function () {
    $user = User::factory()->create();
    Subscriber::factory()->count(25)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('subscribers.index', ['perPage' => 10]));

    $response->assertSuccessful();

    $responseData = $response->viewData('page')['props']['subscribers'];

    expect(count($responseData['data']))->toBe(10);
    expect($responseData['meta']['total'])->toBe(25);
    expect($responseData['meta']['last_page'])->toBe(3);
});
