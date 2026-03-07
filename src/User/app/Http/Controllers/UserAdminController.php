<?php

namespace Modules\User\App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserIdentifierDto;
use Modules\User\App\Dtos\UserUpdateDto;
use Modules\User\App\Services\UserAdminService;
use App\Http\Controllers\Controller as BaseController;

class UserAdminController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @var \Modules\User\App\Services\UserAdminService
     */
    protected UserAdminService $userAdminService;

    /**
     * @param \Modules\User\App\Services\UserAdminService $userAdminService
     * @return void
     */
    public function __construct(UserAdminService $userAdminService)
    {
        $this->userAdminService = $userAdminService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", User::class);

        return response()->json($this->userAdminService->all(), 200);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::findOrFail($identifier->id);
        $this->authorize("view", $user);

        return response()->json($this->userAdminService->get($identifier), 200);
    }

    /**
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserDto $userData): JsonResponse
    {
        $this->authorize("create", User::class);

        return response()->json($this->userAdminService->create($userData), 201);
    }

    /**
     * @param \Modules\User\App\Dtos\UserUpdateDto $userData
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateDto $userData): JsonResponse
    {
        $user = User::findOrFail($userData->id);
        $this->authorize("update", $user);

        return response()->json($this->userAdminService->update($userData), 200);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::findOrFail($identifier->id);
        $this->authorize("verify", $user);

        return response()->json($this->userAdminService->verify($identifier), 200);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::findOrFail($identifier->id);
        $this->authorize("delete", $user);

        return response()->json($this->userAdminService->delete($identifier), 200);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(UserIdentifierDto $identifier): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($identifier->id);
        $this->authorize("restore", $user);

        return response()->json($this->userAdminService->restore($identifier), 200);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize("import", User::class);

        $request->validate([
            "file" => [ "required", "file", "mimes:csv,txt,xls,xlsx", ],
        ]);

        $file = $request->file("file");
        $path = $file->store("imports");

        return response()->json(
            $this->userAdminService->import(
                Storage::path($path),
                $file->getClientOriginalName(),
            ),
            200
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize("export", User::class);

        $type = (string) ($request->query("export_type") ?? $request->query("type", "csv"));

        return response()->json(
            $this->userAdminService->export($type),
            200
        );
    }
}
