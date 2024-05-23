<?php

namespace App\Http\Controllers\Setting\Menu;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tripteki\SettingMenu\Contracts\Repository\ISettingMenuRepository;
use App\Http\Requests\Settings\Menus\MenuStoreValidation;
use App\Http\Requests\Settings\Menus\MenuUpdateValidation;
use App\Http\Requests\Settings\Menus\MenuDestroyValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * @var \Tripteki\SettingMenu\Contracts\Repository\ISettingMenuRepository
     */
    protected $menuRepository;

    /**
     * @param \Tripteki\SettingMenu\Contracts\Repository\ISettingMenuRepository $menuRepository
     * @return void
     */
    public function __construct(ISettingMenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @OA\Get(
     *      path="/menus",
     *      tags={"Menus"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = [];
        $statecode = 200;

        $this->menuRepository->setUser($request->user());

        $data = $this->menuRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/menus",
     *      tags={"Menus"},
     *      summary="Store",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="bar",
     *                      type="string",
     *                      schema={"type": "string", "enum": {"headernavbar", "sidenavbar"}},
     *                      description="Menu's Bar."
     *                  ),
     *                  @OA\Property(
     *                      property="menu",
     *                      type="string",
     *                      description="Menu's Menu."
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \App\Http\Requests\Settings\Menus\MenuStoreValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuStoreValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->menuRepository->setUser($request->user());

        if ($this->menuRepository->getUser()) {

            $data = $this->menuRepository->move(null, $form["menu"], $form["bar"]);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/menus",
     *      tags={"Menus"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="bar",
     *                      type="string",
     *                      schema={"type": "string", "enum": {"headernavbar", "sidenavbar"}},
     *                      description="Menu's Bar."
     *                  ),
     *                  @OA\Property(
     *                      property="menu_from",
     *                      type="string",
     *                      description="Menu's Menu From."
     *                  ),
     *                  @OA\Property(
     *                      property="menu_to",
     *                      type="string",
     *                      description="Menu's Menu To."
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Settings\Menus\MenuUpdateValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuUpdateValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->menuRepository->setUser($request->user());

        if ($this->menuRepository->getUser()) {

            $data = $this->menuRepository->move($form["menu_from"], $form["menu_to"], $form["bar"]);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/menus/{bar}/{menu}",
     *      tags={"Menus"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="bar",
     *          schema={"type": "string", "enum": {"headernavbar", "sidenavbar"}},
     *          description="Menu's Bar."
     *      ),
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="menu",
     *          description="Menu's Menu."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Settings\Menus\MenuDestroyValidation $request
     * @param string $bar
     * @param string $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(MenuDestroyValidation $request, $bar, $menu)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->menuRepository->setUser($request->user());

        if ($this->menuRepository->getUser()) {

            $data = $this->menuRepository->move($menu, null, $bar);

            if ($data) {

                $statecode = 200;
            }
        }

        return iresponse($data, $statecode);
    }
};
