<?php

namespace App\Http\Controllers\Web;

use App\DataTables\LandingPagesDataTable;
use App\Http\Requests\LandingPage\CreateRequest;
use App\Http\Requests\LandingPage\UpdateRequest;
use App\Models\Clinic;
use App\Models\LandingPageDoctor;
use App\Models\LandingPageGallery;
use App\Models\LandingPageTestimonial;
use App\Services\LandingPageService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingPageController extends WebController
{
    protected $dbObject;

    public function __construct(public LandingPageService $landingPageService)
    {
        $this->dbObject = DB::class;
        $this->middleware(['permission:landing-page-list'],   ['only' => ['index']]);
        $this->middleware(['permission:landing-page-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:landing-page-edit'],   ['only' => ['edit', 'update']]);
        $this->middleware(['permission:landing-page-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:landing-page-show'],   ['only' => ['show']]);
    }

    /**
     * Render landing pages listing with server-side datatable.
     */
    public function index(LandingPagesDataTable $dataTable)
    {
        return $dataTable->render('landing-pages.index');
    }

    /**
     * Show create landing page form.
     */
    public function create()
    {
        $clinics = Clinic::select('id', 'name', 'email', 'phone_no', 'address', 'city', 'state', 'postal_code', 'consultation_fee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Build a JS-ready map of clinic data for auto-fill
        $clinicsJson = $clinics->mapWithKeys(fn($c) => [$c->id => [
            'name'             => $c->name,
            'email'            => $c->email ?? '',
            'phone'            => $c->phone_no ?? '',
            'address'          => strip_tags($c->address ?? ''),
            'consultation_fee' => $c->consultation_fee ?? '',
            'city'             => $c->city ?? '',
            'state'            => $c->state ?? '',
        ]])->toJson();

        return view('landing-pages.create', compact('clinics', 'clinicsJson'));
    }

    /**
     * Store a new landing page with nested doctors, testimonials, gallery.
     */
    public function store(CreateRequest $request)
    {
        try {
            $data = $this->landingPageService->getDataFromRequest($request);
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
            $data['is_appointment_enabled'] = $request->boolean('is_appointment_enabled', true);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage($request->file('logo'), 'landing-page-logos');
            }

            // Handle hero background media upload (image or video)
            if ($request->hasFile('hero_media')) {
                $file = $request->file('hero_media');
                $mime = $file->getMimeType();
                $data['hero_media_type'] = str_starts_with($mime, 'video/') ? 'video' : 'image';
                $data['hero_media'] = $this->uploadImage($file, 'landing-page-hero');
            } elseif ($request->filled('hero_media_type')) {
                $data['hero_media_type'] = $request->hero_media_type;
            }

            $this->dbObject::beginTransaction();

            $landingPage = $this->landingPageService->createData($data);

            // Save nested doctors
            $this->saveDoctors($landingPage, $request);

            // Save nested testimonials
            $this->saveTestimonials($landingPage, $request);

            // Save gallery images
            $this->saveGallery($landingPage, $request);

            $this->dbObject::commit();

            return $this->successResponse('admin.landing-pages.index', trans('app.data_created', ['action' => 'Landing Page']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();
            return $this->errorResponse($exception);
        }
    }

    /**
     * Show admin detail view of a landing page.
     */
    public function show(string $id)
    {
        $realId      = SecureRouteParameter::decodeOrFail($id);
        $landingPage = $this->landingPageService->getDataById($realId);
        $landingPage->load(['doctors', 'testimonials', 'gallery']);

        return view('landing-pages.show', compact('landingPage'));
    }

    /**
     * Show edit form for a landing page.
     */
    public function edit(string $id)
    {
        $realId      = SecureRouteParameter::decodeOrFail($id);
        $landingPage = $this->landingPageService->getDataById($realId);
        $landingPage->load(['doctors', 'testimonials', 'gallery']);

        $clinics = Clinic::select('id', 'name', 'email', 'phone_no', 'address', 'city', 'state', 'postal_code', 'consultation_fee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $clinicsJson = $clinics->mapWithKeys(fn($c) => [$c->id => [
            'name'             => $c->name,
            'email'            => $c->email ?? '',
            'phone'            => $c->phone_no ?? '',
            'address'          => strip_tags($c->address ?? ''),
            'consultation_fee' => $c->consultation_fee ?? '',
            'city'             => $c->city ?? '',
            'state'            => $c->state ?? '',
        ]])->toJson();

        return view('landing-pages.edit', compact('landingPage', 'clinics', 'clinicsJson'));
    }

    /**
     * Update landing page with nested data.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            $realId = SecureRouteParameter::decodeOrFail($id);
            $data   = $this->landingPageService->getDataFromRequest($request);
            $data['updated_by'] = auth()->id();
            $data['is_appointment_enabled'] = $request->boolean('is_appointment_enabled', true);

            $landingPage = $this->landingPageService->getDataById($realId);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($landingPage->logo) {
                    $this->deleteImage('landing-page-logos', $landingPage->logo);
                }
                $data['logo'] = $this->uploadImage($request->file('logo'), 'landing-page-logos');
            }

            // Handle hero background media upload (image or video)
            if ($request->hasFile('hero_media')) {
                if ($landingPage->hero_media) {
                    $this->deleteImage('landing-page-hero', $landingPage->hero_media);
                }
                $file = $request->file('hero_media');
                $mime = $file->getMimeType();
                $data['hero_media_type'] = str_starts_with($mime, 'video/') ? 'video' : 'image';
                $data['hero_media'] = $this->uploadImage($file, 'landing-page-hero');
            } elseif ($request->input('remove_hero_media') == '1') {
                if ($landingPage->hero_media) {
                    $this->deleteImage('landing-page-hero', $landingPage->hero_media);
                }
                $data['hero_media'] = null;
                $data['hero_media_type'] = 'color';
            }

            $this->dbObject::beginTransaction();

            $this->landingPageService->updateData($realId, $data);
            $landingPage->refresh();

            // Replace nested doctors (delete existing, insert new)
            $this->deleteOldDoctorPhotos($landingPage);
            $landingPage->doctors()->delete();
            $this->saveDoctors($landingPage, $request);

            // Replace nested testimonials
            $landingPage->testimonials()->delete();
            $this->saveTestimonials($landingPage, $request);

            // Gallery: keep existing (not re-uploaded), add new uploads
            // Note: existing gallery images are kept; new uploads are appended
            // Deletions of individual gallery items happen via separate JS call (handled below)
            $this->saveGallery($landingPage, $request);

            // Remove gallery items checked for deletion
            if ($request->filled('delete_gallery_ids')) {
                $deleteIds = array_filter(explode(',', $request->input('delete_gallery_ids')));
                foreach ($deleteIds as $gid) {
                    $galleryItem = LandingPageGallery::find((int)$gid);
                    if ($galleryItem && $galleryItem->landing_page_id == $landingPage->id) {
                        $this->deleteImage('landing-page-gallery', $galleryItem->image);
                        $galleryItem->delete();
                    }
                }
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.landing-pages.index', trans('app.data_updated', ['action' => 'Landing Page']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();
            return $this->errorResponse($exception);
        }
    }

    /**
     * Soft-delete a landing page and clean up files.
     */
    public function destroy(string $id)
    {
        try {
            $realId      = SecureRouteParameter::decodeOrFail($id);
            $landingPage = $this->landingPageService->getDataById($realId);
            $landingPage->load(['doctors', 'gallery']);

            // Delete doctor photos
            $this->deleteOldDoctorPhotos($landingPage);

            // Delete gallery images
            foreach ($landingPage->gallery as $item) {
                $this->deleteImage('landing-page-gallery', $item->image);
            }

            // Delete logo
            if ($landingPage->logo) {
                $this->deleteImage('landing-page-logos', $landingPage->logo);
            }

            // Delete hero media
            if ($landingPage->hero_media) {
                $this->deleteImage('landing-page-hero', $landingPage->hero_media);
            }

            $this->landingPageService->deleteDataById($realId);

            return $this->successResponse('admin.landing-pages.index', trans('app.data_deleted', ['action' => 'Landing Page']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function saveDoctors($landingPage, $request): void
    {
        $doctors = $request->input('doctors', []);
        foreach ($doctors as $index => $docData) {
            if (empty($docData['doctor_name'])) continue;

            $photo = null;
            if ($request->hasFile("doctors.{$index}.photo")) {
                $photo = $this->uploadImage($request->file("doctors.{$index}.photo"), 'landing-page-doctors');
            }

            LandingPageDoctor::create([
                'landing_page_id'  => $landingPage->id,
                'doctor_name'      => $docData['doctor_name'],
                'email'            => $docData['email'] ?? null,
                'specialization'   => $docData['specialization'] ?? null,
                'consultation_fee' => $docData['consultation_fee'] ?? null,
                'experience'       => $docData['experience'] ?? null,
                'photo'            => $photo,
                'sort_order'       => $index,
            ]);
        }
    }

    private function saveTestimonials($landingPage, $request): void
    {
        $testimonials = $request->input('testimonials', []);
        foreach ($testimonials as $index => $item) {
            if (empty($item['patient_name'])) continue;

            LandingPageTestimonial::create([
                'landing_page_id' => $landingPage->id,
                'patient_name'    => $item['patient_name'],
                'rating'          => $item['rating'] ?? 5,
                'story'           => $item['story'] ?? null,
                'sort_order'      => $index,
            ]);
        }
    }

    private function saveGallery($landingPage, $request): void
    {
        if (!$request->hasFile('gallery_images')) return;

        $existingCount = $landingPage->gallery()->count();
        foreach ($request->file('gallery_images') as $index => $file) {
            if (!$file) continue;
            $path = $this->uploadImage($file, 'landing-page-gallery');
            LandingPageGallery::create([
                'landing_page_id' => $landingPage->id,
                'image'           => $path,
                'sort_order'      => $existingCount + $index,
            ]);
        }
    }

    private function deleteOldDoctorPhotos($landingPage): void
    {
        foreach ($landingPage->doctors as $doc) {
            if ($doc->photo) {
                $this->deleteImage('landing-page-doctors', $doc->photo);
            }
        }
    }

    /**
     * Upload an image file and return just the filename.
     */
    private function uploadImage($file, string $folder): string
    {
        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($folder), $filename);
        return $filename;
    }

    /**
     * Delete an image from the public folder.
     */
    private function deleteImage(string $folder, string $filename): void
    {
        $path = public_path($folder . '/' . $filename);
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
