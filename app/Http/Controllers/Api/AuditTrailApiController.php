<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuditTrailService;

class AuditTrailApiController extends Controller
{
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * Record a button click event via AJAX
     */
    public function recordClick(Request $request)
    {
        $request->validate([
            'button_id' => 'required|string|max:100',
            'action_name' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
        ]);

        $record = $this->auditTrailService->recordButtonClick(
            $request->button_id,
            $request->action_name,
            $request
        );

        return response()->json([
            'success' => $record !== null,
            'message' => $record ? 'Click recorded' : 'Failed to record click',
        ]);
    }

    /**
     * Record a form submission event via AJAX
     */
    public function recordFormSubmit(Request $request)
    {
        $request->validate([
            'form_name' => 'required|string|max:255',
            'success' => 'required|boolean',
        ]);

        $record = $this->auditTrailService->recordFormSubmission(
            $request->form_name,
            $request->boolean('success'),
            $request,
            $request->except(['form_name', 'success', '_token'])
        );

        return response()->json([
            'success' => $record !== null,
            'message' => $record ? 'Form submission recorded' : 'Failed to record form submission',
        ]);
    }
}
