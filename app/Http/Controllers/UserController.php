<?php
namespace App\Http\Controllers;

use App\Jobs\AdminAdded;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use InfluencerMicroservices\UserService;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $this->userService->allows('view', 'users');
        return $this->userService->all($request->input('page', 1));
    }

    public function store(UserCreateRequest $request)
    {
        $this->userService->allows('edit', 'users');

        $data = $request->only('first_name', 'last_name', 'email', 'is_influencer') +
                    ['password' => 'password'];

        $user = $this->userService->create($data);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);
        AdminAdded::dispatch($user->email)->onQueue('emails_queue');
        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    public function show($user)
    {
        $this->userService->allows('view', 'users');

        $user = $this->userService->get($user);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, $user)
    {
        $this->userService->allows('edit', 'users');
        // dd($user);
        $user = $this->userService->update($user, $request->only('first_name', 'last_name', 'email'));

        UserRole::where('user_id', $user->id)->delete();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user), 202);
    }

    public function destroy($user)
    {
        $this->userService->allows('edit', 'users');
        $this->userService->delete($user);

        // dd($user);

        return response(null, HttpResponse::HTTP_NO_CONTENT);
    }
}
