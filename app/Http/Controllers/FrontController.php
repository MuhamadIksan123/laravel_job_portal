<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplyJobRequest;
use App\Models\Category;
use App\Models\CompanyJob;
use App\Models\JobCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $jobs = CompanyJob::with(['category', 'company'])->latest()->take(6)->get();
        return view('front.index', compact('categories', 'jobs'));
    }

    public function details(CompanyJob $companyJob)
    {
        $jobs = CompanyJob::with(['category', 'company'])
            ->where('id', '!=', $companyJob->id)
            ->InRandomOrder()
            ->take(4)
            ->get();
        return view('front.details', compact('companyJob', 'jobs'));
    }

    public function category(Category $category)
    {
        $jobs = CompanyJob::with(['category', 'company'])->where('category_id', $category->id)->get();
        return view('front.category', compact('jobs', 'category'));
    }

    public function apply(CompanyJob $companyJob)
    {
        return view('front.apply', compact('companyJob'));
    }

    public function apply_store(StoreApplyJobRequest $request, CompanyJob $companyJob)
    {
        DB::transaction(function () use ($request, $companyJob) {
            $validated = $request->validated();
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->store('resume/' . date('Y/m/d'), 'public');
                $validated['resume'] = $resumePath;
            }

            $validated['is_hired'] = false;
            $validated['candidate_id'] = Auth::user()->id;
            $validated['company_job_id'] = $companyJob->id;

            JobCandidate::create($validated);
        });
        return redirect()->route('front.apply.success');
    }

    public function success_apply() {
        return view('front.success');
    }

    public function search(Request $request) {
        $request->validate([
            'keyword' => ['required', 'string', 'max:255']
        ]);
        $keyword = $request->keyword;
        $jobs = CompanyJob::with(['category', 'company'])->where('name', 'like', '%' .  $keyword . '%')->paginate(6);
        return view('front.search', compact('jobs', 'keyword'));
    }
}
