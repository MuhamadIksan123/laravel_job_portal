<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreCompanyJobRequest;
use App\Http\Requests\UpdateCompanyJobRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyJob;
use App\Models\JobCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyJobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $my_company = Company::where('employer_id', $user->id)->first();
        if ($my_company) {
            $company_job = CompanyJob::where('company_id', $my_company->id)->paginate(10);
        } else {
            $company_job = collect();
        }
        return view('admin.company_jobs.index', compact('company_job'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $my_company = Company::where('employer_id', $user->id)->first();
        if (!$my_company) {
            return view('admin.company.create');
        }
        $categories = Category::all();
        return view('admin.company_jobs.create', compact('categories', 'my_company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyJobRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnail/' . date('Y/m/d'), 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_open'] = true;

            $newData = CompanyJob::create($validated);

            if(!empty($validated['responsibilities'])) {
                foreach($validated['responsibilities'] as $responsibility) {
                    $newData->responsibilities()->create([
                        'name' => $responsibility
                    ]);
                }
            }

            if (!empty($validated['qualifications'])) {
                foreach ($validated['qualifications'] as $qualification) {
                    $newData->qualifications()->create([
                        'name' => $qualification
                    ]);
                }
            }
        });

        return redirect()->route('admin.company_jobs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanyJob $companyJob)
    {
        $jobCandidate = JobCandidate::where('company_job_id', $companyJob->id)->paginate(10);
        return view('admin.company_jobs.show', compact('companyJob', 'jobCandidate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyJob $companyJob)
    {
        $companyJob->load(['responsibilities', 'qualifications']);
        $categories = Category::all();
        return view('admin.company_jobs.edit', compact('companyJob', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyJobRequest $request, CompanyJob $companyJob)
    {
        DB::transaction(function () use ($request, $companyJob) {
            $validated = $request->validated();
            Log::info($validated);
            // Proses file thumbnail jika ada
            if ($request->hasFile('thumbnail')) {
                // Hapus thumbnail lama jika ada
                if ($companyJob->thumbnail && Storage::disk('public')->exists($companyJob->thumbnail)) {
                    Storage::disk('public')->delete($companyJob->thumbnail);
                }

                // Simpan thumbnail baru
                $thumbnailPath = $request->file('thumbnail')->store('thumbnail/' . date('Y/m/d'), 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            // Update data CompanyJob
            $validated['slug'] = Str::slug($validated['name']);
            $companyJob->update($validated);

            // Update responsibilities
            $newResponsibilities = $validated['responsibilities'] ?? [];

            // Hapus responsibilities yang tidak ada di input
            $companyJob->responsibilities()->whereNotIn('name', $newResponsibilities)->delete();

            // Tambah atau update responsibilities
            foreach ($newResponsibilities as $responsibility) {
                $companyJob->responsibilities()->updateOrCreate(
                    ['name' => $responsibility], // Kondisi pencarian
                    ['name' => $responsibility] // Data yang diupdate atau ditambahkan
                );
            }

            // Update qualifications
            $newQualifications = $validated['qualifications'] ?? [];

            // Hapus qualifications yang tidak ada di input
            $companyJob->qualifications()->whereNotIn('name', $newQualifications)->delete();

            // Tambah atau update qualifications
            foreach ($newQualifications as $qualification) {
                $companyJob->qualifications()->updateOrCreate(
                    ['name' => $qualification], // Kondisi pencarian
                    ['name' => $qualification] // Data yang diupdate atau ditambahkan
                );
            }
        });

        return redirect()->route('admin.company_jobs.index');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyJob $companyJob)
    {
        DB::transaction(function () use ($companyJob) {
            foreach ($$companyJob->responsibilities as $key => $responsibility) {
                $responsibility->delete();
            }

            foreach ($$companyJob->qualifications as $key => $qualification) {
                $qualification->delete();
            }

            foreach ($$companyJob->candidates as $key => $candidate) {
                $candidate->delete();
            }

            $companyJob->delete();
        });

        return redirect()->route('admin.company_jobs.index');
    }
}
