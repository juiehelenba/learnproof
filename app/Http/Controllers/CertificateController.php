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
            'explorerUrl' => $certificate->explorerTxUrl(),
            'blockchainPending' => $certificate->isBlockchainPending(),
        ]);
    }

    public function verify(Certificate $certificate): View
    {
        // Rota pública: carrega apenas as colunas que a página realmente exibe.
        $certificate->load(['user:id,name', 'course:id,title']);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'verified' => $this->blockchain->verifyOnChain($certificate),
            'simulated' => $certificate->isSimulatedAnchor(),
            'explorerUrl' => $certificate->explorerTxUrl(),
            'blockchainPending' => $certificate->isBlockchainPending(),
        ]);
    }

    private function authorizeOwner(Certificate $certificate): void
    {
        if (auth()->id() !== $certificate->user_id) {
            abort(403);
        }
    }
}
