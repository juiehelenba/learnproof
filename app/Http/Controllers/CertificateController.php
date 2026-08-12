<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\BlockchainAnchorService;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(
        private BlockchainAnchorService $blockchain,
    ) {}

    public function show(Certificate $certificate): View
    {
        $this->authorizeOwner($certificate);

        $certificate->load(['user', 'course']);

        return view('certificates.show', [
            'certificate' => $certificate,
            'verified' => $this->blockchain->verifyOnChain($certificate),
        ]);
    }

    public function verify(Certificate $certificate): View
    {
        $certificate->load(['user', 'course']);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'verified' => $this->blockchain->verifyOnChain($certificate),
        ]);
    }

    private function authorizeOwner(Certificate $certificate): void
    {
        if (auth()->id() !== $certificate->user_id) {
            abort(403);
        }
    }
}
