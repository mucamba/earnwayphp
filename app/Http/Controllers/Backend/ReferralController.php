<?php

namespace App\Http\Controllers\Backend;

use App\Models\Reward;
use App\Models\Setting;
use Illuminate\Http\Request;

class ReferralController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|store|edit|update|statusUpdate|destroy' => 'referral-manage',
        ];
    }

    public function index()
    {
        $referralRewards = Reward::all()->groupBy('type');

        return view('backend.referral.index', compact('referralRewards'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'reward' => 'required',
            'type'   => 'required',
        ]);

        $level = Reward::where('type', $validated['type'])->max('level') + 1;

        Reward::create([
            'percentage' => $validated['reward'],
            'type'       => $validated['type'],
            'level'      => $level,
        ]);
        notifyEvs('success', 'Reward created successfully');

        return redirect()->route('admin.referral.index');
    }

    public function statusUpdate($type, $status)
    {
        $key     = $type.'_rewards';
        $section = 'hide_settings';
        Setting::add($key, $status, Setting::getDataType($key, $section));

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);

        return view('backend.referral.edit', compact('reward'))->render();
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'reward' => 'required',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update([
            'percentage' => $validated['reward'],
        ]);
        notifyEvs('success', 'Reward updated successfully');

        return redirect()->route('admin.referral.index');

    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);

        // Fetch all rewards of the same type except the one being deleted
        $otherRewards = Reward::where('type', $reward->type)
            ->where('id', '!=', $reward->id)
            ->orderBy('level')
            ->get();

        // Rearrange the levels to maintain consecutive order
        foreach ($otherRewards as $index => $otherReward) {
            $otherReward->update(['level' => $index + 1]);
        }

        // Delete the selected reward
        $reward->delete();

        notifyEvs('success', 'Reward deleted and levels rearranged successfully');

        return redirect()->route('admin.referral.index');
    }
}
