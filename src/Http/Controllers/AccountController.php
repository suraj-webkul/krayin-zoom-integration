<?php

namespace Webkul\ZoomMeeting\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\ZoomMeeting\Repositories\AccountRepository;
use Webkul\ZoomMeeting\Repositories\UserRepository;
use Webkul\ZoomMeeting\Services\Zoom as ZoomService;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ZoomService $zoomService,
        protected UserRepository $userRepository,
        protected AccountRepository $accountRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $account = $this->accountRepository->findOneByField('user_id', auth()->user()->id);

        return view('zoom_meeting::zoom.index', compact('account'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        if (! request()->has('code')) {
            return redirect($this->zoomService->createAuthUrl());
        }

        $token = $this->zoomService->getAccessToken(request()->get('code'));

        $account = $this->zoomService->getUserInfo($token);

        $this->userRepository->find(auth()->user()->id)->accounts()->updateOrCreate(
            [
                'zoom_id' => $account['account_id'],
            ],
            [
                'name'   => $account['email'],
                'token'  => $token,
            ]
        );

        session()->flash('success', trans('zoom_meeting::app.zoom.index.create-success'));

        return redirect()->route('admin.zoom_meeting.index');
    }

    /**
     * Create zoom meeting link
     */
    public function createLink(): JsonResponse
    {
        $account = $this->accountRepository->findOneByField('user_id', auth()->user()->id);

        $meeting = $this->zoomService->createMeeting($account, request()->all());

        if (is_string($meeting)) {
            return response()->json([
                'message' => $meeting,
            ], 401);
        }

        return response()->json([
            'link'    => $meeting->join_url,
            'comment' => trans('zoom_meeting::app.zoom.index.link-shared', [
                'password' => $meeting->password,
                'link'     => $meeting->join_url,
            ]),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->accountRepository->destroy($id);

        session()->flash('success', trans('zoom_meeting::app.zoom.index.destroy-success'));

        return redirect()->back();
    }
}
