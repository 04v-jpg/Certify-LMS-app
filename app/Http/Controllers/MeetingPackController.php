<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingPack;
use App\Http\Requests\MeetingPack\StoreRequest;
use App\Http\Requests\MeetingPack\UpdateRequest;
use App\UseCases\MeetingPack\IndexAction;
use App\UseCases\MeetingPack\StoreAction;
use App\UseCases\MeetingPack\UpdateAction;
use App\UseCases\MeetingPack\DestroyAction;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\UseCases\MeetingPack\PublishAction;
use App\UseCases\MeetingPack\ArchiveAction;
use App\UseCases\MeetingPack\UnArchiveAction;

class MeetingPackController extends Controller
{
    /**
     * 面談パックマスタの管理　Controller。
     * 面談パックマスタの CRUD を担う。
     */
    public function index(Request $request,IndexAction $action): View
    {
        $this->authorize('viewAny', MeetingPack::class);

        $filters = $request->only([
            'keyword',
            'status',
        ]);

        return view('meeting-pack.management.index', [
        'plans'   => $action($filters),
        'keyword' => $filters['keyword'] ?? null,
        'status'  => $filters['status'] ?? null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MeetingPack::class);

        return view('meeting-pack.management.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request,StoreAction $action): RedirectResponse
    {
        $this->authorize('create', MeetingPack::class);

        $action($request->validated());

        return redirect()->route('admin.meeting-packs.index')
            ->with('success', '面談パックを作成しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(MeetingPack $plan)
    {
        $this->authorize('view', $plan);

        return view('meeting-pack.management.show', [
            'plan' => $plan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeetingPack $plan)
    {
        $this->authorize('update', $plan);

        return view('meeting-pack.management.edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, MeetingPack $plan,UpdateAction $action): RedirectResponse
    {
        $this->authorize('update', $plan);

        $action($plan, $request->validated());

        return redirect()->route('admin.meeting-packs.index')
            ->with('success', '面談パックを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeetingPack $plan, DestroyAction $action): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $action($plan);

        return redirect()->route('admin.meeting-packs.index')
            ->with('success', '面談パックを削除しました。');
    }

    public function publish(MeetingPack $plan, PublishAction $action): RedirectResponse
    {
        $this->authorize('publish', $plan);

        $action($plan);

        return redirect()->route('admin.meeting-packs.show', $plan)
            ->with('success', '面談パックを公開しました。');
    }

    public function archive(MeetingPack $plan, ArchiveAction $action): RedirectResponse
    {
        $this->authorize('archive', $plan);

        $action($plan);

        return redirect()->route('admin.meeting-packs.show', $plan)
            ->with('success', '面談パックを下書きに戻しました。');
    }

    public function unarchive(MeetingPack $plan, UnArchiveAction $action): RedirectResponse
    {
        $this->authorize('unarchive', $plan);

        $action($plan);

        return redirect()->route('admin.meeting-packs.show', $plan)
            ->with('success', '面談パックを復元しました。');
    }
}
