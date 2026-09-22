<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\TeamInitiativeGuide;
use Illuminate\View\View;

class TeamGuideController extends Controller
{
    public function __invoke(TeamInitiativeGuide $guide): View
    {
        return view('instructor.team.index', [
            'guide' => $guide->forPanel(),
        ]);
    }
}
